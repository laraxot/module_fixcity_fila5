<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Str;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Genera un codice univoco di tracciamento (capability token pubblico).
 *
 * Il controllo include le segnalazioni cancellate (soft delete): il loro codice resta occupato. La garanzia finale e' l'indice
 * unique su `tickets.code` (DB-04) con il nuovo tentativo in CreateTicketAction.
 */
final class AllocateTicketCodeAction
{
    use QueueableAction;

    public function execute(?string $prefix = 'TCK'): string
    {
        $safePrefix = $prefix !== null && $prefix !== '' ? strtoupper($prefix) : 'TCK';

        do {
            $code = $safePrefix.'-'.strtoupper(Str::random(16));
        } while (Ticket::withTrashed()->where('code', $code)->exists());

        return $code;
    }
}
