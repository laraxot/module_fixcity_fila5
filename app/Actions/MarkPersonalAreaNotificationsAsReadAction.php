<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * "Segna come letta" dell'area personale (DCP-111): scrive solo `read_at` (colonna gia' presente) e solo sulle notifiche
 * di stato del cittadino (query condivisa con l'elenco). Un id altrui o inesistente non cambia nulla e restituisce 0.
 */
final class MarkPersonalAreaNotificationsAsReadAction
{
    use QueueableAction;

    /**
     * @param  string|null  $notificationId  `null` = tutte le notifiche non lette del cittadino
     * @return int numero di notifiche segnate come lette
     */
    public function execute(UserContract $user, ?string $notificationId = null): int
    {
        $query = app(BuildAuthenticatedUserTicketNotificationsQueryAction::class)->execute($user)->whereNull('read_at');
        if ($notificationId !== null) {
            $query->whereKey($notificationId);
        }

        return $query->update(['read_at' => now()]);
    }
}
