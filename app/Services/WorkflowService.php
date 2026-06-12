<?php

declare(strict_types=1);

namespace Modules\Fixcity\Services;

use InvalidArgumentException;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class WorkflowService
{
    /**
     * @var array<string, list<string>>
     */
    private const TRANSITIONS = [
        'draft' => ['pending', 'in_review'],
        'pending' => ['assigned', 'in_review', 'in_progress', 'escalated'],
        'assigned' => ['in_progress', 'escalated'],
        'in_review' => ['in_progress', 'on_hold'],
        'in_progress' => ['review', 'on_hold', 'resolved', 'escalated'],
        'review' => ['approved', 'rejected', 'return_to_work'],
        'approved' => ['resolved'],
        'rejected' => ['draft'],
        'resolved' => ['closed'],
        'closed' => ['reopened'],
        'escalated' => ['assigned'],
        'return_to_work' => ['in_progress'],
        'reopened' => ['assigned', 'in_progress'],
    ];

    /**
     * Submit ticket for review.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function submitForReview(Ticket $ticket): bool
    {
        if ($ticket->status !== 'draft') {
            throw new InvalidArgumentException('Ticket must be in draft status to submit for review');
        }
        
        $ticket->update(['status' => 'pending']);
        
        return true;
    }
    
    /**
     * Assign ticket to a user.
     *
     * @param Ticket $ticket
     * @param User $assignee
     * @return bool
     */
    public function assignTicket(Ticket $ticket, User $assignee): bool
    {
        if ($ticket->status !== 'pending') {
            throw new InvalidArgumentException('Ticket must be in pending status to assign');
        }
        
        $ticket->update([
            'assigned_to' => $assignee->id,
            'status' => 'assigned'
        ]);
        
        return true;
    }
    
    /**
     * Start work on ticket.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function startWork(Ticket $ticket): bool
    {
        if ($ticket->status !== 'assigned') {
            throw new InvalidArgumentException('Ticket must be in assigned status to start work');
        }
        
        $ticket->update(['status' => 'in_progress']);
        
        return true;
    }
    
    /**
     * Submit ticket for approval.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function submitForApproval(Ticket $ticket): bool
    {
        if ($ticket->status !== 'in_progress') {
            throw new InvalidArgumentException('Ticket must be in progress status to submit for approval');
        }
        
        $ticket->update(['status' => 'review']);
        
        return true;
    }
    
    /**
     * Approve ticket.
     *
     * @param Ticket $ticket
     * @param User $approver
     * @return bool
     */
    public function approveTicket(Ticket $ticket, User $approver): bool
    {
        if ($ticket->status !== 'review') {
            throw new InvalidArgumentException('Ticket must be in review status to approve');
        }
        
        $ticket->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * Reject ticket.
     *
     * @param Ticket $ticket
     * @param User $approver
     * @param string $reason
     * @return bool
     */
    public function rejectTicket(Ticket $ticket, User $approver, string $reason): bool
    {
        if ($ticket->status !== 'review') {
            throw new InvalidArgumentException('Ticket must be in review status to reject');
        }
        
        if (empty(trim($reason))) {
            throw new InvalidArgumentException('Rejection reason is required');
        }
        
        $ticket->update([
            'status' => 'rejected',
            'rejected_by' => $approver->id,
            'rejection_reason' => $reason,
            'rejected_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * Resolve ticket.
     *
     * @param Ticket $ticket
     * @param User $resolver
     * @return bool
     */
    public function resolveTicket(Ticket $ticket, User $resolver): bool
    {
        if ($ticket->status !== 'approved') {
            throw new InvalidArgumentException('Ticket must be in approved status to resolve');
        }
        
        $ticket->update([
            'status' => 'resolved',
            'resolved_by' => $resolver->id,
            'resolved_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * Return ticket to work.
     *
     * @param Ticket $ticket
     * @param User $user
     * @param string $reason
     * @return bool
     */
    public function returnToWork(Ticket $ticket, User $user, string $reason): bool
    {
        if ($ticket->status !== 'review') {
            throw new InvalidArgumentException('Ticket must be in review status to return to work');
        }
        
        if (empty(trim($reason))) {
            throw new InvalidArgumentException('Return reason is required');
        }
        
        $ticket->update([
            'status' => 'in_progress',
            'returned_by' => $user->id,
            'return_reason' => $reason,
            'returned_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * Escalate ticket.
     *
     * @param Ticket $ticket
     * @param User $escalator
     * @param string $reason
     * @return bool
     */
    public function escalateTicket(Ticket $ticket, User $escalator, string $reason): bool
    {
        if (empty(trim($reason))) {
            throw new InvalidArgumentException('Escalation reason is required');
        }
        
        $ticket->update([
            'status' => 'escalated',
            'escalated_by' => $escalator->id,
            'escalation_reason' => $reason,
            'escalated_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * Get workflow history for a ticket.
     *
     * @param Ticket $ticket
     * @return Collection<int, TicketActivity>
     */
    public function getWorkflowHistory(Ticket $ticket): Collection
    {
        /** @var Collection<int, TicketActivity> $activities */
        $activities = $ticket->activities()->orderBy('created_at')->get();
        return $activities;
    }
    
    /**
     * Get valid transitions for a ticket.
     *
     * @param Ticket $ticket
     * @return array<string>
     */
    public function getValidTransitions(Ticket $ticket): array
    {
        $transitions = [
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
            'return_to_work' => ['in_progress'],
            'reopened' => ['assigned']
        ];
        
        $currentStatus = $ticket->status->value ?? 'pending';
        return $transitions[$currentStatus] ?? [];
    }
    
    /**
     * Get all workflow states.
     *
     * @return array<string>
     */
    public function getWorkflowStates(): array
    {
        return [
            'draft', 'pending', 'assigned', 'in_progress', 
            'review', 'approved', 'rejected', 'resolved', 
            'closed', 'escalated', 'reopened'
        ];
    }

    /**
     * Check if ticket can transition to a given status.
     *
     * @param Ticket $ticket
     * @param string $status
     * @return bool
     */
    public function canTransitionTo(Ticket $ticket, string $status): bool
    {
        $currentStatus = $ticket->status instanceof \BackedEnum ? $ticket->status->value : 'pending';
        $transitions = self::TRANSITIONS[$currentStatus] ?? [];

        return in_array($status, $transitions, true);
    }

    /**
     * Get available transitions for a ticket.
     *
     * @param Ticket $ticket
     * @return array<int, string>
     */
    public function getAvailableTransitions(Ticket $ticket): array
    {
        $currentStatus = $ticket->status instanceof \BackedEnum ? $ticket->status->value : 'pending';

        return self::TRANSITIONS[$currentStatus] ?? [];
    }

    /**
     * Transition ticket to a new status.
     *
     * @param Ticket $ticket
     * @param string $status
     * @return bool
     */
    public function transitionTo(Ticket $ticket, string $status): bool
    {
        if (!$this->canTransitionTo($ticket, $status)) {
            return false;
        }

        $oldStatus = $ticket->status instanceof \BackedEnum ? $ticket->status->value : 'pending';

        $ticket->update(['status' => $status]);

        $ticket->activities()->create([
            'description' => "Status changed from {$oldStatus} to {$status}",
            'from_status' => $oldStatus,
            'to_status' => $status,
            'user_id' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Get workflow rules for a ticket.
     *
     * @param Ticket $ticket
     * @return array<string, mixed>
     */
    public function getWorkflowRules(Ticket $ticket): array
    {
        $type = $ticket->type instanceof \BackedEnum ? $ticket->type->value : 'default';

        $rules = [
            'transitions' => self::TRANSITIONS,
            'constraints' => [
                'requires_assignee' => ['in_progress', 'assigned'],
                'requires_resolution_note' => ['resolved'],
            ],
        ];

        if ($ticket->priority instanceof \BackedEnum && $ticket->priority->value === 'urgent') {
            $rules['constraints']['urgent_priority'] = [
                'max_resolution_hours' => 24,
                'requires_approval' => true,
            ];
        }

        if ($type === 'road_maintenance') {
            $rules['constraints']['requires_inspection'] = true;
        }

        return $rules;
    }

    /**
     * Validate if a transition is allowed.
     *
     * @param Ticket $ticket
     * @param string $newStatus
     * @return array<string, mixed>
     */
    public function validateTransition(Ticket $ticket, string $newStatus): array
    {
        $errors = [];

        if ($newStatus === 'in_progress' && $ticket->responsible_id === null) {
            $errors[] = 'Ticket must be assigned to proceed';
        }

        if ($newStatus === 'resolved') {
            /** @var string|null $resolutionNote */
            $resolutionNote = $ticket->getAttribute('resolution_note') ?? '';
            if (trim((string) $resolutionNote) === '') {
                $errors[] = 'Resolution note is required';
            }
        }

        $valid = empty($errors) && $this->canTransitionTo($ticket, $newStatus);

        return [
            'valid' => $valid,
            'errors' => $errors,
        ];
    }

    /**
     * Get transition history for a ticket.
     *
     * @param Ticket $ticket
     * @return Collection<int, TicketActivity>
     */
    public function getTransitionHistory(Ticket $ticket): Collection
    {
        /** @var Collection<int, TicketActivity> $activities */
        $activities = $ticket->activities()->orderBy('created_at')->get();

        return $activities;
    }

    /**
     * Check if a ticket can be reopened.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function canReopenTicket(Ticket $ticket): bool
    {
        $currentStatus = $ticket->status instanceof \BackedEnum ? $ticket->status->value : 'pending';

        if ($currentStatus !== 'closed') {
            return false;
        }

        /** @var Carbon|null $closedAt */
        $closedAt = $ticket->getAttribute('closed_at');
        if ($closedAt instanceof Carbon) {
            if ($closedAt->lt(now()->subDays(30))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Reopen a closed ticket.
     *
     * @param Ticket $ticket
     * @param string $reason
     * @return bool
     */
    public function reopenTicket(Ticket $ticket, string $reason): bool
    {
        if (!$this->canReopenTicket($ticket)) {
            return false;
        }

        $ticket->update(['status' => 'reopened']);

        $ticket->activities()->create([
            'description' => 'Ticket reopened: ' . $reason,
            'user_id' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Get workflow performance metrics for a ticket.
     *
     * @param Ticket $ticket
     * @return array<string, mixed>
     */
    public function getWorkflowMetrics(Ticket $ticket): array
    {
        $avgResolutionTime = 0;
        /** @var Carbon|null $resolvedAt */
        $resolvedAt = $ticket->getAttribute('resolved_at');
        if ($ticket->created_at instanceof Carbon && $resolvedAt instanceof Carbon) {
            $avgResolutionTime = $ticket->created_at->diffInHours($resolvedAt);
        }

        $transitionCount = $ticket->activities()->count();

        $efficiency = $transitionCount > 0 ? min(100, (10 / $transitionCount) * 100) : 100;

        return [
            'avg_resolution_time' => $avgResolutionTime,
            'transition_count' => $transitionCount,
            'workflow_efficiency' => $efficiency,
        ];
    }

    /**
     * Apply workflow automation rules.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function applyWorkflowAutomation(Ticket $ticket): bool
    {
        $priority = $ticket->priority instanceof \BackedEnum ? $ticket->priority->value : 'low';

        if ($priority === 'urgent' && $ticket->responsible_id === null) {
            $autoAssignUser = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['agent', 'admin']);
            })->first();

            if ($autoAssignUser !== null) {
                $ticket->update(['responsible_id' => $autoAssignUser->id]);
            }
        }

        /** @var Carbon|null $dueDate */
        $dueDate = $ticket->getAttribute('due_date');
        if ($dueDate instanceof Carbon && $dueDate->lt(now())) {
            $ticket->update(['priority' => 'urgent']);
        }

        return true;
    }
}
