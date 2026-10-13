<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati della pagina di conferma post-create: il flash di sessione scritto da CreateTicketAction (letto una volta sola) oppure, se manca
 * (refresh, link salvato), il ticket indicato dal codice di tracciamento. Senza nessuno dei due restituisce un array vuoto.
 * Il codice e' un capability token: nel ripiego l'email del proprietario la vede solo il proprietario autenticato (chi ha appena creato il ticket la vede dal flash).
 */
final class ResolveTicketConfirmationDataAction
{
    use QueueableAction;

    /**
     * @return array<string, mixed>
     */
    public function execute(?string $code = null): array
    {
        $builder = app(BuildTicketConfirmationDataAction::class);

        $data = $builder->pullFromSession();
        if ($data !== []) {
            return $data;
        }

        $ticket = app(GetPublicTicketByCodeAction::class)->execute($code);

        if (! $ticket instanceof Ticket) {
            return [];
        }

        $data = $builder->execute($ticket);
        $viewerId = auth()->id();
        $isOwner = $viewerId !== null && (string) $viewerId === (string) $ticket->owner_id;

        return $isOwner ? $data : [...$data, 'email' => ''];
    }
}
