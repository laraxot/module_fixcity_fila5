<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Fixcity\Filament\Resources\TicketResource;

class ManageTicketStatuses extends ManageRelatedRecords
{
    protected static string $resource = TicketResource::class;

    protected static string $relationship = 'statuses';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Statuses';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('reason'),
                TextColumn::make('created_at'),
            ])
            ->filters([
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DetachAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //    Tables\Actions\DetachBulkAction::make(),
                //    Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
