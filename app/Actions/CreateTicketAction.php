<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;

class CreateTicketAction
{
    /**
     * Executes the ticket creation logic.
     *
     * @param  array<string, mixed>  $data
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
