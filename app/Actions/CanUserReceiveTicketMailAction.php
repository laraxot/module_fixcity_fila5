<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Le notifiche di ticket arrivano anche per email a questo utente? (DCP-286 sulla regola di DCP-284.)
 *
 * Vero solo con email verificata, non vuota e preferenza `email_ticket_updates` non disattivata. E' l'unica scrittura della regola:
 * la usano i canali delle notifiche ({@see \Modules\Fixcity\Notifications\Concerns\DeliversByDatabaseAndVerifiedMail}) e la pagina di
 * conferma, cosi' il testo "ti scriveremo" non puo' divergere da quello che parte davvero.
 */
final class CanUserReceiveTicketMailAction
{
    use QueueableAction;

    public function execute(object $notifiable): bool
    {
        return $notifiable instanceof UserContract
            && $notifiable->hasVerifiedEmail()
            && is_string($notifiable->email)
            && trim($notifiable->email) !== ''
            && app(GetFixcityEmailNotificationPreferenceAction::class)->execute($notifiable);
    }
}
