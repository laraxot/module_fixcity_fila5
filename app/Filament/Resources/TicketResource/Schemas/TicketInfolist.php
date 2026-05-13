<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Illuminate\Contracts\Support\Htmlable;
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
     * Array-style schema for Infolist view.
     *
     * **Regola**: classi che estendono XotBaseResourceInfolist NON devono avere configure()
     * **Pattern**: usa solo getInfolistSchema()
     *
     * @return array<int, Component|Htmlable|string>
     */
    public static function getInfolistSchema(): array
    {
        return [
            Tabs::make('ticket')
                ->tabs([
                    Tabs\Tab::make('overview')
                        ->icon('heroicon-o-information-circle')
                        ->schema(static::getOverviewSchema()),
                    Tabs\Tab::make('location')
                        ->icon('heroicon-o-map-pin')
                        ->schema(static::getLocationSchema()),
                ])
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Component|Htmlable|string>
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
     * @return array<int, Component|Htmlable|string>
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
