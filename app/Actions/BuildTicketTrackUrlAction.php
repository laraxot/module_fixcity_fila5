<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

/**
 * Link di tracciamento nel front office (`/{lang}/tickets/track?code=...`): shell del tema `container0.view`, mai l'id del database.
 * Unico punto per notifica di stato, notifiche del ciclo di vita ed elenco dell'area personale (DCP-284).
 */
final class BuildTicketTrackUrlAction
{
    use QueueableAction;

    public function execute(string $ticketCode): string
    {
        return route('container0.view', [
            'container0' => 'tickets',
            'slug0' => 'track',
        ]).'?code='.rawurlencode($ticketCode);
    }
}
