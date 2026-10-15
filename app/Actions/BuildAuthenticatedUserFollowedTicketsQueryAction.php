<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/** Query the authenticated citizen's followed tickets. */
final class BuildAuthenticatedUserFollowedTicketsQueryAction
{
    use QueueableAction;

    /** @return Builder<Ticket> */
    public function execute(UserContract $user): Builder
    {
        $visibleStatuses = array_map(
            static fn (TicketStatusEnum $status): string => $status->value,
            TicketStatusEnum::canViewByAll(),
        );

        // Resolve the pivot IDs on the user connection before querying tickets.
        // The two models intentionally live in different databases; a SQL
        // subquery would otherwise run on the ticket connection and cannot
        // see uncommitted pivot rows (and is not portable across DB servers).
        $followedTicketIds = TicketSubscriber::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->pluck('ticket_id');

        return Ticket::query()
            ->whereIn('id', $followedTicketIds)
            ->where(static function (Builder $visibleToCitizen) use ($visibleStatuses, $user): void {
                $visibleToCitizen
                    ->whereIn('status', $visibleStatuses)
                    ->orWhere('owner_id', $user->getAuthIdentifier());
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
