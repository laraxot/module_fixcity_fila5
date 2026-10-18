<?php

declare(strict_types=1);

namespace Modules\Fixcity\Services;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Illuminate\Support\Facades\Notification;
// use Modules\Notify\Notifications\TicketStatusChangedNotification;
// use Modules\Notify\Notifications\TicketAssignedNotification;

class NotificationService
{
    /**
     * Send ticket status changed notification.
     *
     * @param Ticket $ticket
     * @param string $newStatus
     * @return bool
     */
    public function sendTicketStatusChanged(Ticket $ticket, string $newStatus): bool
    {
        $users = $this->getUsersToNotify($ticket);
        
        foreach ($users as $user) {
            // Notification::send($user, new TicketStatusChangedNotification($ticket, $newStatus));
            // TODO: Create TicketStatusChangedNotification class
        }
        
        return true;
    }
    
    /**
     * Send ticket assigned notification.
     *
     * @param Ticket $ticket
     * @param User $assignee
     * @return bool
     */
    public function sendTicketAssigned(Ticket $ticket, User $assignee): bool
    {
        // Notification::send($assignee, new TicketAssignedNotification($ticket));
        // TODO: Create TicketAssignedNotification class
        
        return true;
    }
    
    /**
     * Send ticket comment notification.
     *
     * @param Ticket $ticket
     * @param User $commenter
     * @param string $comment
     * @return bool
     */
    public function sendTicketCommentNotification(Ticket $ticket, User $commenter, string $comment): bool
    {
        $users = $this->getUsersToNotify($ticket);
        
        // Rimuovi il commenter dalla lista dei destinatari
        $users = $users->filter(function ($user) use ($commenter) {
            return $user->id !== $commenter->id;
        });
        
        foreach ($users as $user) {
            // Invia notifica per nuovo commento
            // Notification::send($user, new TicketCommentNotification($ticket, $commenter, $comment));
        }
        
        return true;
    }
    
    /**
     * Send ticket escalation notification.
     *
     * @param Ticket $ticket
     * @param User $escalator
     * @param string $reason
     * @return bool
     */
    public function sendTicketEscalationNotification(Ticket $ticket, User $escalator, string $reason): bool
    {
        $managers = $this->getManagers();
        
        foreach ($managers as $manager) {
            // Invia notifica di escalation ai manager
            // Notification::send($manager, new TicketEscalationNotification($ticket, $escalator, $reason));
        }
        
        return true;
    }
    
    /**
     * Send ticket approval notification.
     *
     * @param Ticket $ticket
     * @param User $approver
     * @return bool
     */
    public function sendTicketApprovalNotification(Ticket $ticket, User $approver): bool
    {
        $users = $this->getUsersToNotify($ticket);
        
        foreach ($users as $user) {
            // Invia notifica di approvazione
            // Notification::send($user, new TicketApprovalNotification($ticket, $approver));
        }
        
        return true;
    }
    
    /**
     * Send ticket rejection notification.
     *
     * @param Ticket $ticket
     * @param User $rejector
     * @param string $reason
     * @return bool
     */
    public function sendTicketRejectionNotification(Ticket $ticket, User $rejector, string $reason): bool
    {
        $users = $this->getUsersToNotify($ticket);
        
        foreach ($users as $user) {
            // Invia notifica di rifiuto
            // Notification::send($user, new TicketRejectionNotification($ticket, $rejector, $reason));
        }
        
        return true;
    }
    
    /**
     * Send ticket resolution notification.
     *
     * @param Ticket $ticket
     * @param User $resolver
     * @return bool
     */
    public function sendTicketResolutionNotification(Ticket $ticket, User $resolver): bool
    {
        $users = $this->getUsersToNotify($ticket);
        
        foreach ($users as $user) {
            // Invia notifica di risoluzione
            // Notification::send($user, new TicketResolutionNotification($ticket, $resolver));
        }
        
        return true;
    }
    
    /**
     * Send ticket closure notification.
     *
     * @param Ticket $ticket
     * @param User $closer
     * @return bool
     */
    public function sendTicketClosureNotification(Ticket $ticket, User $closer): bool
    {
        $users = $this->getUsersToNotify($ticket);
        
        foreach ($users as $user) {
            // Invia notifica di chiusura
            // Notification::send($user, new TicketClosureNotification($ticket, $closer));
        }
        
        return true;
    }
    
    /**
     * Send ticket reopening notification.
     *
     * @param Ticket $ticket
     * @param User $reopener
     * @return bool
     */
    public function sendTicketReopeningNotification(Ticket $ticket, User $reopener): bool
    {
        $users = $this->getUsersToNotify($ticket);
        
        foreach ($users as $user) {
            // Invia notifica di riapertura
            // Notification::send($user, new TicketReopeningNotification($ticket, $reopener));
        }
        
        return true;
    }
    
    /**
     * Get users to notify for a ticket.
     *
     * @param Ticket $ticket
     * @return Collection<int, User>
     */
    private function getUsersToNotify(Ticket $ticket): Collection
    {
        $users = collect();
        
        // Aggiungi il creatore del ticket
        if ($ticket->creator) {
            $users->push($ticket->creator);
        }
        
        // Aggiungi l'assegnatario del ticket
        if ($ticket->assignee) {
            $users->push($ticket->assignee);
        }
        
        // Aggiungi i subscriber del ticket
        if ($ticket->subscribers) {
            $users = $users->merge($ticket->subscribers);
        }
        
        // Rimuovi duplicati
        return $users->unique('id');
    }
    
    /**
     * Get managers for escalation notifications.
     *
     * @return Collection<int, User>
     */
    private function getManagers(): Collection
    {
        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['manager', 'admin', 'supervisor']);
        })->get();
    }
    
    /**
     * Send bulk notification to multiple users.
     *
     * @param Collection<int, User> $users
     * @param mixed $notification
     * @return bool
     */
    public function sendBulkNotification(Collection $users, $notification): bool
    {
        Notification::send($users, $notification);
        
        return true;
    }
    
    /**
     * Send delayed notification.
     *
     * @param User $user
     * @param object $notification
     * @param Carbon $delay
     * @return bool
     */
    public function sendDelayedNotification(User $user, object $notification, Carbon $delay): bool
    {
        if (method_exists($notification, 'delay')) {
            Notification::send($user, $notification->delay($delay));
        } else {
            Notification::send($user, $notification);
        }
        
        return true;
    }
    
    /**
     * Mark notification as read.
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function markNotificationAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get unread notifications count for user.
     *
     * @param User $user
     * @return int
     */
    public function getUnreadNotificationsCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }
    
    /**
     * Get user notifications with pagination.
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->notifications()->paginate($perPage);
    }
}
