<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Support\SpreadsheetCellSanitizer;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Export CSV/XLSX ticket per backoffice PA (STORY-024 / FR-024).
 */
final class TicketExporter extends Exporter
{
    protected static ?string $model = Ticket::class;

    /**
     * @return array<ExportColumn>
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('name')
                ->formatStateUsing(static fn (?string $state): string => SpreadsheetCellSanitizer::sanitize($state)),
            ExportColumn::make('status')
                ->formatStateUsing(static fn ($state): string => SpreadsheetCellSanitizer::sanitize(
                    $state instanceof \BackedEnum ? $state->value : SafeStringCastAction::cast($state),
                )),
            ExportColumn::make('priority')
                ->formatStateUsing(static fn ($state): string => SpreadsheetCellSanitizer::sanitize(
                    $state instanceof \BackedEnum ? $state->value : SafeStringCastAction::cast($state),
                )),
            ExportColumn::make('type')
                ->formatStateUsing(static fn ($state): string => SpreadsheetCellSanitizer::sanitize(
                    $state instanceof \BackedEnum ? $state->value : SafeStringCastAction::cast($state),
                )),
            ExportColumn::make('owner.name')
                ->formatStateUsing(static fn (?string $state): string => SpreadsheetCellSanitizer::sanitize($state)),
            ExportColumn::make('assignee.name')
                ->formatStateUsing(static fn (?string $state): string => SpreadsheetCellSanitizer::sanitize($state)),
            ExportColumn::make('citizen_rating'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Esportazione ticket completata: '.number_format($export->successful_rows).' righe.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' righe non esportate.';
        }

        return $body;
    }

    /**
     * @return array<ExportFormat>
     */
    public function getFormats(): array
    {
        return [
            ExportFormat::Csv,
            ExportFormat::Xlsx,
        ];
    }
}
