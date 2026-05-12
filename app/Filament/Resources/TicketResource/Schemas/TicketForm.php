<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Forms\Components\Checkbox;
<<<<<<< HEAD
=======
use Filament\Forms\Components\RichEditor;
>>>>>>> 123bd69b8 (refactor: standardize wizard method from getWizardSteps() to Filament's native getSteps() across Fixcity module)
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
<<<<<<< HEAD
use Filament\Schemas\Components\Wizard\Step;
=======
use Filament\Schemas\Components\Text;
>>>>>>> 123bd69b8 (refactor: standardize wizard method from getWizardSteps() to Filament's native getSteps() across Fixcity module)
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class TicketForm extends XotBaseResourceForm
{
    /**
<<<<<<< HEAD
     * @return array<int, Step>
     */
    public static function getWizardSteps(): array
    {
        return [
            Step::make('privacy')
                ->label(__('fixcity::segnalazione.steps.privacy.label'))
                ->schema(static::getPrivacySchema()),
            Step::make('dati-di-segnalazione')
                ->label(__('fixcity::segnalazione.steps.data.label'))
                ->schema(static::getDataSchema()),
            Step::make('riepilogo')
                ->label(__('fixcity::segnalazione.steps.summary.label'))
                ->schema(static::getSummarySchema()),
        ];
    }

    /**
     * @return array<int, SchemaComponent>
     */
    public static function getPrivacySchema(): array
    {
        return [
            Checkbox::make('privacyAccepted')
                ->label(__('fixcity::segnalazione.privacy.acceptance'))
                ->accepted()
                ->dehydrated(false),
        ];
    }

    /**
     * @return array<int, SchemaComponent>
     */
    public static function getDataSchema(): array
    {
        return static::getDataFields();
    }

    /**
     * @return array<int, SchemaComponent>
     */
    public static function getSummarySchema(): array
    {
        return [
            Section::make(__('fixcity::segnalazione.summary.title'))
                ->schema([
                    TextInput::make('review_type')
                        ->label(__('fixcity::segnalazione.summary.type'))
                        ->formatStateUsing(static fn (mixed $state, Get $get): string => (string) ($get('type') ?? ''))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('review_name')
                        ->label(__('fixcity::segnalazione.summary.name'))
                        ->formatStateUsing(static fn (mixed $state, Get $get): string => (string) ($get('name') ?? ''))
                        ->disabled()
                        ->dehydrated(false),
                    Textarea::make('review_content')
                        ->label(__('fixcity::segnalazione.summary.content'))
                        ->formatStateUsing(static fn (mixed $state, Get $get): string => (string) ($get('content') ?? ''))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('review_location')
                        ->label(__('fixcity::segnalazione.summary.location'))
                        ->formatStateUsing(static function (mixed $state, Get $get): string {
                            $location = $get('location');

                            if (is_array($location)) {
                                if (isset($location['address']) && is_string($location['address']) && $location['address'] !== '') {
                                    return $location['address'];
                                }

                                $lat = $location['lat'] ?? $location['latitude'] ?? null;
                                $lng = $location['lng'] ?? $location['longitude'] ?? null;

                                if ($lat !== null && $lng !== null) {
                                    return (string) $lat.', '.(string) $lng;
                                }
                            }

                            return '';
                        })
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ];
    }

    /**
     * @return array<int|string, SchemaComponent>
=======
     * @return array<int|string, \Filament\Schemas\Components\Component>
>>>>>>> 123bd69b8 (refactor: standardize wizard method from getWizardSteps() to Filament's native getSteps() across Fixcity module)
     */
    public static function getFormSchema(): array
    {
        return [
            'main' => Section::make()
                ->schema(static::getDataFields())
                ->columns(1) // Imposta il layout su una colonna
                ->extraAttributes(['class' => 'w-full max-w-full mx-auto', 'style' => 'padding: 0; margin: 0; !important;']), // Rimozione padding e margine
        ];

    }

    /**
<<<<<<< HEAD
     * @return array<int, SchemaComponent>
     */
    protected static function getDataFields(): array
    {
        return [
            TextInput::make('name')
                ->hiddenLabel()
                ->placeholder(__('fixcity::fixcity.ticket.title.placeholder').'*')
                ->columnSpanFull()
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
            TextInput::make('slug')
                ->columnSpanFull()
                ->required()
                ->hidden()
                ->extraAttributes(['class' => 'max-w-full', 'style' => 'padding: 0; margin: 0;']),
            Select::make('type')
                ->hiddenLabel()
                ->placeholder(__('fixcity::fixcity.ticket.type.placeholder').'*')
                ->searchable()
                ->options(TicketTypeEnum::class)
                ->columnSpanFull(),
            Select::make('priority')
                ->hiddenLabel()
                ->placeholder(__('fixcity::fixcity.ticket.priorities.label'))
                ->searchable()
                ->options(TicketPriorityEnum::class)
                ->default(TicketPriorityEnum::default())
                ->columnSpanFull(),
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
            SpatieMediaLibraryFileUpload::make('images')
                ->label(__('fixcity::fixcity.ticket.insert-images'))
                ->collection('ticket')
                ->directory('ticket')
                ->disk('uploads')
                ->responsiveImages()
                ->multiple()
                ->required()
                ->maxFiles(5)
                ->maxSize(10240)
                ->columnSpanFull(),
=======
     * @return array<int, Step>
     */
    public static function getSteps(): array
    {
        return [
            Step::make('step-1')
                ->label(__('fixcity::fixcity.ticket.steps.auth.label'))
                ->icon('heroicon-o-shield-check')
                ->description(__('fixcity::fixcity.ticket.steps.auth.description'))
                ->schema([
                    RichEditor::make('privacy_notice')
                        ->label('')
                        ->default(__('fixcity::fixcity.ticket.fields.privacy_notice.content'))
                        ->disabled()
                        ->extraAttributes(['class' => 'border-0 shadow-none !p-0 !bg-transparent']),

                    Checkbox::make('accept_terms')
                        ->label(__('fixcity::fixcity.ticket.fields.accept_terms.label'))
                        ->helperText(__('fixcity::fixcity.ticket.fields.accept_terms.helper'))
                        ->required()
                        ->default(false)
                        ->extraAttributes(['class' => 'text-green-500 text-lg checked:bg-green-500 checked:hover:bg-green-500 focus:ring-green-500'])
                        ->rules(['accepted']),
                ]),

            Step::make('step-2')
                ->label(__('fixcity::fixcity.ticket.steps.data.label'))
                ->icon('heroicon-o-document-text')
                ->description(__('fixcity::fixcity.ticket.steps.data.description'))
                ->schema([
                    Text::make(
                        new HtmlString(
                            '<h1 class="subtitle text-4xl font-bold mb-4 dark:text-white">'
                            .e(__('fixcity::fixcity.ticket.fields.issue.label'))
                            .'</h1>'
                        )
                    )->columnSpanFull(),
                    ...self::getFormSchema(),
                ]),
>>>>>>> 123bd69b8 (refactor: standardize wizard method from getWizardSteps() to Filament's native getSteps() across Fixcity module)
        ];
    }
}
