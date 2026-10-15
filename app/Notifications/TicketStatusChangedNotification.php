<?php

declare(strict_types=1);

namespace Modules\Fixcity\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\GetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Xot\Contracts\UserContract;

final class TicketStatusChangedNotification extends Notification implements ShouldQueueAfterCommit
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        private readonly string $ticketCode,
        private readonly TicketStatusEnum $status,
    ) {}

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300];
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->canReceiveEmail($notifiable)) {
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

    /**
     * @return array<string, string>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('fixcity::ticket.notification.title'),
            'body' => __('fixcity::ticket.notification.status_changed', [
                'ticket_code' => $this->ticketCode,
                'status' => $this->status->getLabel(),
            ]),
            'ticket_code' => $this->ticketCode,
            'status' => $this->status->value,
            'action_url' => LaravelLocalization::localizeUrl(
                '/tickets/track?code='.rawurlencode($this->ticketCode),
            ),
        ];
    }

    private function canReceiveEmail(object $notifiable): bool
    {
        return $notifiable instanceof UserContract
            && $notifiable->hasVerifiedEmail()
            && is_string($notifiable->email)
            && trim($notifiable->email) !== ''
            && app(GetFixcityEmailNotificationPreferenceAction::class)->execute($notifiable);
    }
}
