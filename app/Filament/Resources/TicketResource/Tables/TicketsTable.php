<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

/**
 * Tickets Table Schema - Filament v5 Hybrid Pattern.
 *
 * **Pattern**: Extends XotBaseResourceTable with dual API support:
 * - `configure(Table $table): Table` - Filament v5 fluent API (NEW)
 * - `table(Table $table): Table` - Legacy API (backward compatibility)
 *
 * **Architecture**:
 * - Columns: ID, name, status, priority, type, owner, assignee, dates
 * - Filters: Status, priority, type
 * - Actions: Edit, Delete (standard Filament)
 * - Auto-label via LangServiceProvider (NO `->label()` calls)
 *
 * @see https://github.com/filamentphp/demo/blob/5.x/app/Filament/Resources/HR/Departments/Tables/DepartmentsTable.php
 * @see \Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable
 */
class TicketsTable extends XotBaseResourceTable
{
    /**
     * Filament v5 style: Fluent Table configuration.
     *
     * **Philosophy**: Use fluent API for table structure.
     * **NO ->label() calls**: LangServiceProvider auto-resolves translations.
     *
     * @see https://filamentphp.com/docs/5.x/schemas/tables
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->sortable(),

                TextColumn::make('type.name')
                    ->placeholder('-'),

                TextColumn::make('owner.name')
                    ->placeholder('-'),

                TextColumn::make('assignee.name')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TicketStatusEnum::class),

                SelectFilter::make('priority')
                    ->options(TicketPriorityEnum::class),

                SelectFilter::make('type_id')
                    ->options(TicketTypeEnum::class)
                    ->native(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    /**
     * Legacy API - backward compatibility.
     *
     * **Deprecation**: Marked for removal in v6.0
     * **Current usage**: Resources still call table()
     *
     * Delegates to configure() to avoid code duplication.
     */
    public static function table(Table $table): Table
    {
        return static::configure($table);
    }
}
