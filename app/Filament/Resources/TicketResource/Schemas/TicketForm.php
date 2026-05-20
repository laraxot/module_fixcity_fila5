<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class TicketForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Step>
     */
    public static function getSteps(): array
    {
        return [
            'privacy' => static::getStepByName('privacy'),
            'data' => static::getStepByName('data'),
            'summary' => static::getStepByName('summary'),
        ];
    }

    /**
     * @return array<string, SchemaComponent>
     */
    public static function getSummarySchema(): array
    {
        return [
            'warningSection' => Section::make()
                ->schema(static::getWarningSectionSchema())
                ->columnSpanFull(),
            'summarySection' => Section::make()
                ->heading(__('fixcity::segnalazione.sections.summary.label'))
                ->schema(static::getSummarySectionSchema())
                ->columnSpanFull(),
            'authorSection' => Section::make()
                ->heading(__('fixcity::segnalazione.heading.report_author.label'))
                ->schema(static::getAuthorSectionSchema())
                ->columnSpanFull(),
            'contactsSection' => Section::make()
                ->heading(__('fixcity::segnalazione.heading.contacts.label'))
                ->schema(static::getContactsSectionSchema())
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<string, SchemaComponent>
     */
    protected static function getWarningSectionSchema(): array
    {
        $warningTitle = (string) __('fixcity::segnalazione.warning.title.label');
        $warningText = (string) __('fixcity::segnalazione.warning.summary_declaration.text');

        return [
            'warningContent' => Html::make(fn () => new HtmlString(
                '<div class="alert alert-warning" role="alert">'
                .'<h4 class="alert-heading">'.e($warningTitle).'</h4>'
                .'<p class="mb-0">'.e($warningText).'</p>'
                .'</div>'
            ))->columnSpanFull(),
        ];
    }

    /**
     * @return array<string, SchemaComponent>
     */
    protected static function getSummarySectionSchema(): array
    {
        return [
            'locationAddress' => TextInput::make('location.address'),
            'type' => TextInput::make('type'),
            'name' => TextInput::make('name'),
            'content' => Textarea::make('content')
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<string, SchemaComponent>
     */
    protected static function getAuthorSectionSchema(): array
    {
        return [
            'authorName' => TextInput::make('author_name'),
            'authorFiscalCode' => TextInput::make('author_fiscal_code'),
        ];
    }

    /**
     * @return array<string, SchemaComponent>
     */
    protected static function getContactsSectionSchema(): array
    {
        return [
            'authorPhone' => TextInput::make('author_phone'),
            'authorEmail' => TextInput::make('author_email'),
        ];
    }

    /**
     * Wrapper per compatibilità con XotBaseResourceForm.
     *
     * @return array<string, SchemaComponent>
     */
    public static function getFormSchema(): array
    {
        return array_merge(
            static::getPrivacySchema(),
            static::getDataSchema(),
            static::getSummarySchema(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultFormState(): array
    {
        return [
            'privacyAccepted' => false,
            'name' => '',
            'type' => null,
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
     * @return array<string, SchemaComponent>
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
            'gdprNotice' => Html::make(static::getGdprHtml())
                ->columnSpanFull(),
            'privacyAccepted' => Checkbox::make('privacyAccepted')
                ->accepted()
                ->dehydrated(false),
        ];
    }

    public static function getGdprHtml(): HtmlString
    {
        return new HtmlString(view('fixcity::components.gdpr-notice', [
            'intro' => (string) __('fixcity::segnalazione.privacy.intro.text'),
            'detailsPrefix' => (string) __('fixcity::segnalazione.privacy.detail_prefix.text'),
            'privacyLabel' => (string) __('fixcity::segnalazione.privacy.link.label'),
            'privacyUrl' => '/privacy',
        ])->render());
    }

    /**
     * @return array<string, SchemaComponent>
     */
    public static function getDataSchema(): array
    {
        return [
            'name' => TextInput::make('name')
                ->hiddenLabel()
                ->columnSpanFull()
                ->required()
                ->maxLength(255),
            'type' => Select::make('type')
                ->hiddenLabel()
                ->searchable()
                ->options(TicketTypeEnum::class)
                ->columnSpanFull(),
            'priority' => Select::make('priority')
                ->hiddenLabel()
                ->searchable()
                ->options(TicketPriorityEnum::class)
                ->columnSpanFull(),
            'content' => Textarea::make('content')
                ->hiddenLabel()
                ->columnSpanFull()
                ->rows(4),
            'location' => CoordinatePicker::make('location')
                ->columnSpanFull()
                ->reverseGeocoding(),
            'images' => SpatieMediaLibraryFileUpload::make('images')
                ->hiddenLabel()
                ->columnSpanFull()
                ->collection('attachments')
                ->imageEditor()
                ->maxFiles(5)
                ->acceptedFileTypes(['image/*']),
        ];
    }
}
