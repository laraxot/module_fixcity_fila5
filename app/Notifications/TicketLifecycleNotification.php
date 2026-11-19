<?php

declare(strict_types=1);

namespace Modules\Fixcity\Notifications;

use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Modules\Fixcity\Actions\BuildTicketTrackUrlAction;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Notifications\Concerns\DeliversByDatabaseAndVerifiedMail;
use Modules\Xot\Contracts\UserContract;

use function Safe\preg_replace;

/**
 * Avviso al PROPRIETARIO su un evento del ciclo di vita della sua segnalazione: ricevuta, approvata, scartata, presa in carico,
 * chiusa (DCP-284, A-01). Il codice di tracciamento e' nel messaggio perche' chi lo riceve e' chi ha creato la segnalazione.
 *
 * Il testo viene dalle chiavi `fixcity::ticket_notification.*` (mai da stringhe del database). L'unico testo libero e' il motivo
 * dello scarto, scritto da un operatore: ridotto a una riga, limitato e, nella mail, con i caratteri Markdown resi inerti.
 *
 * I dati salvati (`ticket_code`, `status`, `event`) sono quelli che l'elenco dell'area personale ricalcola nella lingua di chi legge.
 */
final class TicketLifecycleNotification extends Notification implements ShouldQueueAfterCommit
{
    use DeliversByDatabaseAndVerifiedMail;

    private const int REASON_LIMIT = 300;

    public function __construct(
        private readonly string $ticketCode,
        private readonly TicketLifecycleEventEnum $event,
        private readonly TicketStatusEnum $status,
        private readonly ?string $reason = null,
    ) {}

    public function toMail(UserContract $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title())
            ->line($this->body($this->markdownSafe($this->plainReason())))
            ->action((string) __('fixcity::ticket_notification.view_ticket'), $this->actionUrl());
    }

    /**
     * @return array{title: string, body: string, ticket_code: string, status: string, event: string, action_url: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title(),
            'body' => $this->body($this->plainReason()),
            'ticket_code' => $this->ticketCode,
            'status' => $this->status->value,
            'event' => $this->event->value,
            'action_url' => $this->actionUrl(),
        ];
    }

    private function title(): string
    {
        return (string) __('fixcity::ticket_notification.lifecycle.'.$this->event->value.'.title');
    }

    private function body(string $reason): string
    {
        $key = 'fixcity::ticket_notification.lifecycle.'.$this->event->value;
        $replace = ['ticket_code' => $this->ticketCode, 'status' => $this->status->getLabel()];

        if ($this->event === TicketLifecycleEventEnum::Rejected && $reason !== '') {
            return (string) __($key.'.body_with_reason', $replace + ['reason' => $reason]);
        }

        return (string) __($key.'.body', $replace);
    }

    private function actionUrl(): string
    {
        return app(BuildTicketTrackUrlAction::class)->execute($this->ticketCode);
    }

    /** Motivo scritto dall'operatore: una riga, al massimo {@see self::REASON_LIMIT} caratteri. Solo per lo scarto. */
    private function plainReason(): string
    {
        if ($this->event !== TicketLifecycleEventEnum::Rejected || $this->reason === null) {
            return '';
        }

        return Str::of($this->reason)->squish()->limit(self::REASON_LIMIT)->toString();
    }

    /**
     * La mail e' Markdown: `<`, `>` e `&` li escapa gia' il template ({{ }}), ma `[testo](url)` e `![](url)` diventerebbero un link o
     * un'immagine con un testo scelto da chi scrive. Il backslash li rende testo. Un URL scritto per esteso resta un URL leggibile.
     */
    private function markdownSafe(string $text): string
    {
        return preg_replace('/[\\\\`*_{}\[\]()#+\-.!|~]/', '\\\\$0', $text);
    }
}
