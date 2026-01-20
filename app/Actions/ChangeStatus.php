<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Enums\TicketStatusEnum;

class ChangeStatus
{
    /**
     * Execute the change status action.
     *
     * @param Ticket $ticket The ticket to update
     * @param string $status The new status (string value)
     * @param string $reason The reason for the change
     * @return void
     */
    public function execute(Ticket $ticket, string $status, string $reason): void
    {
        $ticket->setStatus($status, $reason);
        
        // Convert string to enum
        $statusEnum = TicketStatusEnum::from($status);
        $ticket->status = $statusEnum;
        
        $ticket->save();
    }
}
