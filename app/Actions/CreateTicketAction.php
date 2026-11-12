<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;
use Modules\Media\Models\Media;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

/**
 * Unico punto di creazione di una segnalazione: wizard del cittadino, widget legacy e back office (DB-10).
 *
 * - Ogni segnalazione riceve un `code` di tracciamento (senza codice il cittadino non riceve notifiche).
 * - Riga del ticket, allegati (`$afterCreate`) e attivita' sono in una sola transazione su `fixcity` e `media` (DB-06):
 *   se un allegato fallisce non resta un ticket a meta'.
 * - `TicketCreatedEvent` parte solo dopo il commit: un rollback non manda la ricevuta. La ricevuta al cittadino e'
 *   una sola, quella del listener (`NotifyTicketReceivedAction`, DB-05).
 */
class CreateTicketAction
{
    use QueueableAction;

    /** Tentativi di inserimento se il codice generato collide con uno esistente (indice unique su `code`, DB-04). */
    private const int CODE_ATTEMPTS = 3;

    /**
     * @param  array<string, mixed>  $data
     * @param  (Closure(Ticket): void)|null  $afterCreate  eseguita nella transazione, dopo l'insert (es. salvataggio degli allegati)
     * @param  bool  $flashConfirmation  false nel back office: la pagina di conferma e' solo del cittadino
     */
    public function execute(array $data, ?string $locale = null, ?Closure $afterCreate = null, bool $flashConfirmation = true): Ticket
    {
        if (! isset($data['owner_id'])) {
            $user = auth()->user();
            if (! $user) {
                throw (new ValidationException(
                    validator([], ['owner_id' => 'required'])
                ))->withMessages(['owner_id' => __('fixcity::ticket_submit.auth_required')]);
            }
            $data['owner_id'] = $user->id;
        }

        $ticket = $this->transaction(function () use ($data, $afterCreate): Ticket {
            $ticket = $this->insert($data);
            $this->recordSubmission($ticket);
            if ($afterCreate instanceof Closure) {
                $afterCreate($ticket);
            }

            return $ticket;
        });

        // Fuori dalla transazione; l'evento e' anche ShouldDispatchAfterCommit se il chiamante ne ha aperta una sua.
        TicketCreatedEvent::dispatch($ticket);
        if ($flashConfirmation) {
            app(BuildTicketConfirmationDataAction::class)->flash($ticket, $locale);
        }

        return $ticket;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    /**
     * Prima voce della cronologia: "segnalazione ricevuta" nello stato iniziale. Senza, il cittadino vedeva come unico
     * aggiornamento "Allegato aggiunto" (la foto caricata insieme alla segnalazione). Visibile all'autore e all'ufficio:
     * il ticket non e' ancora pubblico.
     */
    private function recordSubmission(Ticket $ticket): void
    {
        $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;
        $ownerId = $ticket->owner_id;

        app(RecordTicketActivityAction::class)->execute(
            $ticket,
            TicketActivityEventTypeEnum::StatusChange,
            null,
            $status,
            null,
            TicketActivityVisibilityEnum::AuthorOnly,
            is_int($ownerId) || is_string($ownerId) ? $ownerId : null,
            ['source' => 'submission'],
        );
    }

    private function insert(array $data): Ticket
    {
        $allocate = SafeStringCastAction::cast($data['code'] ?? null) === '';
        $prefix = SafeStringCastAction::cast($data['ticket_prefix'] ?? 'TCK') ?: 'TCK';
        $connection = $this->ticketConnection();

        for ($attempt = 1; ; $attempt++) {
            if ($allocate) {
                $data['code'] = app(AllocateTicketCodeAction::class)->execute($prefix);
            }

            try {
                // transazione annidata = savepoint: una violazione di unicita' non invalida la transazione esterna
                return $connection->transaction(static fn (): Ticket => Ticket::create($data));
            } catch (UniqueConstraintViolationException $exception) {
                if (! $allocate || $attempt >= self::CODE_ATTEMPTS) {
                    throw $exception;
                }
            }
        }
    }

    /**
     * Ticket e media sono su connessioni diverse (`fixcity`, `media`): la transazione le copre entrambe.
     *
     * @param  Closure(): Ticket  $callback
     */
    private function transaction(Closure $callback): Ticket
    {
        $ticketConnection = $this->ticketConnection();
        $mediaConnectionName = (new Media)->getConnectionName();

        if ($mediaConnectionName === null || $mediaConnectionName === $ticketConnection->getName()) {
            return $ticketConnection->transaction($callback);
        }

        // Apertura e commit a mano, in quest'ordine: media aperta per prima e chiusa per prima. Laravel aggancia i callback
        // "dopo il commit" (job delle conversioni, observer afterCommit) all'ultima transazione aperta, cioe' `fixcity`:
        // con il commit di `fixcity` per ultimo partono solo quando anche le righe `media` sono salvate (revisione 2026-11-10).
        $mediaConnection = DB::connection($mediaConnectionName);
        $mediaConnection->beginTransaction();
        try {
            $ticketConnection->beginTransaction();
            try {
                $ticket = $callback();
            } catch (Throwable $exception) {
                $ticketConnection->rollBack();

                throw $exception;
            }
            $mediaConnection->commit();
        } catch (Throwable $exception) {
            $mediaConnection->rollBack();

            throw $exception;
        }

        // Un errore qui lascerebbe righe `media` senza ticket (nessuna ricevuta parte: i callback sono su `fixcity`).
        // E' il guasto meno grave: l'opposto sarebbe un ticket senza allegati con la ricevuta gia' inviata.
        try {
            $ticketConnection->commit();
        } catch (Throwable $exception) {
            $ticketConnection->rollBack();

            throw $exception;
        }

        return $ticket;
    }

    private function ticketConnection(): Connection
    {
        return DB::connection((new Ticket)->getConnectionName());
    }
}
