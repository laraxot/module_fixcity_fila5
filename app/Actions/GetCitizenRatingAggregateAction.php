<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Actions\TicketCitizenRating\EnsureTicketCitizenRatingDefinitionAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Rating\Models\RatingMorph;
use Spatie\QueueableAction\QueueableAction;

/**
 * KPI aggregati valutazioni cittadino — query su RatingMorph (modulo Rating).
 */
final class GetCitizenRatingAggregateAction
{
    use QueueableAction;

    /**
     * @return array{count: int, average: float|null}
     */
    public function execute(): array
    {
        $definition = app(EnsureTicketCitizenRatingDefinitionAction::class)->execute();

        $query = RatingMorph::query()
            ->where('rating_id', $definition->id)
            ->where('model_type', (new Ticket)->getMorphClass())
            ->whereNotNull('user_id')
            ->whereNotNull('value');

        $count = $query->count();
        if ($count === 0) {
            return ['count' => 0, 'average' => null];
        }

        $average = (clone $query)->avg('value');

        return [
            'count' => $count,
            'average' => round((float) $average, 1),
        ];
    }
}
