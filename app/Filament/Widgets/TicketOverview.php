<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Fixcity\Actions\GetTicketKpiAggregateAction;

/**
 * KPI volumi ticket — dashboard e analytics PA (STORY-025 / STORY-040).
 */
class TicketOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $kpi = app(GetTicketKpiAggregateAction::class)->execute();

        return [
            Stat::make(__('fixcity::ticket_kpi.stats.total.label'), (string) $kpi['total'])
                ->description(__('fixcity::ticket_kpi.stats.total.description.label'))
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),

            Stat::make(__('fixcity::ticket_kpi.stats.backlog.label'), (string) $kpi['backlog'])
                ->description(__('fixcity::ticket_kpi.stats.backlog.description.label'))
                ->descriptionIcon('heroicon-m-inbox-stack')
                ->color('danger'),

            Stat::make(__('fixcity::ticket_kpi.stats.in_progress.label'), (string) $kpi['in_progress'])
                ->description(__('fixcity::ticket_kpi.stats.in_progress.description.label'))
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make(__('fixcity::ticket_kpi.stats.resolved.label'), (string) $kpi['resolved'])
                ->description(__('fixcity::ticket_kpi.stats.resolved.description.label'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
