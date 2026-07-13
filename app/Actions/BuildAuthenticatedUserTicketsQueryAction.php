<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Query ticket/pratiche del cittadino autenticato — area personale "Pratiche".
 */
final class BuildAuthenticatedUserTicketsQueryAction
{
    use QueueableAction;

    /**
     * @return Builder<Ticket>
     */
    public function execute(): Builder
    {
        $currentUserId = auth()->id();

        return Ticket::query()
            ->where('created_by', $currentUserId)
            ->orderByDesc('created_at');
    }
}
