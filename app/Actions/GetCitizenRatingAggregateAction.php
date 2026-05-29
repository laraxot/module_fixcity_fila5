<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;

/**
 * KPI aggregati valutazioni cittadino su ticket risolti (STORY-044 / FR-021).
 */
final class GetCitizenRatingAggregateAction
{
    /**
     * @return array{count: int, average: float|null}
     */
    public function execute(): array
    {
        $count = Ticket::query()->whereNotNull('citizen_rating')->count();
        if ($count === 0) {
            return ['count' => 0, 'average' => null];
        }

        $average = Ticket::query()->whereNotNull('citizen_rating')->avg('citizen_rating');

        return [
            'count' => $count,
            'average' => round((float) $average, 1),
        ];
    }
}
