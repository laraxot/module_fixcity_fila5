<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;
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
                ->heading((string) __('fixcity::ticket.sections.summary.label'))
                ->schema(static::getSummarySectionSchema())
                ->columnSpanFull(),
            'authorSection' => Section::make()
                ->heading((string) __('fixcity::ticket.sections.author.label'))
                ->schema(static::getAuthorSectionSchema())
                ->columnSpanFull(),
            'contactsSection' => Section::make()
                ->heading((string) __('fixcity::ticket.sections.contacts.label'))
                ->schema(static::getContactsSectionSchema())
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<string, SchemaComponent>
     */
    protected static function getWarningSectionSchema(): array
    {
        $warningTitle = (string) __('fixcity::ticket.warning.title.label');
        $warningText = (string) __('fixcity::ticket.warning.summary_declaration.text');

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
     * @return list<SchemaComponent>
     */
    protected static function getSummarySectionSchema(): array
    {
        return array_values(TicketFormReviewInfolist::summarySectionEntries());
    }

    /**
     * Dati autore: input (non Infolist) — si compilano nello stesso step del riepilogo.
     *
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
            'authorEmail' => TextInput::make('author_email')
                ->email(),
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
            'content' => '',
            'author_name' => '',
            'author_fiscal_code' => '',
            'author_phone' => '',
            'author_email' => '',
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
        return [
            'gdprNotice' => Html::make(static::getGdprHtml())
                ->columnSpanFull(),
            'privacyAccepted' => Checkbox::make('privacyAccepted')
                ->accepted()
                ->dehydrated(false)
                ->extraAttributes(['data-element' => 'privacy-consent']),
        ];
    }

    public static function getGdprHtml(): HtmlString
    {
        return new HtmlString(view('fixcity::components.gdpr-notice', [
            'intro' => (string) __('fixcity::ticket.privacy.intro.text'),
            'detailsPrefix' => (string) __('fixcity::ticket.privacy.detail_prefix.text'),
            'privacyLabel' => (string) __('fixcity::ticket.privacy.link.label'),
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
                ->minLength(3)
                ->maxLength(255),
            'type' => Select::make('type')
                ->hiddenLabel()
                ->searchable()
                ->required()
                ->options(TicketTypeEnum::class)
                ->columnSpanFull(),
            'content' => Textarea::make('content')
                ->hiddenLabel()
                ->columnSpanFull()
                ->required()
                ->minLength(10)
                ->rows(4),
            'location' => CoordinatePicker::make('location')
                ->columnSpanFull()
                ->required()
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
