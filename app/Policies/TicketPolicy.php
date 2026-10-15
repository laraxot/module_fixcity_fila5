<?php

declare(strict_types=1);

namespace Modules\Fixcity\Policies;

use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;

final class TicketPolicy extends BasePolicy
{
    public function viewAny(UserContract $user): bool
    {
        return $this->isPaOperator($user) || $user->hasPermissionToOrCreate('ticket.viewAny');
    }

    public function view(UserContract $user, Ticket $ticket): bool
    {
        return $this->isPaOperator($user)
            || $user->hasPermissionToOrCreate('ticket.view')
            || $ticket->isVisibleOnPublicFrontoffice()
            || SafeStringCastAction::cast($ticket->owner_id) === SafeStringCastAction::cast($user->getAuthIdentifier());
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionToOrCreate('ticket.create') || $user->getAuthIdentifier() !== null;
    }

    public function update(UserContract $user, Ticket $ticket): bool
    {
        return $this->isPaOperator($user)
            || $user->hasPermissionToOrCreate('ticket.update')
            || SafeStringCastAction::cast($ticket->owner_id) === SafeStringCastAction::cast($user->getAuthIdentifier());
    }

    public function delete(UserContract $user, Ticket $ticket): bool
    {
        // Deletion is a privileged administrative operation. Being the ticket
        // owner or an ordinary PA operator must not erase the public audit trail.
        return $user->hasRole('admin') || $user->hasPermissionToOrCreate('ticket.delete');
    }

    public function deleteAny(UserContract $user): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionToOrCreate('ticket.delete');
    }

    public function assign(UserContract $user, Ticket $_ticket): bool
    {
        return $this->isPaOperator($user) || $user->hasPermissionToOrCreate('ticket.assign');
    }

    public function changeStatus(UserContract $user, Ticket $_ticket): bool
    {
        return $this->isPaOperator($user) || $user->hasPermissionToOrCreate('ticket.changeStatus');
    }

    public function changePriority(UserContract $user, Ticket $_ticket): bool
    {
        return $this->isPaOperator($user) || $user->hasPermissionToOrCreate('ticket.changePriority');
    }
}
