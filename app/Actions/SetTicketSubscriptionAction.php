<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

final class SetTicketSubscriptionAction
{
    use QueueableAction;

    /**
     * Idempotently follow or unfollow a ticket the user is allowed to view.
     */
    public function execute(Ticket $ticket, UserContract $user, bool $following): bool
    {
        Gate::forUser($user)->authorize('view', $ticket);

        $userId = $user->getAuthIdentifier();
        if (! is_int($userId) && (! is_string($userId) || $userId === '')) {
            throw new \InvalidArgumentException('L’utente deve avere un identificativo persistito.');
        }

        return $ticket->getConnection()->transaction(function () use ($ticket, $userId, $following): bool {
            $lockedTicket = $ticket->newQuery()
                ->whereKey($ticket->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $subscribers = $lockedTicket->ticketSubscribers();
            $isFollowing = $subscribers->wherePivot('user_id', $userId)->exists();

            if ($following && ! $isFollowing) {
                $subscribers->attach($userId);
            } elseif (! $following && $isFollowing) {
                $subscribers->detach($userId);
            }

            return $following;
        });
    }
}
