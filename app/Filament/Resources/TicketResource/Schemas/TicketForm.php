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
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class TicketForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Step>
     */
    public function getSteps(): array
    {
        return [
            'privacy' => static::getStepByName('privacy')
                ->label((string) __('fixcity::fixcity.ticket.steps.auth.label')),
            'data' => static::getStepByName('data')
                ->label((string) __('fixcity::fixcity.ticket.steps.data.label')),
            'summary' => static::getStepByName('summary')
                ->label((string) __('fixcity::fixcity.ticket.steps.summary.label')),
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
     * Wrapper per compatibilità con XotBaseResourceForm.
     *
     * @return array<string, SchemaComponent>
     */
    public function getFormSchema(): array
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
            'status' => 'pending',
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
        return [
            'gdprNotice' => Html::make(static::getGdprHtml())
                ->columnSpanFull(),
            'privacyAccepted' => Checkbox::make('privacyAccepted')
                ->accepted()
                ->required()
                ->disabled(app(GetPublishedPrivacyPolicyAction::class)->execute() === null)
                ->extraInputAttributes(static function (Checkbox $component): array {
                    $errorBag = $component->getLivewire()->getErrorBag();
                    $hasPrivacyError = $errorBag instanceof MessageBag && $errorBag->has('data.privacyAccepted');

                    return [
                        'aria-invalid' => $hasPrivacyError ? 'true' : 'false',
                        'aria-describedby' => 'ticket-privacy-error',
                    ];
                })
                ->extraAttributes(['data-element' => 'privacy-consent']),
        ];
    }

    public static function getGdprHtml(): HtmlString
    {
        $policy = app(GetPublishedPrivacyPolicyAction::class)->execute();
        $privacyUrl = LaravelLocalization::getLocalizedURL(app()->getLocale(), '/privacy');
        $reportUrl = LaravelLocalization::getLocalizedURL(app()->getLocale(), '/tickets/create');

        return new HtmlString(view('fixcity::components.gdpr-notice', [
            'policyHtml' => $policy === null
                ? null
                : (string) Str::markdown($policy, [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]),
            'privacyUrl' => is_string($privacyUrl) ? $privacyUrl : '/privacy',
            'reportUrl' => is_string($reportUrl) ? $reportUrl : '/tickets/create',
        ])->render());
    }

    /**
     * @return array<string, SchemaComponent>
     */
    public static function getDataSchema(): array
    {
        return [
            'priority' => Hidden::make('priority')
                ->default('low')
                ->dehydrated(),
            'status' => Hidden::make('status')
                ->default('pending')
                ->dehydrated(),
            'name' => TextInput::make('name')
                ->columnSpanFull()
                ->required()
                ->minLength(3)
                ->maxLength(255),
            'type' => Select::make('type')
                ->searchable()
                ->required()
                ->options(TicketTypeEnum::class)
                ->columnSpanFull(),
            'content' => Textarea::make('content')
                ->columnSpanFull()
                ->required()
                ->minLength(10)
                ->rows(4),
            'location' => CoordinatePicker::make('location')
                ->columnSpanFull()
                ->required()
                ->reverseGeocoding(),
            'images' => SpatieMediaLibraryFileUpload::make('images')
                ->columnSpanFull()
                ->collection('attachments')
                ->maxFiles(5)
                ->acceptedFileTypes(['image/*']),
        ];
    }
}
