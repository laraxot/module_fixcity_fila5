<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Fixcity\Actions\GetTicketSlaMetricsAction;

/**
 * SLA / tempi medi risoluzione (STORY-041).
 */
class TicketSlaOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $sla = app(GetTicketSlaMetricsAction::class)->execute();

        $avgLabel = $sla['avg_resolution_hours'] !== null
            ? $sla['avg_resolution_hours'].' h'
            : '—';

        return [
            Stat::make(__('fixcity::ticket_kpi.sla.avg_hours.label'), $avgLabel)
                ->description(__('fixcity::ticket_kpi.sla.avg_hours.description.label'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make(__('fixcity::ticket_kpi.sla.resolved_total.label'), (string) $sla['resolved_count'])
                ->description(__('fixcity::ticket_kpi.sla.resolved_total.description.label'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make(__('fixcity::ticket_kpi.sla.resolved_30d.label'), (string) $sla['resolved_last_30_days'])
                ->description(__('fixcity::ticket_kpi.sla.resolved_30d.description.label'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
