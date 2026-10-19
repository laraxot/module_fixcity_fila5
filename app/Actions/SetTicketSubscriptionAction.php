<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketSubscriber;
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

        return TicketSubscriber::query()->getConnection()->transaction(function () use ($ticket, $userId, $following): bool {
            $subscriber = TicketSubscriber::query()
                ->where('ticket_id', $ticket->getKey())
                ->where('user_id', $userId)
                ->first();

            if ($following && $subscriber === null) {
                TicketSubscriber::query()->create([
                    'ticket_id' => $ticket->getKey(),
                    'user_id' => $userId,
                ]);
            } elseif (! $following && $subscriber !== null) {
                $subscriber->delete();
            }

            return $following;
        });
    }
}
