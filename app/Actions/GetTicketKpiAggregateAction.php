<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * KPI volumi ticket per dashboard Filament (STORY-025 / STORY-040 / FR-020).
 */
final class GetTicketKpiAggregateAction
{
    use QueueableAction;

    /**
     * @return array{
     *     total: int,
     *     backlog: int,
     *     in_progress: int,
     *     resolved: int,
     *     by_status: array<string, int>
     * }
     */
    public function execute(): array
    {
        $rows = DB::table((new Ticket)->getTable())
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->get();

        /** @var array<string, int> $byStatus */
        $byStatus = [];
        foreach ($rows as $row) {
            /** @var object{status: string|null, aggregate: int|string} $row */
            $byStatus[(string) ($row->status ?? '')] = (int) $row->aggregate;
        }

        $backlogStatuses = [
            TicketStatusEnum::OPEN->value,
            TicketStatusEnum::PENDING->value,
            TicketStatusEnum::IN_REVIEW->value,
            TicketStatusEnum::IN_PROGRESS->value,
            TicketStatusEnum::REOPENED->value,
            TicketStatusEnum::DRAFT->value,
            TicketStatusEnum::ON_HOLD->value,
        ];

        $resolvedStatuses = [
            TicketStatusEnum::RESOLVED->value,
            TicketStatusEnum::CLOSED->value,
        ];

        $backlog = 0;
        foreach ($backlogStatuses as $status) {
            $backlog += $byStatus[$status] ?? 0;
        }

        $resolved = 0;
        foreach ($resolvedStatuses as $status) {
            $resolved += $byStatus[$status] ?? 0;
        }

        return [
            'total' => array_sum($byStatus),
            'backlog' => $backlog,
            'in_progress' => $byStatus[TicketStatusEnum::IN_PROGRESS->value] ?? 0,
            'resolved' => $resolved,
            'by_status' => $byStatus,
        ];
    }
}
