<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class TicketForm extends XotBaseResourceForm
{
    /**
     * @return array<int, Step>
     */
    public static function getSteps(): array
    {
        return [
            static::getStepByName('privacy'),
            static::getStepByName('data'),
            static::getStepByName('summary'),
        ];
    }

    /**
     * @return array<int, SchemaComponent>
     */
    public static function getPrivacySchema(): array
    {
        return [
            Checkbox::make('privacyAccepted')
                ->hiddenLabel()
                ->accepted()
                ->dehydrated(false),
        ];
    }

    /**
     * @return array<int, SchemaComponent>
     */
    public static function getDataSchema(): array
    {
        return [
            TextInput::make('name')
                ->hiddenLabel()
                ->columnSpanFull()
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->columnSpanFull()
                ->required()
                ->hidden(),
            Select::make('type_id')
                ->hiddenLabel()
                ->searchable()
                ->options(TicketTypeEnum::class)
                ->columnSpanFull(),
            Select::make('priority')
                ->hiddenLabel()
                ->searchable()
                ->options(TicketPriorityEnum::class)
                ->default(TicketPriorityEnum::default())
                ->columnSpanFull(),
            Textarea::make('content')
                ->hiddenLabel()
                ->rows(2)
                ->cols(10),
            CoordinatePicker::make('location')
                ->hiddenLabel()
                ->columnSpanFull()
                ->zoom(15)
                ->height('340px')
                ->reverseGeocoding(),
            SpatieMediaLibraryFileUpload::make('images')
                ->hiddenLabel()
                ->collection('attachments')
                ->directory('attachments')
                ->disk('uploads')
                ->responsiveImages()
                ->multiple()
                ->required()
                ->maxFiles(5)
                ->maxSize(10240)
                ->columnSpanFull(),
        ];
    }

    /**
     * Riepilogo wizard — Infolist entries (read-only) invece di form inputs disabilitati.
     * Pattern: {@see TextEntry} con `->state(fn(Get $get))` per leggere lo stato wizard.
     *
     * @return array<int, SchemaComponent>
     */
    public static function getSummarySchema(): array
    {
        return [
            Section::make()
                ->schema([
                    Grid::make(['default' => 1, 'md' => 2])
                        ->schema([
                            TextEntry::make('type_id')
                                ->state(static fn (Get $get): string => static::formatTicketType($get('type_id'))),
                            TextEntry::make('priority')
                                ->state(static fn (Get $get): string => static::formatTicketPriority($get('priority'))),
                            TextEntry::make('name')
                                ->state(static fn (Get $get): string => (string) ($get('name') ?? '')),
                            TextEntry::make('content')
                                ->columnSpanFull()
                                ->state(static fn (Get $get): string => (string) ($get('content') ?? '')),
                            TextEntry::make('location')
                                ->columnSpanFull()
                                ->state(static function (Get $get): string {
                                    $location = $get('location');
                                    if (! is_array($location)) {
                                        return '';
                                    }

                                    if (isset($location['address']) && is_string($location['address']) && '' !== $location['address']) {
                                        return $location['address'];
                                    }

                                    $lat = $location['lat'] ?? $location['latitude'] ?? null;
                                    $lng = $location['lng'] ?? $location['longitude'] ?? null;

                                    if (null !== $lat && null !== $lng) {
                                        return (string) $lat.', '.(string) $lng;
                                    }

                                    return '';
                                }),
                        ]),
                ]),
        ];
    }

    public static function formatTicketType(mixed $value): string
    {
        if ($value instanceof TicketTypeEnum) {
            return $value->getLabel();
        }

        if (is_string($value)) {
            return TicketTypeEnum::tryFrom($value)?->getLabel() ?? $value;
        }

        return '';
    }

    public static function formatTicketPriority(mixed $value): string
    {
        if ($value instanceof TicketPriorityEnum) {
            return $value->getLabel();
        }

        if (is_string($value)) {
            return TicketPriorityEnum::tryFrom($value)?->getLabel() ?? $value;
        }

        return '';
    }

    /**
     * @return array<int|string, SchemaComponent>
     */
    public static function getFormSchema(): array
    {
        return static::getDataSchema();
    }

    
}
