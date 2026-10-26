<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

/**
 * Ricevuta al proprietario appena la segnalazione e' creata, con la notifica di stato esistente
 * (database sempre, email se l'indirizzo e' verificato e il cittadino non ha disattivato le email).
 * Lo stato iniziale (PENDING) e' interno, quindi NotifyTicketSubscribersOfStatusChangeAction non lo notifica mai.
 */
final class SendTicketReceiptAction
{
    use QueueableAction;

    public function execute(Ticket $ticket): void
    {
        $owner = $this->owner($ticket);
        $notification = $this->notification($ticket);
        if (! $owner instanceof UserContract || ! $notification instanceof TicketStatusChangedNotification) {
            return;
        }

        try {
            Notification::send($owner, $notification);
        } catch (Throwable $exception) {
            Log::error('Fixcity ticket receipt delivery failed.', [
                'ticket_id' => $ticket->id,
                'exception' => $exception::class,
            ]);
        }
    }

    /**
     * Indirizzo a cui la ricevuta va per email; stringa vuota se la ricevuta non parte per email.
     */
    public function mailedTo(Ticket $ticket): string
    {
        $owner = $this->owner($ticket);
        $notification = $this->notification($ticket);
        if (! $owner instanceof UserContract || ! $notification instanceof TicketStatusChangedNotification) {
            return '';
        }

        return in_array('mail', $notification->via($owner), true)
            ? SafeStringCastAction::cast($owner->email)
            : '';
    }

    private function owner(Ticket $ticket): ?UserContract
    {
        $owner = $ticket->owner;

        return $owner instanceof UserContract ? $owner : null;
    }

    private function notification(Ticket $ticket): ?TicketStatusChangedNotification
    {
        $code = trim(SafeStringCastAction::cast($ticket->code));
        if ($code === '') {
            return null;
        }

        $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;

        return new TicketStatusChangedNotification($code, $status);
    }
}
