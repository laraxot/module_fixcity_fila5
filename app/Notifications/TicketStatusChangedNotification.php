<?php

declare(strict_types=1);

namespace Modules\Fixcity\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Fixcity\Actions\BuildTicketDetailUrlAction;
use Modules\Fixcity\Actions\BuildTicketTrackUrlAction;
use Modules\Fixcity\Actions\GetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;

/**
 * Aggiornamento di stato pubblico di una segnalazione. Al PROPRIETARIO porta il codice di tracciamento e il link di tracciamento;
 * a chi la SEGUE ({@see self::forFollower()}) porta titolo e link al dettaglio pubblico, mai il codice: e' una capability che
 * permette di leggere la segnalazione anche quando non e' pubblica (DCP-287).
 */
final class TicketStatusChangedNotification extends Notification implements ShouldQueueAfterCommit
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        private readonly string $ticketCode,
        private readonly TicketStatusEnum $status,
        private readonly ?int $followedTicketId = null,
        private readonly string $followedTicketTitle = '',
    ) {}

    /** Versione per chi segue la segnalazione di altri: nessun codice di tracciamento, solo titolo e id pubblico. */
    public static function forFollower(Ticket $ticket, TicketStatusEnum $status): self
    {
        return new self('', $status, $ticket->id, $ticket->name);
    }

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
        if ($this->followedTicketId !== null) {
            return [
                'title' => __('fixcity::ticket.notification.title'),
                'body' => __('fixcity::ticket_notification.status_changed_followed', [
                    'ticket_title' => $this->followedTicketTitle,
                    'status' => $this->status->getLabel(),
                ]),
                'ticket_id' => (string) $this->followedTicketId,
                'ticket_title' => $this->followedTicketTitle,
                'status' => $this->status->value,
                'action_url' => app(BuildTicketDetailUrlAction::class)->execute($this->followedTicketId),
            ];
        }

        $actionUrl = app(BuildTicketTrackUrlAction::class)->execute($this->ticketCode);

        return [
            'title' => __('fixcity::ticket.notification.title'),
            'body' => __('fixcity::ticket.notification.status_changed', [
                'ticket_code' => $this->ticketCode,
                'status' => $this->status->getLabel(),
            ]),
            'ticket_code' => $this->ticketCode,
            'status' => $this->status->value,
            'action_url' => $actionUrl,
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
