<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\TicketCitizenRating;

use Illuminate\Support\Collection;
use Modules\Fixcity\Models\Ticket;
use Modules\Rating\Models\RatingMorph;
use Spatie\QueueableAction\QueueableAction;

final class GetTicketIdsWithCitizenRatingAction
{
    use QueueableAction;

    /**
     * @return Collection<int, int|string>
     */
    public function execute(): Collection
    {
        $definition = app(EnsureTicketCitizenRatingDefinitionAction::class)->execute();

        /** @var Collection<int, int|string> $ids */
        $ids = RatingMorph::query()
            ->where('rating_id', $definition->id)
            ->where('model_type', (new Ticket)->getMorphClass())
            ->whereNotNull('user_id')
            ->whereNotNull('value')
            ->pluck('model_id');

        return $ids;
    }
}
