<?php

declare(strict_types=1);

namespace Modules\Fixcity\Notifications;

use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Notifications\Concerns\DeliversByDatabaseAndVerifiedMail;
use Modules\Xot\Contracts\UserContract;

/**
 * Avviso agli utenti con ruolo PA: e' arrivata una nuova segnalazione da esaminare (DCP-284, A-01).
 *
 * Contiene solo il numero e il link al back office: niente codice di tracciamento (capability pubblica), email, `owner_id` ne'
 * testo scritto dal cittadino. Chi lo riceve apre la segnalazione nel pannello, dove i permessi decidono cosa vede.
 */
final class TicketReceivedStaffNotification extends Notification implements ShouldQueueAfterCommit
{
    use DeliversByDatabaseAndVerifiedMail;

    public function __construct(private readonly int $ticketId) {}

    public function toMail(UserContract $notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject($data['title'])
            ->line($data['body'])
            ->action((string) __('fixcity::ticket_notification.view_ticket'), $data['action_url']);
    }

    /**
     * @return array{title: string, body: string, ticket_id: int, action_url: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => (string) __('fixcity::ticket_notification.staff.received.title'),
            'body' => (string) __('fixcity::ticket_notification.staff.received.body', ['ticket_id' => $this->ticketId]),
            'ticket_id' => $this->ticketId,
            // Pannello esplicito: in un worker o in una richiesta del front office non c'e' un pannello corrente.
            'action_url' => TicketResource::getUrl('view', ['record' => $this->ticketId], panel: 'fixcity::admin'),
        ];
    }
}
