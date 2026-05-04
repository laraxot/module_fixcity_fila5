<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Modules\Fixcity\Filament\Schemas\Components\PubThemeWizard;
use Illuminate\Support\HtmlString;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Concerns\HasTicketAuthorData;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\UI\Filament\Forms\Components\EnumSelect;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class TicketForm extends XotBaseResourceForm
{
    use HasTicketAuthorData;

    /**
     * @return array<int, Component>
     */
    public static function getFormSchema(): array
    {
        // Zen: Blueprint (Schema) definisce il vestito, non il Widget
        // PubThemeWizard estende Wizard e setta già $view = 'pub_theme::components.wizard'
        $wizard = PubThemeWizard::make(static::getWizardSteps())
            ->skippable()
            ->persistStepInQueryString();

        return [
            $wizard,
        ];
    }

    /**
     * @return array<int, Step>
     */
    public static function getWizardSteps(): array
    {
        return [
            static::getStepByName('privacy'),
            static::getStepByName('data'),
            static::getStepByName('summary'),
                
        ];
    }

    /**
     * @return array<int, Component>
     */
    public static function getPrivacySchema(): array
    {
        return static::getFrontofficePrivacySchema('#');
    }

    /**
     * @return array<int, Component>
     */
    public static function getFrontofficePrivacySchema(string $privacyLink): array
    {
        // Zen: LangServiceProvider owns labels — no ->label() here
        // The key 'fixcity::segnalazione.privacy.checkbox.label' is auto-resolved
        return [
            Text::make(static fn (): HtmlString => static::getPrivacyNoticeHtml($privacyLink))
                ->columnSpanFull(),
            Checkbox::make('privacyAccepted')
                ->accepted()
                ->dehydrated(false),
        ];
    }

    /**
     * @return array<int, Component>
     */
    public static function getDataSchema(): array
    {
        return [
            Section::make(SafeStringCastAction::cast(__('fixcity::segnalazione.fields.place.section.label')))
                ->description(SafeStringCastAction::cast(__('fixcity::segnalazione.sections.place.description')))
                ->compact()
                ->extraAttributes(['id' => 'report-place', 'data-step-section' => 'place'])
                ->schema([
                    CoordinatePicker::make('location')
                        ->hiddenLabel()
                        ->zoom(15)
                        ->height('340px')
                        ->geolocateWhenEmpty()
                        ->reverseGeocoding(),
                ]),

            Section::make(SafeStringCastAction::cast(__('fixcity::segnalazione.fields.inefficiency.section.label')))
                ->description(SafeStringCastAction::cast(__('fixcity::segnalazione.sections.inefficiency.description')))
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
                        ->helperText(SafeStringCastAction::cast(__('fixcity::segnalazione.fields.details.max_chars.label'))),
                    FileUpload::make('images')
                        ->helperText(SafeStringCastAction::cast(__('fixcity::segnalazione.fields.images.help_text')))
                        ->multiple()
                        ->image()
                        ->disk('public')
                        ->directory('tickets/images')
                        ->maxFiles(10)
                        ->openable(),
                ]),

            Section::make(SafeStringCastAction::cast(__('fixcity::segnalazione.sections.author.label')))
                ->description(SafeStringCastAction::cast(__('fixcity::segnalazione.sections.author.description')))
                ->compact()
                ->extraAttributes(['id' => 'report-author', 'data-step-section' => 'author'])
                ->schema([
                    Grid::make(['default' => 1, 'lg' => 2])->schema([
                        TextEntry::make('author_name')
                            ->state(static fn (): string => static::getAuthUserName())
                            ->icon('heroicon-o-user'),
                        TextEntry::make('author_fiscal_code')
                            ->state(static fn (): string => static::getAuthUserFiscalCode())
                            ->icon('heroicon-o-identification'),
                        TextEntry::make('author_phone')
                            ->state(static fn (): string => static::getAuthUserPhone())
                            ->icon('heroicon-o-phone'),
                        TextEntry::make('author_email')
                            ->state(static fn (): string => static::getAuthUserEmail())
                            ->icon('heroicon-o-envelope'),
                    ]),
                ]),
        ];
    }

    /**
     * @return array<int, Component>
     */
    public static function getSummarySchema(): array
    {
        return [
            Section::make(SafeStringCastAction::cast(__('fixcity::ticket_wizard.steps.summary.label')))
                ->description(SafeStringCastAction::cast(__('fixcity::ticket_wizard.steps.summary.description')))
                ->compact()
                ->extraAttributes(['id' => 'report-summary', 'data-step-section' => 'summary'])
                ->schema([
                    Grid::make(['default' => 1, 'lg' => 2])
                        ->schema([
                            TextEntry::make('review_type')
                                ->state(static fn (Get $get): string => static::formatTicketTypeSummary($get('type_id'))),
                            TextEntry::make('review_name')
                                ->state(static fn (Get $get): string => SafeStringCastAction::cast($get('name'))),
                            TextEntry::make('review_content')
                                ->state(static fn (Get $get): string => SafeStringCastAction::cast($get('content')))
                                ->columnSpanFull(),
                            TextEntry::make('review_location')
                                ->state(static fn (Get $get): string => static::formatLocationSummary($get('location'))),
                            ImageEntry::make('review_images')
                                ->state(static fn (Get $get): array => static::normalizeSummaryImages($get('images')))
                                ->disk('public')
                                ->limit(4)
                                ->limitedRemainingText()
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    protected static function getPrivacyNoticeHtml(string $privacyLink = '#'): HtmlString
    {
        $intro = SafeStringCastAction::cast(__('fixcity::segnalazione.privacy.intro.text'));
        $detailPrefix = SafeStringCastAction::cast(__('fixcity::segnalazione.privacy.detail_prefix.text'));
        $linkLabel = SafeStringCastAction::cast(__('fixcity::segnalazione.privacy.link.label'));

        return new HtmlString(\sprintf(
            '<p class="mb-3">%s</p><p>%s<a href="%s" class="text-primary text-decoration-underline">%s</a></p>',
            e($intro),
            e($detailPrefix),
            e($privacyLink),
            e($linkLabel),
        ));
    }

    protected static function formatTicketTypeSummary(mixed $value): string
    {
        if ($value instanceof TicketTypeEnum) {
            return $value->getLabel();
        }

        if ($value === null || $value === '') {
            return '';
        }

        $type = TicketTypeEnum::tryFrom(SafeStringCastAction::cast($value));

        return $type?->getLabel() ?? SafeStringCastAction::cast($value);
    }

    protected static function formatLocationSummary(mixed $location): string
    {
        if (! \is_array($location)) {
            return '';
        }

        $address = trim(SafeStringCastAction::cast($location['address'] ?? ''));
        if ($address !== '') {
            return $address;
        }

        $lat = $location['lat'] ?? $location['latitude'] ?? null;
        $lng = $location['lng'] ?? $location['longitude'] ?? null;
        if (is_numeric($lat) && is_numeric($lng)) {
            return \sprintf('%s, %s', SafeStringCastAction::cast($lat), SafeStringCastAction::cast($lng));
        }

        return '';
    }

    /**
     * @return array<int, string>
     */
    protected static function normalizeSummaryImages(mixed $images): array
    {
        if (! \is_array($images)) {
            return [];
        }

        return array_values(array_filter($images, static fn (mixed $image): bool => \is_string($image) && $image !== ''));
    }
}
