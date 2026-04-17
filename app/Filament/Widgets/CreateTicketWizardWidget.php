<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;
use Modules\Geo\Filament\Forms\Components\LatitudeLongitudeInput;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    /**
     * Vista modulo (layout Design Comuni: sidebar step 2, stepper, parity CSS).
     * {@see GetViewByClassAction} risolve prima `pub_theme::filament.widgets.createticketwizard`:
     * senza override qui verrebbe usato il wrapper tema slim senza colonna sinistra.
     */
    protected string $view = 'fixcity::filament.widgets.ticket-create-wizard';

    public array $blockData = [];

    /** @param array<string, mixed> $blockData */
    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->initWizardState();
    }

    protected function getFormModel(): ?string
    {
        return null;
    }

    /**
     * Stato iniziale completo per tutti i campi del wizard.
     *
     * Senza chiavi presenti su `$data`, Livewire genera errori «Entangle» sui campi
     * (es. `data.content`) perché Alpine non trova la proprietà annidata.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    protected function defaultFormData(): array
    {
        return [
            'privacyAccepted' => false,
            'type_id' => null,
            'name' => '',
            'content' => '',
            'images' => [],
            'email' => '',
            'location' => [
                'latitude' => null,
                'longitude' => null,
            ],
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public function getPrivacySchema(): array
    {
        return [
            Text::make(fn (): HtmlString => $this->getPrivacyNoticeHtml())
                ->columnSpanFull(),
            Checkbox::make('privacyAccepted')
                ->accepted()
                ->dehydrated(false),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public function getDataSchema(): array
    {
        return [
            Section::make((string) __('fixcity::segnalazione.fields.place.section.label'))
                ->description((string) __('fixcity::segnalazione.sections.place.description'))
                ->compact()
                ->extraAttributes(['id' => 'report-place', 'data-step-section' => 'place'])
                ->schema([
                    LatitudeLongitudeInput::make('location')
                        ->hiddenLabel()
                        ->defaultCenter(41.9028, 12.4964)
                        ->defaultZoom(13)
                        ->mapHeight('340px')
                        ->showMap(true),
                ]),

            Section::make((string) __('fixcity::segnalazione.fields.inefficiency.section.label'))
                ->description((string) __('fixcity::segnalazione.sections.inefficiency.description'))
                ->compact()
                ->extraAttributes(['id' => 'report-info', 'data-step-section' => 'inefficiency'])
                ->schema([
                    Select::make('type_id')
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
                        Text::make(fn (): string => $this->getAuthUserName())
                            ->icon('heroicon-o-user'),
                        Text::make(fn (): string => __('fixcity::segnalazione.fields.fiscal_code.label').': '.$this->getAuthUserFiscalCode())
                            ->icon('heroicon-o-identification'),
                        Text::make(fn (): string => __('fixcity::segnalazione.fields.phone.label').': '.$this->getAuthUserPhone())
                            ->icon('heroicon-o-phone'),
                    ]),

                    TextInput::make('email')
                        ->helperText((string) __('fixcity::create_ticket_wizard.fields.email.helper_text'))
                        ->email()
                        ->maxLength(255),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public function getSummarySchema(): array
    {
        return [
            Section::make((string) __('fixcity::segnalazione.sections.summary.label'))
                ->compact()
                ->extraAttributes(['data-step-section' => 'summary'])
                ->schema([
                    Grid::make(['default' => 1, 'lg' => 2])->schema([
                        Text::make(fn (Get $get): string => (string) ($get('name') ?? ''))
                            ->weight('bold')
                            ->icon('heroicon-o-document'),

                        Text::make(function (Get $get): string {
                            $raw = $get('type_id');
                            $type = $raw instanceof TicketTypeEnum
                                ? $raw
                                : TicketTypeEnum::tryFrom((string) ($raw ?? ''));

                            return $type?->getLabel() ?? '';
                        })
                            ->badge()
                            ->icon('heroicon-o-tag'),

                        Text::make(function (Get $get): string {
                            $lat = trim((string) ($get('latitude') ?? ''));
                            $lng = trim((string) ($get('longitude') ?? ''));
                            if ($lat === '' && $lng === '') {
                                return '';
                            }

                            return $lat.', '.$lng;
                        })
                            ->columnSpanFull()
                            ->icon('heroicon-o-map-pin'),

                        Text::make(fn (Get $get): string => (string) ($get('content') ?? ''))
                            ->columnSpanFull()
                            ->icon('heroicon-o-chat-bubble-left-ellipsis'),

                        Text::make(fn (Get $get): string => (string) ($get('email') ?? ''))
                            ->icon('heroicon-o-envelope'),
                    ]),
                ]),
        ];
    }

    public function submit(): void
    {
        $this->validateWizardSubmission();

        try {
            $state = $this->prepareTicketData();

            $ticket = $this->createTicket($state);
            $this->dispatchEvents($ticket);

            $this->redirectAfterSuccess($ticket);

        } catch (\Throwable $e) {
            $this->handleSubmissionError($e);
        }
    }

    /**
     * Validazione specifica per il wizard submission
     */
    protected function validateWizardSubmission(): void
    {
        // Filament gestisce automaticamente la validation dei form fields
        // Qui possiamo aggiungere logiche custom se necessario
        $this->form->validate();
    }

    /**
     * Prepara i dati per la creazione del ticket.
     *
     * @return array<string, mixed>
     */
    protected function prepareTicketData(): array
    {
        $state = $this->normalizeWizardFormState($this->form->getState());

        // Rimuovere fields non necessari per il model
        unset($state['images'], $state['privacyAccepted']);

        // Estrarre latitude e longitude dal campo location se presente
        if (isset($state['location']) && is_array($state['location'])) {
            if (isset($state['location']['latitude']) && is_numeric($state['location']['latitude'])) {
                $state['latitude'] = (string) $state['location']['latitude'];
            }
            if (isset($state['location']['longitude']) && is_numeric($state['location']['longitude'])) {
                $state['longitude'] = (string) $state['location']['longitude'];
            }
            unset($state['location']);
        }

        // Assicurarsi che latitude e longitude siano presenti e siano stringhe
        foreach (['latitude', 'longitude'] as $coord) {
            if (isset($state[$coord]) && is_numeric($state[$coord])) {
                $state[$coord] = (string) $state[$coord];
            }
        }

        unset($state['address']);

        // Aggiungere owner_id se utente autenticato
        if (auth()->check()) {
            $state['owner_id'] = auth()->id();
        }

        return $state;
    }

    /**
     * Crea il record nel database.
     *
     * @param  array<string, mixed>  $state
     */
    protected function createTicket(array $state): Ticket
    {
        return Ticket::query()->create($state);
    }

    /**
     * Dispaccia gli eventi dopo la creazione
     */
    protected function dispatchEvents(Ticket $ticket): void
    {
        TicketCreatedEvent::dispatch($ticket);
    }

    /**
     * Redirect dopo successo con gestione multilingua
     */
    protected function redirectAfterSuccess(Ticket $ticket): void
    {
        $slug = (string) ($this->blockData['confirmation_slug']
            ?? config('fixcity.wizard.confirmation_slug', 'segnalazione-04-conferma'));

        $url = route('tests.view', ['slug' => $slug]);
        $localizedUrl = LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            $url
        ) ?: $url;

        $this->redirect($localizedUrl);
    }

    /**
     * Gestione errori con user-friendly notification
     */
    protected function handleSubmissionError(\Throwable $e): void
    {
        // Aggiungi errore al form per mostrarlo nella UI
        $this->addError('submit', $e->getMessage());

        // Invia notifica all'utente
        \Filament\Notifications\Notification::make()
            ->danger()
            ->title(__('fixcity::segnalazione.errors.submit.title'))
            ->body($e->getMessage())
            ->send();

        // Log dettagliato per il debug (solo in development)
        if (app()->isLocal()) {
            report($e);
        }
    }

    public function render(): View
    {
        return view($this->view, [
            'blockData' => $this->blockData,
            'pageTitle' => (string) ($this->blockData['title'] ?? __('fixcity::segnalazione.page.title.label')),
            'pageDescription' => (string) ($this->blockData['description'] ?? ''),
        ]);
    }

    /**
     * Step con label (Lang) e description come da [Filament wizard su CreateRecord](https://filamentphp.com/docs/5.x/resources/creating-records#using-a-wizard).
     *
     * @return array<int, Step>
     */
    public function getWizardSteps(): array
    {
        return [
            $this->getStepByName('privacy')
                ->description((string) __('fixcity::ticket_wizard.steps.privacy.description')),
            $this->getStepByName('data')
                ->description((string) __('fixcity::ticket_wizard.steps.data.description')),
            $this->getStepByName('summary')
                ->description((string) __('fixcity::ticket_wizard.steps.summary.description')),
        ];
    }

    protected function getPrivacyNoticeHtml(): HtmlString
    {
        $privacyLink = (string) ($this->blockData['privacy_link'] ?? '#');
        $intro = (string) __('fixcity::segnalazione.privacy.intro.text');
        $detailPrefix = (string) __('fixcity::segnalazione.privacy.detail_prefix.text');
        $linkLabel = (string) __('fixcity::segnalazione.privacy.link.label');

        return new HtmlString(sprintf(
            '<p class="mb-3">%s</p><p>%s<a href="%s" class="text-primary text-decoration-underline">%s</a></p>',
            e($intro),
            e($detailPrefix),
            e($privacyLink),
            e($linkLabel),
        ));
    }

    /**
     * Utente autenticato per blocchi read-only nello step dati (DRY).
     */
    protected function getAuthUser(): ?Authenticatable
    {
        return auth()->user();
    }

    protected function getAuthUserName(): string
    {
        $user = $this->getAuthUser();
        if ($user === null) {
            return '';
        }

        return (string) (data_get($user, 'name')
            ?? trim(((string) data_get($user, 'first_name', '')).' '.((string) data_get($user, 'last_name', '')))
        );
    }

    protected function getAuthUserFiscalCode(): string
    {
        $user = $this->getAuthUser();
        if ($user === null) {
            return '';
        }

        return (string) (data_get($user, 'fiscal_code')
            ?? data_get($user, 'codice_fiscale')
            ?? '');
    }

    protected function getAuthUserPhone(): string
    {
        $user = $this->getAuthUser();
        if ($user === null) {
            return '';
        }

        return (string) (data_get($user, 'phone')
            ?? data_get($user, 'mobile')
            ?? data_get($user, 'telefono')
            ?? '');
    }
}
