<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models\Policies;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return Response|bool
     */
    public function viewAny(UserContract $user)
    {
        return true;
        // return $user->can('List tickets');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return Response|bool
     */
    public function view(UserContract $user, Ticket $ticket)
    {
        return true;
        /*
        return $user->can('View ticket')
            && (
                $ticket->owner_id === $user->id
                || $ticket->responsible_id === $user->id
                || $ticket->project->users()->where('users.id', authId())->count()
                || $ticket->project->owner_id === $user->id
            );
        */
    }

    /**
     * Determine whether the user can create models.
     *
     * @return Response|bool
     */
    public function create(UserContract $user)
    {
        return $user->can('Create ticket');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return Response|bool
     */
    public function update(UserContract $user, Ticket $ticket)
    {
        return true;
        /*
        return $user->can('Update ticket')
            && (
                $ticket->owner_id === $user->id
                || $ticket->responsible_id === $user->id
                || $ticket->project->users()->where('users.id', authId())->count()
                || $ticket->project->owner_id === $user->id
            );
        */
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return Response|bool
     */
    public function delete(UserContract $user, Ticket $ticket)
    {
        return $user->can('Delete ticket');
    }
}
