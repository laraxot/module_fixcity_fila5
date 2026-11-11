<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dice a chi va per email la ricevuta della segnalazione appena creata (pagina di conferma).
 *
 * La ricevuta e' UNA sola: la `TicketLifecycleNotification` Received che {@see NotifyTicketReceivedAction} invia dopo
 * {@see TicketCreatedEvent}, nella lingua del destinatario. Prima questa classe ne inviava una seconda
 * (`TicketStatusChangedNotification` pending): due email e due notifiche per ogni segnalazione (DB-05). Ora legge solo i canali
 * della notifica che parte davvero.
 */
final class SendTicketReceiptAction
{
    use QueueableAction;

    /**
     * Indirizzo a cui la ricevuta va per email; stringa vuota se la ricevuta non parte per email.
     */
    public function mailedTo(Ticket $ticket): string
    {
        $owner = $ticket->owner;
        $code = trim(SafeStringCastAction::cast($ticket->code));
        if (! $owner instanceof UserContract || $code === '') {
            return '';
        }

        $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;
        $notification = new TicketLifecycleNotification($code, TicketLifecycleEventEnum::Received, $status);

        return in_array('mail', $notification->via($owner), true)
            ? SafeStringCastAction::cast($owner->email)
            : '';
    }
}
