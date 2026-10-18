<?php

declare(strict_types=1);

namespace Modules\Fixcity\Services;

use InvalidArgumentException;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Illuminate\Support\Collection;

class WorkflowService
{
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
}
