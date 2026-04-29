<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    /** @var array<string, mixed> */
    public array $blockData = [];

    /**
     * Vista modulo (layout Design Comuni: sidebar step 2, stepper, parity CSS).
     * Override del default view di XotBaseWizardWidget per mantenere HTML parity.
     */
    protected string $view = 'fixcity::filament.widgets.ticket-create-wizard';

    /** @param array<string, mixed> $blockData */
    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->initWizardState();
    }

    /**
     * @return array<int, Step>
     */
    public function getWizardSteps(): array
    {
        return [
            $this->getStepByName('privacy')
                ->description(SafeStringCastAction::cast(__('fixcity::ticket_wizard.steps.privacy.description'))),
            $this->getStepByName('data')
                ->description(SafeStringCastAction::cast(__('fixcity::ticket_wizard.steps.data.description'))),
            $this->getStepByName('summary')
                ->description(SafeStringCastAction::cast(__('fixcity::ticket_wizard.steps.summary.description'))),
        ];
    }

    /**
     * @return array<int, Component>
     */
    public function getPrivacySchema(): array
    {
        return TicketForm::getFrontofficePrivacySchema(SafeStringCastAction::cast($this->blockData['privacy_link'] ?? '#'));
    }

    /**
     * @return array<int, Component>
     */
    public function getDataSchema(): array
    {
        return TicketForm::getDataSchema();
    }

    /**
     * @return array<int, Component>
     */
    public function getSummarySchema(): array
    {
        return TicketForm::getSummarySchema();
    }

    public function submit(): void
    {
        $this->validateWizardSubmission();

        try {
            $state = $this->prepareTicketData();

            $ticket = $this->createTicket($state);
            $this->dispatchEvents($ticket);

            $this->redirectAfterSuccess();
        } catch (\Throwable $exception) {
            $this->handleSubmissionError($exception);
        }
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
            $state['status'] = TicketStatusEnum::DRAFT->value;

            $ticket = $this->createTicket($state);

            if (TicketStatusEnum::DRAFT !== $ticket->status) {
                $ticket->forceFill(['status' => TicketStatusEnum::DRAFT])->saveQuietly();
            }

            // Redirect to draft confirmation page
            $slug = SafeStringCastAction::cast($this->blockData['draft_confirmation_slug']
                ?? $this->blockData['confirmation_slug']
                ?? config('fixcity.wizard.draft_confirmation_slug', 'segnalazione-04-conferma'));

            $url = route('tests.view', ['slug' => $slug]);
            $localizedUrl = LaravelLocalization::getLocalizedURL(
                LaravelLocalization::getCurrentLocale(),
                $url
            );

            $this->redirect($localizedUrl !== false ? $localizedUrl : $url);
        } catch (\Throwable $exception) {
            $this->handleSubmissionError($exception);
        }
    }

    public function render(): View
    {
        return view($this->view, [
            'blockData' => $this->blockData,
            'pageTitle' => SafeStringCastAction::cast($this->blockData['title'] ?? __('fixcity::segnalazione.page.title.label')),
            'pageDescription' => SafeStringCastAction::cast($this->blockData['description'] ?? ''),
        ]);
    }

    /**
     * Nasconde il footer nativo Filament: la navigazione frontoffice e' resa nel Blade Design Comuni.
     */
    public function configureWizardNextAction(Action $nextAction): Action
    {
        return $nextAction->hidden();
    }

    public function configureWizardPreviousAction(Action $previousAction): Action
    {
        return $previousAction->hidden();
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
                'lat' => null,
                'lng' => null,
                'address' => '',
                'provider' => null,
                'address_details' => [],
                'display_name' => '',
                'street' => '',
                'street_number' => '',
                'city' => '',
                'postcode' => '',
                'state' => '',
                'province' => '',
                'country' => '',
                'country_code' => '',
                'suburb' => '',
                'structured' => [],
                'raw' => null,
            ],
        ];
    }

    /**
     * Validazione specifica per il wizard submission
     */
    protected function validateWizardSubmission(): void
    {
        $this->form->getState();
    }

    /**
     * Prepara i dati per la creazione del ticket.
     *
     * @return array<string, mixed>
     */
    protected function prepareTicketData(): array
    {
        $state = $this->normalizeWizardFormState($this->form->getState());
        $location = $this->findWizardStateValue($state, 'location');

        // Rimuovere fields non necessari per il model
        unset($state['images'], $state['privacyAccepted'], $state['email']);

        $normalizedLocation = $this->normalizeLocationPayload($location);
        $state['location'] = [] !== $normalizedLocation ? $normalizedLocation : null;
        unset($state['address']);

        // Aggiungere owner_id se utente autenticato
        if (auth()->check()) {
            $state['owner_id'] = auth()->id();
        }

        return $state;
    }

    /**
     * Normalizza il payload mappa mantenendo `location` come source of truth.
     *
     * @return array<string, mixed>
     */
    protected function normalizeLocationPayload(mixed $location): array
    {
        if (\is_string($location)) {
            $decoded = json_decode($location, true);
            $location = \is_array($decoded) ? $decoded : [];
        }

        if (! \is_array($location)) {
            return [];
        }

        $lat = $location['lat'] ?? $location['latitude'] ?? null;
        $lng = $location['lng'] ?? $location['longitude'] ?? null;

        return array_filter([
            'lat' => is_numeric($lat) ? SafeStringCastAction::cast($lat) : null,
            'lng' => is_numeric($lng) ? SafeStringCastAction::cast($lng) : null,
            'address' => $this->normalizeLocationText($location['address'] ?? $location['display_name'] ?? null),
            'provider' => $this->normalizeLocationText($location['provider'] ?? null),
            'address_details' => \is_array($location['address_details'] ?? null) ? $location['address_details'] : null,
            'display_name' => $this->normalizeLocationText($location['display_name'] ?? null),
            'street' => $this->normalizeLocationText($location['street'] ?? null),
            'street_number' => $this->normalizeLocationText($location['street_number'] ?? null),
            'city' => $this->normalizeLocationText($location['city'] ?? null),
            'postcode' => $this->normalizeLocationText($location['postcode'] ?? null),
            'state' => $this->normalizeLocationText($location['state'] ?? null),
            'province' => $this->normalizeLocationText($location['province'] ?? null),
            'country' => $this->normalizeLocationText($location['country'] ?? null),
            'country_code' => $this->normalizeLocationText($location['country_code'] ?? null),
            'suburb' => $this->normalizeLocationText($location['suburb'] ?? null),
            'structured' => \is_array($location['structured'] ?? null) ? $location['structured'] : null,
            'raw' => $location['raw'] ?? null,
        ], static fn (mixed $item): bool => null !== $item && '' !== $item && [] !== $item);
    }

    protected function normalizeLocationText(mixed $value): ?string
    {
        if (! \is_string($value)) {
            return null;
        }

        $value = trim($value);

        return '' !== $value ? $value : null;
    }

    /**
     * Cerca un valore nello stato Filament anche quando il Wizard lo annida per step/container.
     *
     * @param array<string, mixed> $state
     */
    protected function findWizardStateValue(array $state, string $key): mixed
    {
        if (\array_key_exists($key, $state)) {
            return $state[$key];
        }

        foreach ($state as $value) {
            if (\is_array($value)) {
                $found = $this->findWizardStateValue($this->stringKeyed($value), $key);
                if (null !== $found) {
                    return $found;
                }
            }
        }

        return null;
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
            $state['status'] = TicketStatusEnum::PENDING->value;
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
    protected function redirectAfterSuccess(): void
    {
        $slug = SafeStringCastAction::cast($this->blockData['confirmation_slug']
            ?? config('fixcity.wizard.confirmation_slug', 'segnalazione-04-conferma'));

        $url = route('tests.view', ['slug' => $slug]);
        $localizedUrl = LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            $url
        );

        $this->redirect($localizedUrl !== false ? $localizedUrl : $url);
    }

    /**
     * Gestione errori con user-friendly notification
     */
    protected function handleSubmissionError(\Throwable $exception): void
    {
        // Aggiungi errore al form per mostrarlo nella UI
        $this->addError('submit', $exception->getMessage());

        // Invia notifica all'utente
        Notification::make()
            ->danger()
            ->title(SafeStringCastAction::cast(__('fixcity::segnalazione.errors.submit.title')))
            ->body($exception->getMessage())
            ->send();

        // Log dettagliato per il debug (solo in development)
        if (app()->isLocal()) {
            report($exception);
        }
    }
}
