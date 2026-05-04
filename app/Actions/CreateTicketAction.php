<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Events\TicketCreatedEvent;

class CreateTicketAction
{
    /**
     * Executes the ticket creation logic.
     *
     * @param array<string, mixed> $data
     * @return Ticket
     */
    public function execute(array $data): Ticket
    {
        if (! isset($data['owner_id'])) {
            $data['owner_id'] = auth()->id();
        }

        $ticket = Ticket::create($data);
        TicketCreatedEvent::dispatch($ticket);

        return $ticket;
    }
}
