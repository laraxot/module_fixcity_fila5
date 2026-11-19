<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Riga di una segnalazione nelle pagine dell'area personale (DCP-110): solo dati di presentazione, nessuna regola di accesso alla riga.
 * Chi la chiama ha gia' filtrato i ticket (proprietario o seguace). Il codice di tracciamento e' un capability token e permette di
 * leggere la segnalazione anche quando non e' pubblica (DCP-287): finisce nella riga e nei link SOLO se il chiamante dice che il
 * ticket e' del visitatore (`$isOwner`); un follower ha il link al dettaglio e basta. Senza dirlo, il default e' non esporlo.
 *
 * Il tracciamento ha due forme perche' i due elenchi hanno due contratti: `/tickets/track/{code}` (Le mie segnalazioni, la stessa
 * del link della ricevuta) e `/tickets/track?code={code}` (Segnalazioni che seguo). Senza codice (ticket storici) entrambe sono null.
 *
 * @phpstan-type TicketRow array{
 *     id: int,
 *     title: string,
 *     code: string|null,
 *     status: string,
 *     status_label: string,
 *     is_concluded: bool,
 *     is_owner: bool,
 *     date: string,
 *     date_iso: string,
 *     detail_url: string,
 *     track_url: string|null,
 *     track_query_url: string|null
 * }
 */
final class BuildPersonalAreaTicketRowAction
{
    use QueueableAction;

    /** Stati che chiudono il percorso della segnalazione; tutti gli altri (anche la bozza) sono "in corso". */
    private const array CONCLUDED = [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED];

    /**
     * @return TicketRow
     */
    public function execute(Ticket $ticket, string $locale, bool $isOwner = false): array
    {
        $status = $ticket->status;
        $statusValue = $status instanceof TicketStatusEnum ? $status->value : SafeStringCastAction::cast($ticket->getRawOriginal('status'));
        $code = $isOwner && is_string($ticket->code) && trim($ticket->code) !== '' ? trim($ticket->code) : null;
        $createdAt = $ticket->created_at;

        return [
            'id' => $ticket->id,
            'title' => $ticket->name,
            'code' => $code,
            'status' => $statusValue,
            'status_label' => $this->statusLabel($statusValue, $locale),
            'is_concluded' => $status instanceof TicketStatusEnum && in_array($status, self::CONCLUDED, true),
            'is_owner' => $isOwner,
            // en_GB: "5 March 2026" come nel resto del sito, invece del "March 5, 2026" americano di `en`
            'date' => $createdAt === null ? '' : $createdAt->toImmutable()->settings(['locale' => $locale === 'en' ? 'en_GB' : $locale])->isoFormat('LL'),
            'date_iso' => $createdAt === null ? '' : $createdAt->format('Y-m-d'),
            'detail_url' => $this->localize('/tickets/'.$ticket->id, $locale),
            'track_url' => $code === null ? null : $this->localize('/tickets/track/'.rawurlencode($code), $locale),
            'track_query_url' => $code === null ? null : $this->localize('/tickets/track?code='.rawurlencode($code), $locale),
        ];
    }

    /**
     * Etichetta nella lingua richiesta, dalla traduzione dell'enum: la stessa di elenco, dettaglio e notifiche (DCP-287, FO-2-32).
     * Uno stato sconosciuto (dato storico) si mostra com'e', mai come chiave grezza.
     */
    private function statusLabel(string $statusValue, string $locale): string
    {
        $key = 'fixcity::ticket_status_enum.values.'.$statusValue.'.label';
        $label = trans($key, [], $locale);

        return is_string($label) && $label !== $key ? $label : $statusValue;
    }

    private function localize(string $path, string $locale): string
    {
        $localized = LaravelLocalization::getLocalizedURL($locale, $path);

        return $localized !== false ? $localized : $path;
    }
}
