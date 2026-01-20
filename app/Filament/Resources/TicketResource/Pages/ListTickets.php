<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Modules\Fixcity\Filament\Resources\TicketResource;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable(),
            TextColumn::make('title')
                ->searchable(),
            TextColumn::make('status')
                ->badge()
                ->colors([
                    'danger' => 'open',
                    'warning' => 'in_progress',
                    'success' => 'resolved',
                    'secondary' => 'closed',
                ]),
            TextColumn::make('priority')
                ->badge()
                ->colors([
                    'secondary' => 'low',
                    'primary' => 'medium',
                    'warning' => 'high',
                    'danger' => 'critical',
                ]),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
