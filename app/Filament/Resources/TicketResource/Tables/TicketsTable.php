<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Tables;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Actions\TicketCitizenRating\GetTicketIdsWithCitizenRatingAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

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
}
