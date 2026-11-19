<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Policies\BasePolicy;
use Modules\Xot\Datas\XotData;
use Spatie\QueueableAction\QueueableAction;

/**
 * Utenti a cui si puo' assegnare una segnalazione (DCP-281, A-14): quelli con un ruolo PA.
 *
 * I ruoli sono `BasePolicy::PA_ROLES`, la stessa lista di `isPaOperator`, cosi' chi puo' operare sui ticket e chi si puo'
 * scegliere come responsabile non divergono. La selezione e' per nome del ruolo (`whereHas`) e non con lo scope `role()` di
 * Spatie, che lancia `RoleDoesNotExist` se un ruolo della lista non e' ancora una riga.
 */
final class ListAssignableTicketOperatorsAction
{
    use QueueableAction;

    /**
     * @return array<int|string, string> id utente => nome, in ordine di nome
     */
    public function execute(): array
    {
        $userClass = XotData::make()->getUserClass();

        return $userClass::query()
            ->whereHas('roles', static fn (Builder $roles): Builder => $roles->whereIn('name', BasePolicy::PA_ROLES))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->map(static fn (mixed $name): string => is_scalar($name) ? (string) $name : '')
            ->all();
    }
}
