<?php

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Illuminate\Support\HtmlString;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Geo\Filament\Forms\Components\GeopointPicker;
use Modules\UI\Filament\Forms\Components\EnumSelect;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class TicketForm extends XotBaseResourceForm
{
    public static function getFormSchema(): array
    {
        return [
            Wizard::make(static::getWizardSteps())
                ->skippable()
            // ->startOnStep(2)
                ->persistStepInQueryString(),
        ];
    }

    public static function getWizardSteps(): array
    {
        return [
            static::getStepByName('privacy')
                ->description((string) __('fixcity::ticket_wizard.steps.privacy.description')),
            static::getStepByName('data')
                ->description((string) __('fixcity::ticket_wizard.steps.data.description')),
            static::getStepByName('summary')
                ->description((string) __('fixcity::ticket_wizard.steps.summary.description')),
        ];
    }

    public static function getPrivacySchema(): array
    {
        return [
            Text::make(fn (): HtmlString => static::getPrivacyNoticeHtml())
                ->columnSpanFull(),
            Checkbox::make('privacyAccepted')
                ->accepted()
                ->dehydrated(false),
        ];
    }

    protected static function getPrivacyNoticeHtml(): HtmlString
    {
        // $privacyLink = (string) ($this->blockData['privacy_link'] ?? '#');
        $privacyLink = '#';
        $intro = (string) __('fixcity::segnalazione.privacy.intro.text');
        $detailPrefix = (string) __('fixcity::segnalazione.privacy.detail_prefix.text');
        $linkLabel = (string) __('fixcity::segnalazione.privacy.link.label');

        return new HtmlString(\sprintf(
            '<p class="mb-3">%s</p><p>%s<a href="%s" class="text-primary text-decoration-underline">%s</a></p>',
            e($intro),
            e($detailPrefix),
            e($privacyLink),
            e($linkLabel),
        ));
    }

    public static function getDataSchema(): array
    {
        return [
            Section::make((string) __('fixcity::segnalazione.fields.place.section.label'))
                ->description((string) __('fixcity::segnalazione.sections.place.description'))
                ->compact()
                ->extraAttributes(['id' => 'report-place', 'data-step-section' => 'place'])
                ->schema([
                    CoordinatePicker::make('location')
                        ->hiddenLabel()
                        ->zoom(15)
                        ->height('340px')
                        ->geolocateWhenEmpty()
                        ->reverseGeocoding(),
                    /*
                        // NON CANCELLARE QUESTO
                        GeopointPicker::make('location1')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                    /*
                        // NON CANCELLARE QUESTO
                        LatitudeLongitudeInput::make('location2')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                    /*
                        // NON CANCELLARE QUESTO
                        LeafletMarkerMapInput::make('location3')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                    /*
                        LocationPicker::make('location4')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                    /*
                        MapLocationInput::make('location5')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                    /*
                        MapPicker::make('location6')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                    /*
                        MapPositioner::make('location7')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                    /*
                        PlacePicker::make('location8')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
                ]),

            Section::make((string) __('fixcity::segnalazione.fields.inefficiency.section.label'))
                ->description((string) __('fixcity::segnalazione.sections.inefficiency.description'))
                ->compact()
                ->extraAttributes(['id' => 'report-info', 'data-step-section' => 'inefficiency'])
                ->schema([
                    EnumSelect::make('type_id')
                        ->options(TicketTypeEnum::class)
                        ->required()
                        ->native(false),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('content')
                        ->required()
                        ->maxLength(200)
                        ->rows(3)
                        ->helperText((string) __('fixcity::segnalazione.fields.details.max_chars.label')),
                    FileUpload::make('images')
                        ->helperText((string) __('fixcity::segnalazione.fields.images.help_text'))
                        ->multiple()
                        ->image()
                        ->disk('public')
                        ->directory('tickets/images')
                        ->maxFiles(10)
                        ->openable(),
                ]),

            Section::make((string) __('fixcity::segnalazione.sections.author.label'))
                ->description((string) __('fixcity::segnalazione.sections.author.description'))
                ->compact()
                ->extraAttributes(['id' => 'report-author', 'data-step-section' => 'author'])
                ->schema([
                    Grid::make(['default' => 1, 'lg' => 3])->schema([
                        TextEntry::make('author_name')
                            // ->state(fn (): string => $this->getAuthUserName())
                            ->icon('heroicon-o-user'),
                        TextEntry::make('author_fiscal_code')
                            // ->state(fn (): string => $this->getAuthUserFiscalCode())
                            ->icon('heroicon-o-identification'),
                        TextEntry::make('author_phone')
                            // ->state(fn (): string => $this->getAuthUserPhone())
                            ->icon('heroicon-o-phone'),
                    ]),

                    TextInput::make('email')
                        ->helperText((string) __('fixcity::create_ticket_wizard.fields.email.helper_text'))
                        ->email()
                        ->maxLength(255),
                ]),
        ];
    }

    public static function getSummarySchema(): array
    {
        return [
            Section::make((string) __('fixcity::ticket_wizard.steps.summary.label'))
                ->description((string) __('fixcity::ticket_wizard.steps.summary.description'))
                ->compact()
                ->extraAttributes(['id' => 'report-summary', 'data-step-section' => 'summary'])
                ->schema([
                    Grid::make(['default' => 1, 'lg' => 2])
                        ->schema([
                            TextEntry::make('review_type')
                            // ->state(fn (Get $get): string => $this->formatTicketTypeSummary($get('type_id')))
                            ,
                            TextEntry::make('review_name')
                                ->state(fn (Get $get): string => (string) ($get('name') ?? '')),
                            TextEntry::make('review_content')
                                ->state(fn (Get $get): string => (string) ($get('content') ?? ''))
                                ->columnSpanFull(),
                            TextEntry::make('review_email')
                                ->state(fn (Get $get): string => (string) ($get('email') ?? '')),
                            TextEntry::make('review_location')
                            // ->state(fn (Get $get): string => $this->formatLocationSummary($get('location')))
                            ,
                            ImageEntry::make('review_images')
                                ->state(fn (Get $get): array => $this->normalizeSummaryImages($get('images')))
                                ->disk('public')
                                ->limit(4)
                                ->limitedRemainingText()
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    protected function formatTicketTypeSummary(mixed $value): string
    {
        if (! $value) {
            return '';
        }
        $type = TicketTypeEnum::tryFrom((string) $value);

        return $type?->getLabel() ?? (string) $value;
    }

    protected function formatLocationSummary(mixed $location): string
    {
        if (! is_array($location)) {
            return '';
        }

        $address = trim((string) ($location['address'] ?? ''));
        if ($address !== '') {
            return $address;
        }

        $lat = $location['lat'] ?? $location['latitude'] ?? null;
        $lng = $location['lng'] ?? $location['longitude'] ?? null;
        if (is_numeric($lat) && is_numeric($lng)) {
            return sprintf('%s, %s', (string) $lat, (string) $lng);
        }

        return '';
    }

    protected function normalizeSummaryImages(mixed $images): array
    {
        if (! is_array($images)) {
            return [];
        }

        return array_values(array_filter($images, static fn (mixed $image): bool => is_string($image) && $image !== ''));
    }
}
