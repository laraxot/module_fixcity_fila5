<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Modules\Fixcity\Actions\GenerateTicketsJsonAction;
use Modules\Fixcity\Filament\Exports\TicketExporter;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\Tables\TicketsTable;
use Modules\Fixcity\Filament\Widgets\CitizenRatingOverviewWidget;
use Modules\Fixcity\Filament\Widgets\TicketOverview;
use Modules\Fixcity\Filament\Widgets\TicketSlaOverviewWidget;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

/**
 * Lista ticket admin — tabella Xot + export CSV (STORY-024) + KPI header (STORY-025).
 */
class ListTickets extends XotBaseListRecords
{
    protected static string $resource = TicketResource::class;

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            TicketOverview::class,
            TicketSlaOverviewWidget::class,
            CitizenRatingOverviewWidget::class,
        ];
    }

    /**
     * @return array<string, Action|ActionGroup>
     */
    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            'create' => CreateAction::make(),
            'export' => ExportAction::make()
                ->exporter(TicketExporter::class)
                ->label(__('fixcity::ticket_kpi.export.header_action.label')),
            'export_map_json' => Action::make('export_map_json')
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

    /**
     * @return array<string, Column>
     */
    #[\Override]
    public function getTableColumns(): array
    {
        return (new TicketsTable)->getTableColumns();
    }

    /**
     * @return array<string, BaseFilter>
     */
    #[\Override]
    public function getTableFilters(): array
    {
        return (new TicketsTable)->getTableFilters();
    }

    /**
     * @return array<string, BulkAction>
     */
    #[\Override]
    public function getTableBulkActions(): array
    {
        return [
            'delete' => DeleteBulkAction::make()
                ->label('')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation(),
            'export' => ExportBulkAction::make()
                ->exporter(TicketExporter::class),
        ];
    }
}
