<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    /**
     * Vista modulo (layout Design Comuni: sidebar step 2, stepper, parity CSS).
     * Override del default view di XotBaseWizardWidget per mantenere HTML parity.
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
                'address' => '',
                'address_details' => null,
                'street' => '',
                'street_number' => '',
                'city' => '',
                'postcode' => '',
                'state' => '',
                'province' => '',
                'country' => '',
                'country_code' => '',
                'suburb' => '',
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
                        // *
                        // NON CANCELLARE QUESTO - Active Picker
                        CoordinatePicker::make('location')
                            ->hiddenLabel()
                            ->zoom(15)
                            ->height('340px')
                            ->reverseGeocoding(),
                        // */
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
                            TextEntry::make('author_name')
                                ->state(fn (): string => $this->getAuthUserName())
                                ->icon('heroicon-o-user'),
                            TextEntry::make('author_fiscal_code')
                                ->state(fn (): string => $this->getAuthUserFiscalCode())
                                ->icon('heroicon-o-identification'),
                            TextEntry::make('author_phone')
                                ->state(fn (): string => $this->getAuthUserPhone())
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
            Section::make((string) __('fixcity::ticket_wizard.steps.summary.label'))
                ->description((string) __('fixcity::ticket_wizard.steps.summary.description'))
                ->compact()
                ->extraAttributes(['id' => 'report-summary', 'data-step-section' => 'summary'])
                ->schema([
                    Grid::make(['default' => 1, 'lg' => 2])
                        ->schema([
                            TextEntry::make('review_type')
                                ->state(fn (Get $get): string => $this->formatTicketTypeSummary($get('type_id'))),
                            TextEntry::make('review_name')
                                ->state(fn (Get $get): string => (string) ($get('name') ?? '')),
                            TextEntry::make('review_content')
                                ->state(fn (Get $get): string => (string) ($get('content') ?? ''))
                                ->columnSpanFull(),
                            TextEntry::make('review_email')
                                ->state(fn (Get $get): string => (string) ($get('email') ?? '')),
                            TextEntry::make('review_location')
                                ->state(fn (Get $get): string => $this->formatLocationSummary($get('location'))),
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
        if ($value instanceof TicketTypeEnum) {
            return $value->getLabel();
        }

        if (null === $value || '' === $value) {
            return '';
        }

        $type = TicketTypeEnum::tryFrom((string) $value);

        return $type?->getLabel() ?? (string) $value;
    }

    protected function formatLocationSummary(mixed $location): string
    {
        if (! \is_array($location)) {
            return '';
        }

        $address = trim((string) ($location['address'] ?? ''));
        if ('' !== $address) {
            return $address;
        }

        $latitude = $location['latitude'] ?? null;
        $longitude = $location['longitude'] ?? null;
        if (is_numeric($latitude) && is_numeric($longitude)) {
            return \sprintf('%s, %s', (string) $latitude, (string) $longitude);
        }

        return '';
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeSummaryImages(mixed $images): array
    {
        if (! \is_array($images)) {
            return [];
        }

        return array_values(array_filter($images, static fn (mixed $image): bool => \is_string($image) && '' !== $image));
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
        $this->getForm('form')->validate();
    }

    /**
     * Prepara i dati per la creazione del ticket.
     *
     * @return array<string, mixed>
     */
    protected function prepareTicketData(): array
    {
        $state = $this->normalizeWizardFormState($this->getForm('form')->getState());

        // Rimuovere fields non necessari per il model
        unset($state['images'], $state['privacyAccepted']);

        // Estrarre latitude e longitude dal campo location se presente
        if (isset($state['location']) && \is_array($state['location'])) {
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
     * Salva la segnalazione come bozza (draft).
     */
    public function saveDraft(): void
    {
        $this->validateWizardSubmission();

        try {
            $state = $this->prepareTicketData();

            // Force status to draft
            $state['status'] = \Modules\Fixcity\Enums\TicketStatusEnum::DRAFT->value;

            $ticket = $this->createTicket($state);

            $this->dispatchEvents($ticket);

            // Redirect to draft confirmation page
            $slug = $this->blockData['draft_confirmation_slug']
                ?? $this->blockData['confirmation_slug']
                ?? config('fixcity.wizard.draft_confirmation_slug', 'segnalazione-04-conferma');

            $url = route('tests.view', ['slug' => $slug]);
            $localizedUrl = LaravelLocalization::getLocalizedURL(
                LaravelLocalization::getCurrentLocale(),
                $url
            ) ?: $url;

            $this->redirect($localizedUrl);
        } catch (\Throwable $e) {
            $this->handleSubmissionError($e);
        }
    }

    /**
     * Crea il record nel database.
     *
     * @param array<string, mixed> $state
     */
    protected function createTicket(array $state): Ticket
    {
        // Set default status if not provided
        if (! isset($state['status'])) {
            $state['status'] = \Modules\Fixcity\Enums\TicketStatusEnum::PENDING->value;
        }

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
    public function configureWizardNextAction(Action $nextAction): Action
    {
        return $nextAction->hidden();
    }

    public function configureWizardPreviousAction(Action $previousAction): Action
    {
        return $previousAction->hidden();
    }

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

        return new HtmlString(\sprintf(
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
        if (null === $user) {
            return '';
        }

        return (string) (data_get($user, 'name')
            ?? trim(((string) data_get($user, 'first_name', '')).' '.((string) data_get($user, 'last_name', '')))
        );
    }

    protected function getAuthUserFiscalCode(): string
    {
        $user = $this->getAuthUser();
        if (null === $user) {
            return '';
        }

        return (string) (data_get($user, 'fiscal_code')
            ?? data_get($user, 'codice_fiscale')
            ?? '');
    }

    protected function getAuthUserPhone(): string
    {
        $user = $this->getAuthUser();
        if (null === $user) {
            return '';
        }

        return (string) (data_get($user, 'phone')
            ?? data_get($user, 'mobile')
            ?? data_get($user, 'telefono')
            ?? '');
    }
}
