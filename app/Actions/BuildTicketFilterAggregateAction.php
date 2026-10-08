<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

/**
 * Compatibility action for existing ticket-list consumers.
 *
 * Public map and filter data share one live database aggregate. Keep this
 * established action name as a thin adapter instead of maintaining a second,
 * static GeoJSON implementation.
 */
class BuildTicketFilterAggregateAction
{
    use QueueableAction;

    /**
     * @return array{
     *     features: array<int, array<string, mixed>>,
     *     countsPerType: array<string, int>,
     *     uniqueTypes: array<int, array<string, mixed>>,
     *     countsPerStatus: array<string, int>,
     *     uniqueStatuses: array<int, array<string, mixed>>,
     *     totalCount: int
     * }
     */
    public function execute(): array
    {
        return app(BuildSegnalazioniFilterAggregateAction::class)->execute();
    }
}
