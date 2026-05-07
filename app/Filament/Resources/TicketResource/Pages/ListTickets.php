<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Modules\Fixcity\Actions\GenerateTicketsJsonAction;
use Modules\Fixcity\Filament\Resources\TicketResource;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('export_map_json')
                ->icon('heroicon-o-map')
                ->color('success')
                ->action(function (): void {
                    $path = app(GenerateTicketsJsonAction::class)->execute();
                    Notification::make()
                        ->success()
                        ->title('JSON mappa generato')
                        ->body('File scritto in '.basename(dirname($path)).'/'.basename($path))
                        ->send();
                }),
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
