<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Tables;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

/**
 * TicketsTable Schema - XotBaseResourceTable Zen Pattern.
 *
 * **Zen Philosophy**: No `configure()` override - XotBaseResourceTable base class handles table setup.
 * Subclass only provides static `getTable*()` methods.
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
    public static function getTableColumns(): array
    {
        return [
            'id' => TextColumn::make('id')->sortable(),
            'name' => TextColumn::make('name')->searchable()->sortable()->limit(50),
            'status' => TextColumn::make('status')->badge()->sortable(),
            'priority' => TextColumn::make('priority')->badge()->sortable(),
            'type' => TextColumn::make('type')->badge()->placeholder('-'),
            'owner.name' => TextColumn::make('owner.name')->placeholder('-'),
            'assignee.name' => TextColumn::make('assignee.name')->placeholder('-'),
            'created_at' => TextColumn::make('created_at')->dateTime()->sortable(),
            'updated_at' => TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @return array<string, BaseFilter>
     */
    public static function getTableFilters(): array
    {
        return [
            'status' => SelectFilter::make('status')->options(TicketStatusEnum::class),
            'priority' => SelectFilter::make('priority')->options(TicketPriorityEnum::class),
            'type' => SelectFilter::make('type')->options(TicketTypeEnum::class)->native(false),
        ];
    }
}
