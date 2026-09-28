<?php

declare(strict_types=1);

namespace Modules\Fixcity\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Fixcity\Actions\GetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Xot\Contracts\UserContract;

final class TicketAssignedNotification extends Notification implements ShouldQueueAfterCommit
{
    use Queueable;

    public int $tries = 3;

    public function __construct(private readonly int $ticketId) {}

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300];
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable instanceof UserContract
            && $notifiable->hasVerifiedEmail()
            && is_string($notifiable->email)
            && trim($notifiable->email) !== ''
            && app(GetFixcityEmailNotificationPreferenceAction::class)->execute($notifiable)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(UserContract $notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject($data['title'])
            ->line($data['body'])
            ->action(__('fixcity::ticket_notification.view_ticket'), $data['action_url']);
    }

    /** @return array{title: string, body: string, ticket_id: int, action_url: string} */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('fixcity::ticket.notification.assignment_title'),
            'body' => __('fixcity::ticket.notification.assigned_to_you', ['ticket_id' => $this->ticketId]),
            'ticket_id' => $this->ticketId,
            'action_url' => TicketResource::getUrl('view', ['record' => $this->ticketId]),
        ];
    }
}
