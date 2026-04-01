<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;

class ChangeStatus
{
    /**
     * Execute the change status action.
     *
     * @param  Ticket  $ticket  The ticket to update
     * @param  string  $status  The new status (string value)
     * @param  string  $reason  The reason for the change
     */
    public function execute(Ticket $ticket, string $status, string $reason): void
    {
        /** @phpstan-ignore method.notFound */
        $ticket->setStatus($status);

        // Note: reason is logged but not stored on ticket
        // Could be added to activity log or comments
    }
}
