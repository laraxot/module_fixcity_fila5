<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

/**
 * Consegna una notifica di ticket a un destinatario nella SUA lingua (DCP-284). Un guasto di consegna si registra (solo id e
 * classi, nessun dato personale) e non interrompe l'azione dell'operatore o del cittadino che l'ha provocata: la riga di audit
 * e' gia' scritta e la notifica e' accodata dopo il commit.
 */
final class SendTicketNotificationAction
{
    use QueueableAction;

    public function execute(UserContract $recipient, Notification $notification, int $ticketId): void
    {
        if (! method_exists($recipient, 'notify')) {
            return;
        }

        $notification->locale(app(ResolveNotificationLocaleAction::class)->execute($recipient));

        try {
            NotificationFacade::send($recipient, $notification);
        } catch (Throwable $exception) {
            Log::error('Fixcity ticket notification delivery failed.', [
                'ticket_id' => $ticketId,
                'recipient_id' => SafeStringCastAction::cast($recipient->getAuthIdentifier()),
                'notification' => $notification::class,
                'exception' => $exception::class,
            ]);
        }
    }
}
