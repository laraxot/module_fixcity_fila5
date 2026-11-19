<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Actions\GetMissingEnteFieldsAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Actions\SubmitTicketOnceAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Filament\Concerns\HasTicketAuthorData;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Actions\Auth\HasVerifiedIdentityAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

use function Safe\parse_url;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    use HasTicketAuthorData;

    /**
     * View resolution is intentionally left to XotBaseWidget::resolveView(),
     * which picks 'pub_theme::filament.widgets.create-ticket-wizard'
     * (Themes/Sixteen) when it exists, falling back to
     * 'fixcity::filament.widgets.create-ticket-wizard' (this module)
     * otherwise. Do not declare a $view property here: it would bypass
     * that convention-based lookup (see GetViewByClassAction) and the
     * theme override would silently stop being used.
     */

    /** @var array<string, mixed> */
    #[Locked]
    public array $blockData = [];

    #[Locked]
    public string $confirmationLocale = 'it';

    /**
     * Token di questo invio, generato al mount: due "Invia" dello stesso modulo (doppio clic) restituiscono la stessa segnalazione
     * (SubmitTicketOnceAction, DCP-286). Locked: il client non lo sceglie.
     */
    #[Locked]
    public string $submissionToken = '';

    /** Vero dopo un "Invia" senza utente (sessione scaduta): la vista mostra l'avviso con "Accedi di nuovo". */
    #[Locked]
    public bool $sessionExpired = false;

    public static string $resource = TicketResource::class;

    /**
     * @param  array<string, mixed>  $blockData
     */
    public function mount(array $blockData = []): void
    {
        // PRD-02: la pagina /tickets/create servita dal tema non applica il middleware della pagina Folio del modulo
        // (Folio si ferma alla prima pagina che corrisponde). Il controllo all'apertura evita di compilare tutto il modulo
        // per poi essere rimandati alla verifica all'invio. Gli ospiti li gestisce il middleware `auth` della pagina.
        $user = Filament::auth()->user();
        if ($user !== null && ! app(HasVerifiedIdentityAction::class)->execute($user)) {
            $this->redirect(route('verification.notice'));

            return;
        }

        $this->blockData = $blockData;
        $this->confirmationLocale = LaravelLocalization::getCurrentLocale();
        $this->submissionToken = (string) Str::uuid();
        $this->form->fill(TicketForm::getDefaultFormState());
    }

    /**
     * @return array<string, Step>
     */
    public function getSteps(): array
    {
        return app(TicketForm::class)->getSteps();
    }

    protected function hasSkippableSteps(): bool
    {
        return false;
    }

    public function save(): void
    {
        // Sessione scaduta o uscita da un'altra scheda: la pagina e' ancora aperta ma l'utente non c'e' piu'. Prima l'errore finiva in un
        // bag che nessuna vista mostrava (FO-3-06): ora la vista mostra l'avviso con "Accedi di nuovo".
        $userId = Filament::auth()->id();
        $this->sessionExpired = ! $userId;
        if (! $userId) {
            return;
        }

        // PRD-02: le richieste Livewire non ripassano dal middleware della pagina Folio; stesso controllo qui (email o SPID/CIE).
        if (! app(HasVerifiedIdentityAction::class)->execute(Filament::auth()->user())) {
            $this->redirect(route('verification.notice'));

            return;
        }

        $policy = app(GetPublishedPrivacyPolicyAction::class)->execute();
        if ($policy === null) {
            $this->addError('data.privacyAccepted', __('fixcity::ticket.privacy.not_configured'));

            return;
        }

        // PRD-04: l'informativa esiste ma i dati dell'ente (titolare, DPO, PEC...) non sono compilati in `config/<tenant>/ente.php`:
        // il cittadino presterebbe il consenso su un testo con segnaposto `[[ente.*]]`.
        $missingEnteFields = app(GetMissingEnteFieldsAction::class)->execute($policy);
        if ($missingEnteFields !== []) {
            Log::warning('Fixcity ticket submission refused: authority (ente) data used by the privacy policy is not configured.', [
                'missing_fields' => $missingEnteFields,
            ]);
            $this->addError('data.submit', __('fixcity::ticket_submit.ente_not_configured'));

            return;
        }

        $once = app(SubmitTicketOnceAction::class);
        // Secondo "Invia" dello stesso modulo: stessa conferma, senza rivalidare (gli allegati del primo invio sono gia' stati spostati).
        $sent = $once->existing((string) $userId, $this->submissionToken);
        if ($sent instanceof Ticket) {
            $this->redirectAfterSuccess($sent);

            return;
        }

        /** @var array<string, mixed> $data */
        // Resolve and validate form state without saving relationships yet:
        // the Media Library field needs the persisted Ticket as its owner.
        $data = $this->form->getState(shouldCallHooksBefore: false);

        $data['owner_id'] = $userId;
        // Stato e priorita' iniziali li decide il server: `status` e `priority` sono campi Hidden e il client puo' riscriverli
        // (`$wire.set('data.status', 'closed')`): senza questo un cittadino creerebbe ticket gia' aperti o chiusi (SEC-01, DCP-49).
        $data['status'] = TicketStatusEnum::PENDING->value;
        $data['priority'] = TicketPriorityEnum::LOW->value;

        try {
            $ticket = $once->execute((string) $userId, $this->submissionToken, fn (): Ticket => $this->createTicket($data));
        } catch (LockTimeoutException) {
            $this->addError('data.submit', __('fixcity::ticket_submit.in_progress'));

            return;
        }

        $this->redirectAfterSuccess($ticket);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createTicket(array $data): Ticket
    {
        // Allegati nella stessa transazione del ticket (DB-06): se il salvataggio di una foto fallisce non resta un ticket senza foto,
        // non parte la ricevuta e il token di invio non viene memorizzato, quindi il nuovo "Invia" crea una sola segnalazione.
        return app(CreateTicketAction::class)->execute(
            $data,
            $this->confirmationLocale,
            function (Ticket $ticket): void {
                $this->form->model($ticket);
                $this->form->saveRelationships();
            },
        );
    }

    /**
     * Stable submit entrypoint for Livewire forms and theme templates.
     */
    public function submit(): void
    {
        $this->save();
    }

    protected function redirectAfterSuccess(Ticket $ticket): void
    {
        $path = SafeStringCastAction::cast(
            $this->blockData['confirmation_path'] ?? '/tickets/confirmation'
        );

        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $locale = array_key_exists($this->confirmationLocale, $supportedLocales)
            ? $this->confirmationLocale
            : app()->getLocale();

        $localizedUrl = LaravelLocalization::getLocalizedURL($locale, $path);

        $redirectUrl = $localizedUrl !== false ? $localizedUrl : $path;
        $code = SafeStringCastAction::cast($ticket->code);
        if ($code !== '') {
            $redirectUrl .= (str_contains($redirectUrl, '?') ? '&' : '?').'code='.rawurlencode($code);
        }

        $this->redirect($redirectUrl);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'blockData' => $this->blockData,
            'pageTitle' => SafeStringCastAction::cast($this->blockData['title'] ?? __('fixcity::ticket.page.title.label')),
            'pageDescription' => SafeStringCastAction::cast($this->blockData['description'] ?? ''),
            'sessionExpired' => $this->sessionExpired,
            'loginAgainUrl' => $this->sessionExpired ? $this->loginAgainUrl() : '',
        ];
    }

    /**
     * Login nella lingua del wizard che, a fine accesso, riporta al wizard: `?redirect=` e' il meccanismo gia' usato da "Accedi per commentare"
     * (solo percorsi interni, StoreIntendedUrlAction).
     */
    private function loginAgainUrl(): string
    {
        $locale = array_key_exists($this->confirmationLocale, LaravelLocalization::getSupportedLocales())
            ? $this->confirmationLocale
            : app()->getLocale();
        $login = LaravelLocalization::getLocalizedURL($locale, '/auth/login');
        $wizardPath = parse_url((string) LaravelLocalization::getLocalizedURL($locale, '/tickets/create'), PHP_URL_PATH);

        return (is_string($login) ? $login : '/'.$locale.'/auth/login').'?redirect='.rawurlencode(is_string($wizardPath) ? $wizardPath : '/'.$locale.'/tickets/create');
    }
}
