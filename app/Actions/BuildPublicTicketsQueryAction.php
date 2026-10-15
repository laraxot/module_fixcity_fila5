<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Query ticket visibili in frontoffice (lista, mappa, filtri) — STORY-029.
 *
 * Per un utente autenticato la mappa include anche le sue segnalazioni non ancora pubbliche; l'elenco pubblico (DCP-104)
 * passa `includeOwnTickets: false` cosi' mostra a tutti lo stesso insieme e gli URL filtrati sono condivisibili.
 */
final class BuildPublicTicketsQueryAction
{
    use QueueableAction;

    /**
     * @return Builder<Ticket>
     */
    public function execute(bool $includeOwnTickets = true): Builder
    {
        $query = Ticket::query()->whereNotNull('location');

        $currentUserId = auth()->id();
        if ($includeOwnTickets && $currentUserId !== null) {
            $query->where(function (Builder $q) use ($currentUserId): void {
                /** @var list<TicketStatusEnum> $visibleStatuses */
                $visibleStatuses = TicketStatusEnum::canViewByAll();
                $q->whereIn('status', array_map(
                    static fn (TicketStatusEnum $status): string => $status->value,
                    $visibleStatuses,
                ))
                    ->orWhere('owner_id', $currentUserId);
            });
        } else {
            /** @var list<TicketStatusEnum> $visibleStatuses */
            $visibleStatuses = TicketStatusEnum::canViewByAll();
            $query->whereIn('status', array_map(
                static fn (TicketStatusEnum $status): string => $status->value,
                $visibleStatuses,
            ));
        }

        return $query;
    }
}
