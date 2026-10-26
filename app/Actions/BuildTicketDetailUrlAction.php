<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

/**
 * Link al dettaglio pubblico di una segnalazione nel front office (`/{lang}/tickets/{id}`), shell del tema `container0.view`.
 * E' il link che si da' a chi NON e' il proprietario (follower): il codice di tracciamento resta una capability del proprietario
 * (DCP-287). Gemella di {@see BuildTicketTrackUrlAction}, che costruisce il link con il codice.
 */
final class BuildTicketDetailUrlAction
{
    use QueueableAction;

    public function execute(int $ticketId): string
    {
        return route('container0.view', [
            'container0' => 'tickets',
            'slug0' => (string) $ticketId,
        ]);
    }
}
