<?php

declare(strict_types=1);

namespace Modules\Fixcity\Services;

use InvalidArgumentException;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class TicketService
{
    /**
     * Create a new ticket.
     *
     * @param array<string, mixed> $ticketData
     * @param User $user
     * @return Ticket
     */
    public function createTicket(array $ticketData, User $user): Ticket
    {
        $ticketData['created_by'] = $user->id;
        $ticketData['status'] = $ticketData['status'] ?? 'draft';
        
        return Ticket::create($ticketData);
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
     * Update ticket status.
     *
     * @param Ticket $ticket
     * @param string $newStatus
     * @return bool
     */
    public function updateStatus(Ticket $ticket, string $newStatus): bool
    {
        $currentStatus = $ticket->status->value ?? 'pending';
        $validTransitions = $this->getValidStatusTransitions($currentStatus);
        
        if (!in_array($newStatus, $validTransitions)) {
            throw new InvalidArgumentException("Invalid status transition from {$currentStatus} to {$newStatus}");
        }
        
        $ticket->update(['status' => $newStatus]);
        
        return true;
    }
    
    /**
     * Update ticket priority.
     *
     * @param Ticket $ticket
     * @param string $priority
     * @return bool
     */
    public function updatePriority(Ticket $ticket, string $priority): bool
    {
        $validPriorities = ['low', 'medium', 'high', 'urgent'];
        
        if (!in_array($priority, $validPriorities)) {
            throw new InvalidArgumentException("Invalid priority value: {$priority}");
        }
        
        $ticket->update(['priority' => $priority]);
        
        return true;
    }
    
    /**
     * Update ticket category.
     *
     * @param Ticket $ticket
     * @param string $category
     * @return bool
     */
    public function updateCategory(Ticket $ticket, string $category): bool
    {
        $validCategories = ['general', 'technical', 'support', 'bug', 'feature'];
        
        if (!in_array($category, $validCategories)) {
            throw new InvalidArgumentException("Invalid category value: {$category}");
        }
        
        $ticket->update(['category' => $category]);
        
        return true;
    }
    
    /**
     * Close ticket.
     *
     * @param Ticket $ticket
     * @param User $user
     * @return bool
     */
    public function closeTicket(Ticket $ticket, User $user): bool
    {
        $currentStatus = $ticket->status->value ?? 'pending';
        if (!in_array($currentStatus, ['resolved', 'approved'])) {
            throw new InvalidArgumentException("Cannot close ticket in status: {$currentStatus}");
        }
        
        $ticket->update([
            'status' => 'closed',
            'closed_by' => $user->id,
            'closed_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * Reopen ticket.
     *
     * @param Ticket $ticket
     * @param User $user
     * @return bool
     */
    public function reopenTicket(Ticket $ticket, User $user): bool
    {
        if ($ticket->status !== 'closed') {
            throw new InvalidArgumentException('Cannot reopen ticket not in closed status');
        }
        
        $ticket->update([
            'status' => 'reopened',
            'reopened_by' => $user->id,
            'reopened_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * Add comment to ticket.
     *
     * @param Ticket $ticket
     * @param string $comment
     * @param User $user
     * @return bool
     */
    public function addComment(Ticket $ticket, string $comment, User $user): bool
    {
        if (empty(trim($comment))) {
            throw new InvalidArgumentException('Comment cannot be empty');
        }
        
        $ticket->comments()->create([
            'user_id' => $user->id,
            'comment' => $comment
        ]);
        
        return true;
    }
    
    /**
     * Get tickets by user.
     *
     * @param User $user
     * @return Collection<int, Ticket>
     */
    public function getTicketsByUser(User $user): Collection
    {
        return Ticket::where('created_by', $user->id)
            ->orWhere('assigned_to', $user->id)
            ->get();
    }
    
    /**
     * Get tickets by status.
     *
     * @param string $status
     * @return Collection<int, Ticket>
     */
    public function getTicketsByStatus(string $status): Collection
    {
        return Ticket::where('status', $status)->get();
    }
    
    /**
     * Get tickets by priority.
     *
     * @param string $priority
     * @return Collection<int, Ticket>
     */
    public function getTicketsByPriority(string $priority): Collection
    {
        return Ticket::where('priority', $priority)->get();
    }
    
    /**
     * Get tickets by category.
     *
     * @param string $category
     * @return Collection<int, Ticket>
     */
    public function getTicketsByCategory(string $category): Collection
    {
        return Ticket::where('category', $category)->get();
    }
    
    /**
     * Search tickets.
     *
     * @param string $query
     * @return Collection<int, Ticket>
     */
    public function searchTickets(string $query): Collection
    {
        return Ticket::where(function (Builder $builder) use ($query) {
            $builder->where('title', 'like', "%{$query}%")
                   ->orWhere('description', 'like', "%{$query}%");
        })->get();
    }
    
    /**
     * Get valid status transitions for a given status.
     *
     * @param string $currentStatus
     * @return array<string>
     */
    private function getValidStatusTransitions(string $currentStatus): array
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
        
        return $transitions[$currentStatus] ?? [];
    }
}
