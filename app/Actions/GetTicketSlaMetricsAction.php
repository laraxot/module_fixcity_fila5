<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;

/**
 * Tempi medi risoluzione ticket (STORY-041 / FR-020).
 */
final class GetTicketSlaMetricsAction
{
    /**
     * @return array{
     *     resolved_count: int,
     *     avg_resolution_hours: float|null,
     *     resolved_last_30_days: int
     * }
     */
    public function execute(): array
    {
        $resolvedStatuses = [
            TicketStatusEnum::RESOLVED->value,
            TicketStatusEnum::CLOSED->value,
        ];

        $table = (new Ticket)->getTable();

        $resolvedCount = (int) DB::table($table)
            ->whereIn('status', $resolvedStatuses)
            ->count();

        if ($resolvedCount === 0) {
            return [
                'resolved_count' => 0,
                'avg_resolution_hours' => null,
                'resolved_last_30_days' => 0,
            ];
        }

        $driver = DB::connection()->getDriverName();
        $avgHours = $this->averageResolutionHours($table, $resolvedStatuses, $driver);

        $resolvedLast30 = (int) DB::table($table)
            ->whereIn('status', $resolvedStatuses)
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->count();

        return [
            'resolved_count' => $resolvedCount,
            'avg_resolution_hours' => $avgHours !== null ? round($avgHours, 1) : null,
            'resolved_last_30_days' => $resolvedLast30,
        ];
    }

    /**
     * @param  list<string>  $resolvedStatuses
     */
    private function averageResolutionHours(string $table, array $resolvedStatuses, string $driver): ?float
    {
        $baseQuery = DB::table($table)->whereIn('status', $resolvedStatuses);

        if ($driver === 'sqlite') {
            $avgSeconds = $baseQuery
                ->selectRaw('avg((julianday(updated_at) - julianday(created_at)) * 86400) as avg_seconds')
                ->value('avg_seconds');
        } else {
            $avgSeconds = $baseQuery
                ->selectRaw('avg(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_seconds')
                ->value('avg_seconds');
        }

        if ($avgSeconds === null) {
            return null;
        }

        return ((float) $avgSeconds) / 3600;
    }
}
