<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Actions\DeleteTicketAction;
use Modules\Fixcity\Actions\TicketCitizenRating\GetTicketIdsWithCitizenRatingAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Exports\TicketExporter;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Datas\XotData;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;
use Webmozart\Assert\Assert;

/**
 * TicketsTable Schema - XotBaseResourceTable Zen Pattern.
 *
 * **Zen Philosophy**: No `configure()` override - XotBaseResourceTable base class handles table setup.
 * Subclass only provides `getTable*()` methods.
 *
 * **Architecture**:
 * - Columns: ID, name, status, priority, type, owner, assignee, dates
 * - Filters: Status, priority, type
 * - Auto-label via LangServiceProvider (NO `->label()` calls)
 *
 * @see XotBaseResourceTable
 */
class TicketsTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'id' => TextColumn::make('id')->sortable(),
            'name' => TextColumn::make('name')->searchable()->sortable()->limit(50),
            'status' => TextColumn::make('status')->badge()->sortable(),
            'priority' => TextColumn::make('priority')->badge()->sortable(),
            'type' => TextColumn::make('type')->badge()->placeholder('-'),
            'owner.name' => TextColumn::make('owner.name')->placeholder('-'),
            'assignee.name' => TextColumn::make('assignee.name')->placeholder('-'),
            'citizen_rating' => TextColumn::make('citizen_rating')
                ->sortable()
                ->placeholder('-')
                ->formatStateUsing(static fn (?int $state): string => $state !== null ? $state.'/5' : '-'),
            'citizen_rated_at' => TextColumn::make('citizen_rated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            'created_at' => TextColumn::make('created_at')->dateTime()->sortable(),
            'updated_at' => TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @return array<string, BaseFilter>
     */
    public function getTableFilters(): array
    {
        return [
            'status' => SelectFilter::make('status')->options(TicketStatusEnum::class),
            'priority' => SelectFilter::make('priority')->options(TicketPriorityEnum::class),
            'type' => SelectFilter::make('type')->options(TicketTypeEnum::class)->native(false),
            'responsible_id' => SelectFilter::make('responsible_id')
                ->options(static function (): array {
                    $userClass = XotData::make()->getUserClass();

                    return $userClass::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->map(static fn (mixed $name): string => SafeStringCastAction::cast($name))
                        ->all();
                })
                ->searchable()
                ->preload(),
            'assignment_status' => Filter::make('assignment_status')
                ->schema([
                    Select::make('assignment_status')->options([
                        'assigned' => __('fixcity::ticket_filter.assignment_status.assigned'),
                        'unassigned' => __('fixcity::ticket_filter.assignment_status.unassigned'),
                    ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $assignmentStatus = $data['assignment_status'] ?? null;

                    if ($assignmentStatus === 'assigned') {
                        return $query->whereNotNull('responsible_id');
                    }

                    if ($assignmentStatus === 'unassigned') {
                        return $query->whereNull('responsible_id');
                    }

                    return $query;
                }),
            'created_at' => Filter::make('created_at')
                ->schema([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (isset($data['created_from']) && is_string($data['created_from']) && $data['created_from'] !== '') {
                        $query->whereDate('created_at', '>=', $data['created_from']);
                    }

                    if (isset($data['created_until']) && is_string($data['created_until']) && $data['created_until'] !== '') {
                        $query->whereDate('created_at', '<=', $data['created_until']);
                    }

                    return $query;
                })
                ->columns(2),
            'has_citizen_rating' => TernaryFilter::make('has_citizen_rating')
                ->queries(
                    true: static function (Builder $query): Builder {
                        $ids = app(GetTicketIdsWithCitizenRatingAction::class)->execute();
                        if ($ids->isEmpty()) {
                            return $query->whereRaw('1 = 0');
                        }

                        return $query->whereIn('id', $ids->all());
                    },
                    false: static function (Builder $query): Builder {
                        $ids = app(GetTicketIdsWithCitizenRatingAction::class)->execute();
                        if ($ids->isEmpty()) {
                            return $query;
                        }

                        return $query->whereNotIn('id', $ids->all());
                    },
                ),
        ];
    }

    /**
     * @return array<string, BulkAction>
     */
    public function getTableBulkActions(): array
    {
        return [
            'delete' => BulkAction::make('delete')
                ->authorize('deleteAny')
                ->fetchSelectedRecords()
                ->label('')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(static function (Collection $records): void {
                    DB::connection((new Ticket)->getConnectionName())->transaction(
                        static function () use ($records): void {
                            $records->each(static function (mixed $record): void {
                                Assert::isInstanceOf($record, Model::class);
                                Assert::isInstanceOf($record, Ticket::class);
                                app(DeleteTicketAction::class)->execute($record);
                            });
                        }
                    );
                }),
            'export' => ExportBulkAction::make()
                ->exporter(TicketExporter::class),
        ];
    }
}
