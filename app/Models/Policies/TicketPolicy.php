<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Elenco ticket: oggi aperto a ogni utente autenticato (policy da restringere
     * con permessi Filament quando il backoffice ripristina ACL fine).
     */
    public function viewAny(UserContract $user): bool
    {
        return true;
    }

    /**
     * Dettaglio ticket: oggi aperto; in produzione va legato a ownership/assegnazione.
     */
    public function view(UserContract $user, Ticket $ticket): bool
    {
        return true;
    }

    public function create(UserContract $user): bool
    {
        return true;
    }

    public function update(UserContract $user, Ticket $ticket): bool
    {
        return true;
    }

    public function delete(UserContract $user, Ticket $ticket): bool
    {
        return true;
    }
}
