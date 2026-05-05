<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

/**
 * Ticket Infolist Schema - Filament v5 Hybrid Pattern.
 *
 * **Pattern**: Extends XotBaseResourceInfolist with dual API support:
 * - `configure(Schema $schema): Schema` - Filament v5 fluent API (NEW)
 * - `getInfolistSchema(): array` - Legacy array API (preserved for backward compatibility)
 *
 * **Architecture**:
 * - Tabs-based layout: Overview + Location
 * - Uses LangServiceProvider for auto-label (NO ->label() calls)
 * - Section-based grouping within tabs
 *
 * @see https://github.com/filamentphp/demo/blob/5.x/app/Filament/Resources/HR/Projects/Schemas/ProjectInfolist.php
 * @see XotBaseResourceInfolist
 */
class TicketInfolist extends XotBaseResourceInfolist
{
    /**
     * Filament v5 style: Fluent Schema configuration.
     *
     * **Philosophy**: Use fluent API for layout structure.
     * **NO ->label() calls**: LangServiceProvider auto-resolves translations.
     *
     * @see https://filamentphp.com/docs/5.x/schemas/infolists
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('ticket')
                    ->schema([
                        static::getTabByName('overview', static::getOverviewSchema(), 'heroicon-o-information-circle', 2),
                        static::getTabByName('location', static::getLocationSchema(), 'heroicon-o-map-pin', 2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Legacy array-style for backward compatibility.
     *
     * **Deprecation**: Marked for removal in v6.0
     * **Current usage**: Resources still call getInfolistSchema()
     *
     * @return array<int, \Filament\Schemas\Components\Component|\Illuminate\Contracts\Support\Htmlable|string>
     */
    public static function getInfolistSchema(): array
    {
        return [
            Tabs::make('ticket')
                ->schema([
                    static::getTabByName('overview', static::getOverviewSchema(), 'heroicon-o-information-circle', 2),
                    static::getTabByName('location', static::getLocationSchema(), 'heroicon-o-map-pin', 2),
                ])
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component|\Illuminate\Contracts\Support\Htmlable|string>
     */
    public static function getOverviewSchema(): array
    {
        return [
            Section::make()
                ->columns(2)
                ->schema([
                    TextEntry::make('id'),
                    TextEntry::make('slug'),
                    TextEntry::make('name')
                        ->columnSpanFull(),
                    TextEntry::make('status')
                        ->badge(),
                    TextEntry::make('priority')
                        ->badge(),
                    TextEntry::make('type_id')
                        ->badge(),
                    TextEntry::make('owner.name')
                        ->placeholder('-'),
                    TextEntry::make('assignee.name')
                        ->placeholder('-'),
                    TextEntry::make('created_at')
                        ->dateTime(),
                    TextEntry::make('updated_at')
                        ->dateTime(),
                    TextEntry::make('content')
                        ->prose()
                        ->columnSpanFull()
                        ->placeholder('-'),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component|\Illuminate\Contracts\Support\Htmlable|string>
     */
    public static function getLocationSchema(): array
    {
        return [
            Section::make()
                ->columns(2)
                ->schema([
                    TextEntry::make('location.address')
                        ->columnSpanFull()
                        ->placeholder('-'),
                    TextEntry::make('location.lat')
                        ->placeholder('-'),
                    TextEntry::make('location.lng')
                        ->placeholder('-'),
                    SpatieMediaLibraryImageEntry::make('images')
                        ->collection('ticket')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
