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
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\BuildTicketReviewSummaryAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

use function Safe\preg_replace;

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
            'summary' => Step::make('summary')
                ->schema(array_values(static::getReviewStepSchema()))
                ->label((string) __('fixcity::fixcity.ticket.steps.summary.label')),
        ];
    }

    /**
     * Passo "riepilogo" del wizard front office (DCP-33): un solo componente con il markup di sito/segnalazione-03-riepilogo, alimentato dallo
     * stato del form (`$livewire->data`). Il guscio (breadcrumb, h1, barra dei passi, container) e' del widget; qui restano avviso, schede,
     * mappa e navigazione. `getSummarySchema()` (schema piatto) resta per il back office.
     *
     * @return array<string, SchemaComponent>
     */
    public static function getReviewStepSchema(): array
    {
        return [
            'reviewSummary' => View::make('pub_theme::components.wizard.summary-step')
                ->viewData(static fn (object $livewire): array => [
                    'review' => app(BuildTicketReviewSummaryAction::class)->execute(
                        array_filter((array) data_get($livewire, 'data', []), is_string(...), ARRAY_FILTER_USE_KEY),
                        Filament::auth()->user(),
                    ),
                    'data' => (array) data_get($livewire, 'blockData', []),
                    'withContainer' => false,
                    'withShell' => false,
                    'wired' => true,
                ])
                ->columnSpanFull(),
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
        return array_merge(
            [
                'review_map' => CoordinatePicker::make('location')
                    ->label((string) __('fixcity::ticket_form.fields.location.label'))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ],
            TicketFormReviewInfolist::summarySectionEntries(),
        );
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
                ->view('fixcity::filament.forms.components.privacy-consent-checkbox')
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

    /**
     * `required()` su un array passa anche con latitudine e longitudine nulle, e oltre 99 gradi la colonna `decimal(20,18)` dava
     * QueryException (SEC-06 DCP-49): il punto deve esistere, essere numerico e stare nei limiti geografici.
     */
    protected static function coordinatesRule(): \Closure
    {
        return static function (string $attribute, mixed $value, \Closure $fail): void {
            $point = \is_array($value) ? $value : [];
            $latitude = $point['latitude'] ?? $point['lat'] ?? null;
            $longitude = $point['longitude'] ?? $point['lng'] ?? null;

            if (! is_numeric($latitude) || ! is_numeric($longitude)
                || abs((float) $latitude) > 90 || abs((float) $longitude) > 180) {
                $fail((string) __('fixcity::ticket_form.fields.location.invalid'));
            }
        };
    }

    public static function getGdprHtml(): HtmlString
    {
        $policy = app(GetPublishedPrivacyPolicyAction::class)->execute();
        $privacyUrl = LaravelLocalization::getLocalizedURL(app()->getLocale(), '/privacy');
        $reportUrl = LaravelLocalization::getLocalizedURL(app()->getLocale(), '/tickets/create');

        return new HtmlString(view('fixcity::components.gdpr-notice', [
            // Il titolo del documento (`# ...`) e' ridondante: il passo ha gia' l'h1 della pagina.
            'policyHtml' => $policy === null
                ? null
                : (string) Str::markdown((string) preg_replace('/\A#[^\r\n]*\R*/', '', $policy), [
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
            'priority' => Hidden::make('priority')->default('low')->dehydrated(),
            'status' => Hidden::make('status')->default('pending')->dehydrated(),
            'locationSection' => Section::make((string) __('fixcity::ticket_form.sections.location.heading'))
                ->description((string) __('fixcity::ticket_form.sections.location.description'))
                ->schema([
                    TextInput::make('location.address')
                        ->placeholder((string) __('fixcity::ticket_form.fields.location.placeholder'))
                        ->columnSpanFull(),
                    CoordinatePicker::make('location')
                        ->columnSpanFull()
                        ->required()
                        ->rule(static fn (): \Closure => static::coordinatesRule())
                        ->reverseGeocoding(),
                ])
                ->columnSpanFull(),
            'issueSection' => Section::make((string) __('fixcity::ticket_form.sections.issue.heading'))
                ->schema([
                    Select::make('type')
                        ->searchable()
                        ->required()
                        ->options(TicketTypeEnum::class)
                        ->columnSpanFull(),
                    TextInput::make('name')
                        ->required()
                        ->minLength(3)
                        ->maxLength(255),
                    Textarea::make('content')
                        ->required()
                        ->minLength(10)
                        ->rows(4),
                    SpatieMediaLibraryFileUpload::make('images')
                        ->columnSpanFull()
                        ->collection('attachments')
                        ->maxFiles(5)
                        // Gli stessi mime della collezione `attachments` (Ticket::registerMediaCollections): ammettere `image/*` faceva passare
                        // SVG, GIF, WebP e la creazione dei media falliva dopo Ticket::create (ticket orfano, SEC-04 DCP-49).
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                        ->maxSize(10 * 1024),
                ])
                ->columnSpanFull(),
            'authorSection' => Section::make((string) __('fixcity::ticket_form.sections.author.heading'))
                ->description((string) __('fixcity::ticket_form.sections.author.description'))
                ->schema([
                    Html::make(static::getAuthorHtml(...))->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }

    public static function getAuthorHtml(): HtmlString
    {
        $user = Filament::auth()->user();
        $name = e((string) ($user?->name ?? ''));
        $fiscalCode = e((string) ($user?->fiscal_code ?? $user?->codice_fiscale ?? ''));

        return new HtmlString(
            '<div class="ticket-author-card">'
            .'<strong class="ticket-author-card__name">'.$name.'</strong>'
            .'<dl class="ticket-author-card__details">'
            .'<div><dt>'.e(__('fixcity::ticket_form.fields.author_fiscal_code.label')).'</dt>'
            .'<dd>'.$fiscalCode.'</dd></div>'
            .'</dl>'
            .'<button type="button" class="ticket-author-card__toggle" x-data x-on:click="$el.previousElementSibling.classList.toggle(\'hidden\')">'
            .e(__('fixcity::ticket_form.fields.author.show_more')).'</button>'
            .'</div>'
        );
    }
}
