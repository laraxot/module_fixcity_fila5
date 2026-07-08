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
    /**
     * @return Collection<int, User>
     */
    private function getUsersToNotify(Ticket $ticket): Collection
    {
        /** @var Collection<int, User> $users */
        $users = collect();
        
        // Aggiungi il creatore del ticket
        if ($ticket->owner instanceof User) {
            $users->push($ticket->owner);
        }

        // Aggiungi l'assegnatario del ticket
        if ($ticket->responsible instanceof User) {
            $users->push($ticket->responsible);
        }
        
        // Aggiungi i subscriber del ticket (pivot ticket_subscribers)
        /** @var Collection<int, User> $ticketSubscribers */
        $ticketSubscribers = $ticket->ticketSubscribers()->get();
        if ($ticketSubscribers->isNotEmpty()) {
            $users = $users->merge($ticketSubscribers);
        }
        
        // Rimuovi duplicati
        /** @var Collection<int, User> $unique */
        $unique = $users->unique('id');

        return $unique;
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
     * @return LengthAwarePaginator<int, \Illuminate\Notifications\DatabaseNotification>
     */
    public function getUserNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, \Illuminate\Notifications\DatabaseNotification> $paginator */
        $paginator = $user->notifications()->paginate($perPage);

        return $paginator;
    }

    /**
     * Notify when a ticket is created.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function notifyTicketCreated(Ticket $ticket): bool
    {
        $users = $this->getUsersToNotify($ticket);

        foreach ($users as $user) {
            // Notification::send($user, new TicketCreatedNotification($ticket));
        }

        return true;
    }

    /**
     * Notify when a ticket is updated.
     *
     * @param Ticket $ticket
     * @param string|null $oldStatus
     * @param string|null $oldPriority
     * @return bool
     */
    public function notifyTicketUpdated(Ticket $ticket, ?string $oldStatus = null, ?string $oldPriority = null): bool
    {
        $users = $this->getUsersToNotify($ticket);

        foreach ($users as $user) {
            // Notification::send($user, new TicketUpdatedNotification($ticket, $oldStatus, $oldPriority));
        }

        return true;
    }

    /**
     * Notify when a ticket is assigned.
     *
     * @param Ticket $ticket
     * @param User $assignee
     * @return bool
     */
    public function notifyTicketAssigned(Ticket $ticket, User $assignee): bool
    {
        // Notification::send($assignee, new TicketAssignedNotification($ticket));
        // Notification::send($ticket->owner, new TicketAssignedNotification($ticket, $assignee));

        return true;
    }

    /**
     * Notify when a ticket is resolved.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function notifyTicketResolved(Ticket $ticket): bool
    {
        $users = $this->getUsersToNotify($ticket);

        foreach ($users as $user) {
            // Notification::send($user, new TicketResolvedNotification($ticket));
        }

        return true;
    }

    /**
     * Notify when a ticket is closed.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function notifyTicketClosed(Ticket $ticket): bool
    {
        $users = $this->getUsersToNotify($ticket);

        foreach ($users as $user) {
            // Notification::send($user, new TicketClosedNotification($ticket));
        }

        return true;
    }

    /**
     * Notify when a comment is added to a ticket.
     *
     * @param Ticket $ticket
     * @param mixed $comment
     * @return bool
     */
    public function notifyCommentAdded(Ticket $ticket, mixed $comment): bool
    {
        $users = $this->getUsersToNotify($ticket);

        // Remove comment author from notification list
        $commentUserId = is_object($comment) && property_exists($comment, 'user_id') ? $comment->user_id : null;
        if ($commentUserId !== null) {
            $users = $users->filter(function ($user) use ($commentUserId) {
                return $user->id !== $commentUserId;
            });
        }

        foreach ($users as $user) {
            // Notification::send($user, new TicketCommentAddedNotification($ticket, $comment));
        }

        return true;
    }

    /**
     * Notify when a due date is approaching.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function notifyDueDateApproaching(Ticket $ticket): bool
    {
        $users = $this->getUsersToNotify($ticket);

        foreach ($users as $user) {
            // Notification::send($user, new DueDateApproachingNotification($ticket));
        }

        return true;
    }

    /**
     * Notify when a due date is exceeded.
     *
     * @param Ticket $ticket
     * @return bool
     */
    public function notifyDueDateExceeded(Ticket $ticket): bool
    {
        $users = $this->getUsersToNotify($ticket);

        foreach ($users as $user) {
            // Notification::send($user, new DueDateExceededNotification($ticket));
        }

        return true;
    }

    /**
     * Send bulk notifications to multiple users.
     *
     * @param Collection<int, User> $users
     * @param string $message
     * @return bool
     */
    public function sendBulkNotifications(Collection $users, string $message): bool
    {
        foreach ($users as $user) {
            $user->notifications()->create([
                'type' => 'bulk_notification',
                'data' => ['message' => $message],
            ]);
        }

        return true;
    }

    /**
     * Send email notification to a user.
     *
     * @param User $user
     * @param string $subject
     * @param string $content
     * @return bool
     */
    public function sendEmailNotification(User $user, string $subject, string $content): bool
    {
        // Mail::to($user)->send(new SimpleMail($subject, $content));

        return true;
    }

    /**
     * Send SMS notification to a user.
     *
     * @param User $user
     * @param string $message
     * @return bool
     */
    public function sendSMSNotification(User $user, string $message): bool
    {
        // Sms::to($user->phone)->send($message);

        return true;
    }

    /**
     * Send push notification to a user.
     *
     * @param User $user
     * @param string $title
     * @param string $message
     * @return bool
     */
    public function sendPushNotification(User $user, string $title, string $message): bool
    {
        // PushNotification::send($user, $title, $message);

        return true;
    }
}
