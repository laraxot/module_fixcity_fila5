<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Query ticket/pratiche del cittadino autenticato — area personale "Pratiche".
 */
final class BuildAuthenticatedUserTicketsQueryAction
{
    use QueueableAction;

    /**
     * Senza utente esplicito vale quello autenticato; senza nessuno dei due la query e' VUOTA
     * (`where owner_id = null` diventerebbe `IS NULL` e mostrerebbe i ticket senza proprietario).
     *
     * @return Builder<Ticket>
     */
    public function execute(?UserContract $user = null): Builder
    {
        $ownerId = $user?->getAuthIdentifier() ?? auth()->id();

        if ($ownerId === null) {
            return Ticket::query()->whereKey([]);
        }

        return Ticket::query()
            ->where('owner_id', $ownerId)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
