<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Riga di una segnalazione nelle pagine dell'area personale (DCP-110): solo dati di presentazione, nessuna regola di accesso.
 * Chi la chiama ha gia' filtrato i ticket (proprietario o seguace): qui il codice di tracciamento, che e' un capability token,
 * finisce nei link solo se il ticket e' stato consegnato dalla query autorizzata.
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
    public function execute(Ticket $ticket, string $locale): array
    {
        $status = $ticket->status;
        $statusValue = $status instanceof TicketStatusEnum ? $status->value : SafeStringCastAction::cast($ticket->getRawOriginal('status'));
        $code = is_string($ticket->code) && trim($ticket->code) !== '' ? trim($ticket->code) : null;
        $createdAt = $ticket->created_at;

        return [
            'id' => $ticket->id,
            'title' => $ticket->name,
            'code' => $code,
            'status' => $statusValue,
            'status_label' => $this->statusLabel($statusValue, $locale),
            'is_concluded' => $status instanceof TicketStatusEnum && in_array($status, self::CONCLUDED, true),
            // en_GB: "5 March 2026" come nel resto del sito, invece del "March 5, 2026" americano di `en`
            'date' => $createdAt === null ? '' : $createdAt->toImmutable()->settings(['locale' => $locale === 'en' ? 'en_GB' : $locale])->isoFormat('LL'),
            'date_iso' => $createdAt === null ? '' : $createdAt->format('Y-m-d'),
            'detail_url' => $this->localize('/tickets/'.$ticket->id, $locale),
            'track_url' => $code === null ? null : $this->localize('/tickets/track/'.rawurlencode($code), $locale),
            'track_query_url' => $code === null ? null : $this->localize('/tickets/track?code='.rawurlencode($code), $locale),
        ];
    }

    /** Etichetta nella lingua richiesta; uno stato sconosciuto (dato storico) si mostra com'e', mai come chiave grezza. */
    private function statusLabel(string $statusValue, string $locale): string
    {
        $key = 'fixcity::personal_area.status.'.$statusValue;
        $label = trans($key, [], $locale);

        return is_string($label) && $label !== $key ? $label : $statusValue;
    }

    private function localize(string $path, string $locale): string
    {
        $localized = LaravelLocalization::getLocalizedURL($locale, $path);

        return $localized !== false ? $localized : $path;
    }
}
