<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\User\Models\Notification;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Notifiche di stato ticket del solo cittadino indicato (DCP-111).
 *
 * Sorgente: la tabella `notifications` (canale `database` di {@see TicketStatusChangedNotification}); nessuna tabella nuova.
 * E' l'unico punto che decide "di chi e di che tipo": elenco e "segna come letta" passano di qui, cosi' non si puo'
 * leggere o toccare la notifica di un altro utente (ne' di un altro tipo, es. l'assegnazione agli operatori).
 */
final class BuildAuthenticatedUserTicketNotificationsQueryAction
{
    use QueueableAction;

    /** @return Builder<Notification> */
    public function execute(UserContract $user): Builder
    {
        return Notification::query()
            ->where('notifiable_type', $user->getMorphClass())
            ->where('notifiable_id', $user->getKey())
            ->where('type', TicketStatusChangedNotification::class);
    }
}
