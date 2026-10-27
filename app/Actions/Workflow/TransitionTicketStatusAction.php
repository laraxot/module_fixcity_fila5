<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\Workflow;

use Modules\Fixcity\Enums\TicketStatusEnum;
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
        $current = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue());
        $target = TicketStatusEnum::tryFrom($targetStatus);

        if (! $current instanceof TicketStatusEnum || ! $target instanceof TicketStatusEnum) {
            throw new \InvalidArgumentException("Invalid ticket status transition target: {$targetStatus}");
        }

        $current->assertCanTransitionTo($target);
        $ticket->update(['status' => $target]);

        return true;
    }
}
