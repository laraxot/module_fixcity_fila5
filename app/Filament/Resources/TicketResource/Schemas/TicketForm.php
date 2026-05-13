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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
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
     * Stato iniziale del form wizard richiesto da Livewire per l'entangle Alpine.
     * Tutti i campi di $fillable che il wizard gestisce, con valori null/vuoti.
     * `location` allineato alle chiavi di CoordinatePicker (latitude/longitude).
     * `priority` è stringa nel DB (non castata a enum nel modello).
     * `type_id` è integer nel DB, castato a TicketTypeEnum nel modello.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultFormState(): array
    {
        return [
            'privacyAccepted' => false,
            'name' => '',
            'type_id' => null,
            'priority' => TicketPriorityEnum::default()->value,
            'content' => '',
            'location' => [
                'latitude' => null,
                'longitude' => null,
                'address' => null,
            ],
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
     * Campi del form dati del ticket.
     * - `name`: obbligatorio, $fillable
     * - `type_id`: integer nel DB, castato a TicketTypeEnum; Select con options enum
     * - `priority`: stringa nel DB ($fillable), NON castata a enum nel modello; Select con options enum
     * - `content`: longText nel DB, $fillable
     * - `location`: JSON nel DB, castato array; gestito da CoordinatePicker con chiavi latitude/longitude
     * - `images`: media collection 'attachments' tramite Spatie MediaLibrary
     *
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
            Select::make('type_id')
                ->hiddenLabel()
                ->searchable()
                ->options(TicketTypeEnum::class)
                ->columnSpanFull(),
            Select::make('priority')
                ->hiddenLabel()
                ->searchable()
                ->options(TicketPriorityEnum::class)
                ->default(TicketPriorityEnum::default()->value)
                ->columnSpanFull(),
            Textarea::make('content')
                ->hiddenLabel()
                ->rows(4)
                ->columnSpanFull(),
            CoordinatePicker::make('location')
                ->hiddenLabel()
                ->columnSpanFull()
                ->zoom(15)
                ->height('340px'),
            SpatieMediaLibraryFileUpload::make('images')
                ->hiddenLabel()
                ->collection('attachments')
                ->directory('attachments')
                ->disk('uploads')
                ->multiple()
                ->maxFiles(5)
                ->maxSize(10240)
                ->columnSpanFull(),
        ];
    }

    /**
     * Riepilogo wizard — TextEntry read-only con ->state() che legge $get() dal form state.
     * NON usa form inputs disabilitati (causano errori di cast enum→string).
     * I nomi degli entry hanno prefisso 'review_' per evitare conflitti con i field del form.
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
                            TextEntry::make('review_type')
                                ->state(static fn (Get $get): string => static::formatTicketType($get('type_id'))),
                            TextEntry::make('review_priority')
                                ->state(static fn (Get $get): string => static::formatTicketPriority($get('priority'))),
                            TextEntry::make('review_name')
                                ->columnSpanFull()
                                ->state(static fn (Get $get): string => (string) ($get('name') ?? '')),
                            TextEntry::make('review_content')
                                ->columnSpanFull()
                                ->state(static fn (Get $get): string => (string) ($get('content') ?? '')),
                            TextEntry::make('review_location')
                                ->columnSpanFull()
                                ->state(static function (Get $get): string {
                                    $location = $get('location');
                                    if (! is_array($location)) {
                                        return '';
                                    }

                                    if (isset($location['address']) && is_string($location['address']) && '' !== $location['address']) {
                                        return $location['address'];
                                    }

                                    $lat = $location['latitude'] ?? $location['lat'] ?? null;
                                    $lng = $location['longitude'] ?? $location['lng'] ?? null;

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

        if (is_string($value) || is_int($value)) {
            $enum = is_int($value)
                ? TicketTypeEnum::tryFrom((string) $value)
                : TicketTypeEnum::tryFrom($value);

            return $enum?->getLabel() ?? (string) $value;
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
     * Wizard: getFormSchema() deve restituire array vuoto.
     * Il wizard usa getSteps() → getStepByName() → get{Name}Schema().
     *
     * @return array<int|string, SchemaComponent>
     */
    public static function getFormSchema(): array
    {
        return [];
    }

    public static function getDataSchemaOld(): array
    {
        return [];
    }
}


