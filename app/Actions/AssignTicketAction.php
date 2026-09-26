<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

final class AssignTicketAction
{
    use QueueableAction;

    /**
     * Assign a ticket to a PA operator, or remove its assignment.
     */
    public function execute(Ticket $ticket, ?int $responsibleId): Ticket
    {
        $ticket->responsible_id = $responsibleId;
        $ticket->save();

        return $ticket->refresh();
    }
}
