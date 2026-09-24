<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Modules\Fixcity\Actions\PrepareTicketFormDataForPersistAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\CreateTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\EditTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ListTickets;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ViewTicket;
use Modules\Fixcity\Models\Ticket;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Filament\Resources\XotBaseResource;

class TicketResource extends XotBaseResource
{
    protected static ?string $model = Ticket::class;

    /**
     * @return array<string, Component>
     */
    public static function getFormSchema(): array
    {
        return [
            'main' => Section::make()
                ->schema([
                    // Ticket Name
                    TextInput::make('name')
                        ->hiddenLabel()
                        ->placeholder(__('fixcity::fixcity.ticket.title.placeholder').'*')
                        ->columnSpanFull() // Occupa tutta la larghezza disponibile
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(static function (Set $set, Get $get, string $state): void {
                            if ($get('slug')) {
                                return;
                            }
                            $set('slug', Str::slug($state));
                        })
                        ->extraAttributes([
                            'style' => '',
                        ]),

                    // Slug
                    TextInput::make('slug')
                        ->columnSpanFull() // Anche lo slug occupa tutta la larghezza disponibile
                        ->required()
                        ->hidden()
                        ->extraAttributes(['class' => 'max-w-full', 'style' => 'padding: 0; margin: 0;']), // Rimozione del padding e margini

                    // Ticket Type
                    Select::make('type')
                        ->hiddenLabel()
                        ->placeholder(__('fixcity::fixcity.ticket.type.placeholder').'*')
                        ->searchable()
                        ->options(TicketTypeEnum::class)
                        ->columnSpanFull(),

                    // Ticket Priority
                    Select::make('priority')
                        ->hiddenLabel()
                        ->placeholder(__('fixcity::fixcity.ticket.priorities.label'))
                        ->searchable()
                        ->options(TicketPriorityEnum::class)
                        ->default(TicketPriorityEnum::default())
                        ->columnSpanFull(),

                    // Ticket Content (RichEditor)
                    // Forms\Components\RichEditor::make('content')
                    //     ->label(__('ticket::ticket.content.label'))
                    //     ->required()
                    //     ->columnSpanFull()
                    //     ->extraAttributes(['class' => 'max-w-full', 'style' => 'padding: 0; margin: 0;']), // Rimozione del padding e margini

                    Textarea::make('content')
                        ->hiddenLabel()
                        ->placeholder(__('fixcity::fixcity.ticket.content.placeholder').'**')
                        ->rows(2)
                        ->cols(10)
                        ->helperText(__('fixcity::fixcity.ticket.content.helper_text')),

                    CoordinatePicker::make('location')
                        ->label(__('fixcity::fixcity.ticket.your-location'))
                        ->columnSpanFull()
                        ->zoom(15)
                        ->height('340px')
                        ->geolocateWhenEmpty()
                        ->reverseGeocoding(),

                    // Image Upload
                    // SpatieMediaLibraryFileUpload::make('images')
                    //     ->label(__('ticket::ticket.insert-images'))
                    //     ->collection('ticket')
                    //     ->directory('ticket')
                    //     ->disk('uploads')
                    //     ->responsiveImages()
                    //     ->multiple()
                    //     ->required()
                    //     ->columnSpanFull()
                    //     // ->extraAttributes(['class' => 'max-w-full', 'style' => 'padding: 0; margin: 0;'])
                    //     ,

                    SpatieMediaLibraryFileUpload::make('images')
                        ->label(__('fixcity::fixcity.ticket.insert-images'))
                        ->collection('ticket')
                        ->directory('ticket')
                        ->disk('uploads')
                        ->responsiveImages()
                        ->multiple()
                        ->required()
                        ->maxFiles(5) // Limita il numero di file caricabili
                        ->maxSize(10240) // Imposta un limite massimo di 10MB per file
                        // ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg']) // Accetta solo immagini
                        ->columnSpanFull(),

                ])
                ->columns(1) // Imposta il layout su una colonna
                ->extraAttributes(['class' => 'w-full max-w-full mx-auto', 'style' => 'padding: 0; margin: 0; !important;']), // Rimozione padding e margine
        ];
    }

    /**
     * Normalizza stato form/schema verso gli attributi ammessi in creazione (`\Modules\Fixcity\Models\Ticket`) per il pannello Filament:
     * `TicketResource\Pages\CreateTicket::mutateFormDataBeforeCreate`. Il wizard frontoffice (`CreateTicketWizardWidget`) non usa questa helper.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function prepareFormDataBeforePersist(array $data): array
    {
        return app(PrepareTicketFormDataForPersistAction::class)->execute($data);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'edit' => EditTicket::route('/{record}/edit'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}
