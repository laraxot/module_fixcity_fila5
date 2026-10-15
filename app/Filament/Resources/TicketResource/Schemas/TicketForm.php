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
use Modules\Xot\Actions\Cast\SafeStringCastAction;
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
            // Front office: passo con markup di sito/segnalazione-02-dati (DCP-128). L'etichetta NON si cambia: da essa dipende la chiave `?step=`.
            'data' => Step::make('data')
                ->schema(array_values(static::getDataSchema(designComuni: true)))
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
                        static::reviewFormState($livewire),
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
     * Stato del form a chiavi stringa per l'action di riepilogo.
     *
     * `data_get()` torna `mixed` e il cast `(array)` propagherebbe chiavi `array-key`,
     * mentre l'action richiede `array<string, mixed>`: si tengono solo le chiavi stringa,
     * come faceva il precedente `array_filter(..., ARRAY_FILTER_USE_KEY)`.
     *
     * @return array<string, mixed>
     */
    public static function reviewFormState(object $livewire): array
    {
        $raw = data_get($livewire, 'data', []);

        if (! is_array($raw)) {
            return [];
        }

        $state = [];

        foreach ($raw as $key => $value) {
            if (is_string($key)) {
                $state[$key] = $value;
            }
        }

        return $state;
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
     * @return array<string, SchemaComponent>
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

    /**
     * Messaggi di validazione dei campi del passo dati (solo front office). `Modules/Lang` applica a OGNI campo `user::validation`, che in en e de e'
     * una copia dell'italiano e in es non esiste: l'errore "Il campo X e' obbligatorio" compariva in italiano in tutte le lingue (misurato 2026-10-13).
     *
     * @return array<string, string>
     */
    protected static function getDataStepValidationMessages(): array
    {
        return [
            'required' => (string) __('fixcity::ticket_form.validation.required'),
            'min' => (string) __('fixcity::ticket_form.validation.min'),
            'max' => (string) __('fixcity::ticket_form.validation.max'),
        ];
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
     * Passo "dati". `$designComuni = false` (default) e' lo schema piatto del back office, invariato; `true` e' il front office (wizard su pagine
     * `data-dc`, DCP-128): i campi Hidden restano al primo livello e le tre schede stanno in UN solo `View` (`wizard/data-step`: avviso, indice
     * laterale, colonna con schede e navigazione) con il markup di sito/segnalazione-02-dati. Stato e regole non cambiano (nessun state path nuovo).
     *
     * @return array<string, SchemaComponent>
     */
    public static function getDataSchema(bool $designComuni = false): array
    {
        $sections = [
            'locationSection' => static::getLocationSection($designComuni),
            'issueSection' => static::getIssueSection($designComuni),
            'authorSection' => static::getAuthorSection($designComuni),
        ];

        return [
            'priority' => Hidden::make('priority')->default('low')->dehydrated(),
            'status' => Hidden::make('status')->default('pending')->dehydrated(),
            ...($designComuni
                ? ['dataStep' => View::make('pub_theme::components.wizard.data-step')->schema(array_values($sections))->columnSpanFull()]
                : $sections),
        ];
    }

    protected static function getLocationSection(bool $designComuni): Section
    {
        $address = TextInput::make('location.address')
            ->placeholder((string) __('fixcity::ticket_form.fields.location.placeholder'))
            ->columnSpanFull();
        $map = CoordinatePicker::make('location')
            ->columnSpanFull()
            ->geolocateWhenEmpty()
            ->required()
            ->rule(static fn (): \Closure => static::coordinatesRule())
            ->reverseGeocoding();
        $section = Section::make((string) __('fixcity::ticket_form.sections.location.heading'))
            ->description((string) __('fixcity::ticket_form.sections.location.description'))
            ->schema([$address, $map])
            ->columnSpanFull();

        if (! $designComuni) {
            return $section;
        }

        // L'indirizzo e' il campo "Cerca un luogo*" del riferimento (obbligatorio: la squadra legge un luogo, non solo due numeri); la mappa e'
        // l'extra, in un blocco bianco sotto di esso, con un selettore stabile per i gate di parity.
        $address->hiddenLabel()->required()->validationMessages(static::getDataStepValidationMessages())->view('fixcity::filament.forms.components.dc-place-autocomplete');
        // L'aiuto "Indica il luogo del disservizio" ripeterebbe la descrizione della scheda: non serve sotto la mappa.
        $map->hiddenLabel()->helperText(null)->validationMessages(static::getDataStepValidationMessages())->extraFieldWrapperAttributes(['class' => 'bg-white p-3 mt-3', 'data-element' => 'location-map']);

        return $section->id('report-place')
            ->view('pub_theme::components.wizard.dc-section-card', ['cardClass' => 'p-big p-lg-4']);
    }

    protected static function getIssueSection(bool $designComuni): Section
    {
        $type = Select::make('type')
            ->required()
            ->options(TicketTypeEnum::class)
            ->columnSpanFull();
        $name = TextInput::make('name')
            ->required()
            ->minLength(3)
            ->maxLength(255);
        $content = Textarea::make('content')
            ->required()
            ->minLength(10)
            ->rows(4);
        $images = SpatieMediaLibraryFileUpload::make('images')
            ->columnSpanFull()
            ->collection('attachments')
            ->maxFiles(5)
            // Gli stessi mime della collezione `attachments` (Ticket::registerMediaCollections): ammettere `image/*` faceva passare
            // SVG, GIF, WebP e la creazione dei media falliva dopo Ticket::create (ticket orfano, SEC-04 DCP-49).
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->maxSize(10 * 1024);
        $section = Section::make((string) __('fixcity::ticket_form.sections.issue.heading'))
            ->columnSpanFull();

        if (! $designComuni) {
            return $section->schema([$type->searchable(), $name, $content, $images]);
        }

        // Select nativo con prima opzione "Tipo di disservizio*"; "Titolo" al posto di "Nome"; dettagli con limite di 200 caratteri APPLICATO
        // (regola `max:200`, non solo il testo); allegati con lo stesso campo Media Library ma client in stile Design Comuni.
        $messages = static::getDataStepValidationMessages();
        $type->native()->validationMessages($messages)->view('fixcity::filament.forms.components.dc-select');
        $name->label((string) __('fixcity::ticket_form.fields.title.label'))->validationMessages($messages)->view('fixcity::filament.forms.components.dc-text-input');
        $content->rows(2)->maxLength(200)->validationMessages($messages)->view('fixcity::filament.forms.components.dc-textarea');
        $images->validationMessages([])->view('fixcity::filament.forms.components.dc-file-upload');

        return $section->schema([$type, $name, $content, $images])
            ->id('report-info')
            ->view('pub_theme::components.wizard.dc-section-card', ['cardClass' => 'p-big', 'required' => true]);
    }

    protected static function getAuthorSection(bool $designComuni): Section
    {
        $section = Section::make((string) __('fixcity::ticket_form.sections.author.heading'))
            ->description((string) __('fixcity::ticket_form.sections.author.description'))
            ->columnSpanFull();

        if (! $designComuni) {
            return $section->schema([Html::make(static::getAuthorHtml(...))->columnSpanFull()]);
        }

        // Dati veri dell'utente loggato (stessa fonte del riepilogo: nome, email, telefono del profilo); la scheda sparisce, insieme alla sua voce
        // dell'indice, se non c'e' un utente. Nessun codice fiscale inventato: non ha una sorgente.
        return $section->schema([
            View::make('pub_theme::components.wizard.data-author')
                ->viewData(static fn (): array => [
                    'review' => app(BuildTicketReviewSummaryAction::class)->execute([], Filament::auth()->user()),
                    'email' => SafeStringCastAction::cast(data_get(Filament::auth()->user(), 'email')),
                ])
                ->columnSpanFull(),
        ])
            ->id('report-author')
            ->visible(static fn (): bool => Filament::auth()->check())
            ->view('pub_theme::components.wizard.dc-section-card');
    }

    public static function getAuthorHtml(): HtmlString
    {
        $user = Filament::auth()->user();
        $name = e(SafeStringCastAction::cast(data_get($user, 'name')));
        $fiscalCode = e(SafeStringCastAction::cast(data_get($user, 'fiscal_code') ?? data_get($user, 'codice_fiscale')));

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
