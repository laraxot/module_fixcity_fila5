<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Segna la segnalazione come risolta (motivo obbligatorio).
 * La chiusura formale è un passo successivo ({@see CloseTicketAction}).
 */
final class ResolveTicketAction
{
    use QueueableAction;

    public function execute(Ticket $ticket, string $reason): void
    {
        Gate::authorize('changeStatus', $ticket);

        $reason = trim($reason);
        if ($reason === '') {
            throw new \InvalidArgumentException('La motivazione di risoluzione è obbligatoria.');
        }

        app(ChangeStatus::class)->execute($ticket, TicketStatusEnum::RESOLVED->value, $reason);
    }
}
