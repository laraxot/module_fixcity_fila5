<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Recupera un ticket tramite codice di tracciamento.
 *
 * Il codice è un capability token: chi lo conosce può vedere stato e timeline
 * pubblica minima, anche se lo stato non è ancora in canViewByAll (es. PENDING).
 */
final class GetPublicTicketByCodeAction
{
    use QueueableAction;

    public function execute(?string $code = null): ?Ticket
    {
        if ($code === null || trim($code) === '') {
            return null;
        }

        return Ticket::query()
            ->where('code', trim($code))
            ->first();
    }
}
