<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\Workflow;

use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Spatie Queueable Action — conversione da WorkflowService (No-Services BMAD).
 * Lo scopo originale: gestire transizioni stato ticket (draft→pending→assigned→in_progress→approved→closed).
 * La fonte dell'errore PHPStan (`method.notFound` su `canTransitionTo`) era che il servizio era un monolite da eliminare.
 */
final class TransitionTicketStatusAction
{
    use QueueableAction;

    /**
     * Apply one validated domain transition synchronously or through Spatie's queue API.
     *
     * The concrete workflow operations remain separate Actions; this class is the small
     * state-transition primitive that replaced the old service monolith.
     */
    public function execute(Ticket $ticket, string $targetStatus): bool
    {
        $valid = [
            'draft' => ['pending'],
            'pending' => ['assigned', 'escalated'],
            'assigned' => ['in_progress', 'escalated'],
            'in_progress' => ['review', 'escalated'],
            'review' => ['approved', 'rejected', 'return_to_work'],
            'approved' => ['resolved'],
            'rejected' => ['draft'],
            'resolved' => ['closed'],
            'closed' => ['reopened'],
            'escalated' => ['assigned'],
            'reopened' => ['assigned'],
        ];

        $current = $ticket->resolveTicketStatusValue();
        if (! in_array($targetStatus, $valid[$current] ?? [], true)) {
            throw new \InvalidArgumentException("Invalid transition from {$current} to {$targetStatus}");
        }

        $ticket->update(['status' => $targetStatus]);

        return true;
    }
}
