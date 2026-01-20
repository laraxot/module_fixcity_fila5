<?php

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Fixcity\Models\Ticket;

class TicketOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Segnalazioni Totali', Ticket::count())
                ->description('Tutte le segnalazioni')
                ->descriptionIcon('heroicon-m-ticket')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

            Stat::make('In Attesa', Ticket::where('status', 'open')->count())
                ->description('Segnalazioni da gestire')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),

            Stat::make('Risolte', Ticket::where('status', 'resolved')->count())
                ->description('Segnalazioni completate')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
