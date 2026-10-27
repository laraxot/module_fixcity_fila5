<?php

declare(strict_types=1);

namespace Modules\Fixcity\Notifications\Concerns;

use Illuminate\Bus\Queueable;
use Modules\Fixcity\Actions\CanUserReceiveTicketMailAction;

/**
 * Canali e riprova delle notifiche di ticket (DCP-284): `database` sempre; `mail` solo con email verificata, non vuota e
 * preferenza `email_ticket_updates` non disattivata. E' la stessa regola di `TicketStatusChangedNotification` e
 * `TicketAssignedNotification`, scritta una volta per le notifiche nuove. Da usare insieme a `ShouldQueueAfterCommit`.
 */
trait DeliversByDatabaseAndVerifiedMail
{
    use Queueable;

    public int $tries = 3;

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300];
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // la regola e' scritta una volta sola (anche la pagina di conferma della segnalazione la usa per dire se partira' una email)
        if (app(CanUserReceiveTicketMailAction::class)->execute($notifiable)) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
