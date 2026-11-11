<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Models\Ticket;
use Modules\Media\Models\Media;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Chi puo' scaricare un allegato di un ticket (SEC-04, DB-19).
 *
 * - Foto (`image/*`): chi puo' vedere il ticket (`TicketPolicy::view`: ticket pubblico, proprietario, operatore PA).
 * - Altri file (PDF): solo il proprietario e chi puo' vedere l'elenco dei ticket nel back office (`TicketPolicy::viewAny`).
 *   Un PDF del cittadino (documenti, ricevute) non e' mai pubblico, nemmeno quando il ticket lo e'.
 */
final class CanViewTicketMediaAction
{
    use QueueableAction;

    public function execute(Media $media, Ticket $ticket, ?Authenticatable $viewer = null): bool
    {
        if (! $this->belongsTo($media, $ticket)) {
            return false;
        }

        $gate = Gate::forUser($viewer);
        if ($gate->denies('view', $ticket)) {
            return false;
        }

        if (str_starts_with(SafeStringCastAction::cast($media->mime_type), 'image/')) {
            return true;
        }

        return $this->isOwner($ticket, $viewer) || ($viewer !== null && $gate->allows('viewAny', Ticket::class));
    }

    public function belongsTo(Media $media, Ticket $ticket): bool
    {
        $ticketMorph = $ticket->getMorphClass();

        return in_array($media->model_type, [Ticket::class, $ticketMorph], true)
            && SafeStringCastAction::cast($media->model_id) === SafeStringCastAction::cast($ticket->getKey());
    }

    private function isOwner(Ticket $ticket, ?Authenticatable $viewer): bool
    {
        if ($viewer === null || $ticket->owner_id === null) {
            return false;
        }

        return SafeStringCastAction::cast($ticket->owner_id) === SafeStringCastAction::cast($viewer->getAuthIdentifier());
    }
}
