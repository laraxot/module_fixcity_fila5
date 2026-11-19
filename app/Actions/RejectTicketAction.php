<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Moderazione: scarta senza chiudere la pratica civica.
 * Registra `rejected` (AuthorOnly); lo stato resta non pubblico (pending/draft).
 * Non usa `closed`: la chiusura richiede `resolved` (invariante prodotto).
 */
final class RejectTicketAction
{
    use QueueableAction;

    public function execute(Ticket $ticket, string $reason): void
    {
        Gate::authorize('changeStatus', $ticket);

        $reason = trim($reason);
        if ($reason === '') {
            throw new \InvalidArgumentException('La motivazione di scarto è obbligatoria.');
        }

        $actorId = auth()->id();
        $userId = is_int($actorId) || is_string($actorId) ? $actorId : null;

        // Stato riletto con la riga bloccata (DB-07): uno scarto non puo' piu' riportare a pending un ticket appena passato in lavorazione.
        app(LockTicketForStatusChangeAction::class)->execute($ticket, static function (Ticket $locked) use ($reason, $userId): void {
            $current = TicketStatusEnum::tryFrom($locked->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;

            if (! in_array($current, [TicketStatusEnum::PENDING, TicketStatusEnum::DRAFT, TicketStatusEnum::IN_REVIEW], true)) {
                throw new \DomainException(
                    'Si può scartare solo una segnalazione in bozza, in attesa o in revisione.',
                );
            }

            // Resta pending: non è di competenza pubblica e non è "risolta".
            if ($current !== TicketStatusEnum::PENDING) {
                $locked->setStatus(TicketStatusEnum::PENDING);
            }

            app(RecordTicketActivityAction::class)->execute(
                $locked,
                TicketActivityEventTypeEnum::Rejected,
                $current,
                TicketStatusEnum::PENDING,
                $reason,
                TicketActivityVisibilityEnum::AuthorOnly,
                $userId,
            );
        });
    }
}
