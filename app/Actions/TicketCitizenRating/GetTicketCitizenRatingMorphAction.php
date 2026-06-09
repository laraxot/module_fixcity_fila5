<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\TicketCitizenRating;

use Modules\Fixcity\Models\Ticket;
use Modules\Rating\Models\RatingMorph;

final class GetTicketCitizenRatingMorphAction
{
    public function executeForTicket(Ticket $ticket, ?string $userId = null): ?RatingMorph
    {
        $definition = app(EnsureTicketCitizenRatingDefinitionAction::class)->execute();

        $query = RatingMorph::query()
            ->where('rating_id', $definition->id)
            ->where('model_type', $ticket->getMorphClass())
            ->where('model_id', $ticket->getKey())
            ->whereNotNull('user_id');

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        $morph = $query->first();

        return $morph instanceof RatingMorph ? $morph : null;
    }
}
