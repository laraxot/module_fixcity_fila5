<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Fixcity\Actions\GetCitizenRatingAggregateAction;

/**
 * Aggregato valutazioni cittadino in backoffice (STORY-044).
 */
class CitizenRatingOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $aggregate = app(GetCitizenRatingAggregateAction::class)->execute();

        $averageLabel = $aggregate['average'] !== null
            ? (string) $aggregate['average'].'/5'
            : '—';

        return [
            Stat::make(__('fixcity::ticket_citizen_rating.admin.stats.average.label'), $averageLabel)
                ->description(__('fixcity::ticket_citizen_rating.admin.stats.average.description.label'))
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make(__('fixcity::ticket_citizen_rating.admin.stats.count.label'), (string) $aggregate['count'])
                ->description(__('fixcity::ticket_citizen_rating.admin.stats.count.description.label'))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
        ];
    }
}
