<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Query ticket visibili in frontoffice (lista, mappa, filtri) — STORY-029.
 */
final class BuildPublicTicketsQueryAction
{
    use QueueableAction;

    /**
     * @return Builder<Ticket>
     */
    public function execute(): Builder
    {
        $query = Ticket::query()->whereNotNull('location');

        $currentUserId = auth()->id();
        if ($currentUserId !== null) {
            $query->where(function (Builder $q) use ($currentUserId): void {
                /** @var list<TicketStatusEnum> $visibleStatuses */
                $visibleStatuses = TicketStatusEnum::canViewByAll();
                $q->whereIn('status', array_map(
                    static fn (TicketStatusEnum $status): string => $status->value,
                    $visibleStatuses,
                ))
                    ->orWhere('created_by', $currentUserId)
                    ->orWhere('updated_by', $currentUserId);
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
