{{--
    DCP-286 (FO-3-06): avviso di "Invia" senza utente (sessione scaduta o uscita da un'altra scheda). Il login si apre in una nuova scheda e,
    a fine accesso, riporta al wizard (`?redirect=`): questa pagina conserva i dati inseriti, quindi l'utente torna qui e preme di nuovo Invia.
    Stesso avviso "callout" del riepilogo (classi gia' presenti nel tema). Variabili dal widget (CreateTicketWizardWidget::getViewData): $sessionExpired, $loginAgainUrl.
--}}
@if ($sessionExpired ?? false)
    <div class="callout callout-highlight warning ps-3 pe-3 mb-4" role="alert" tabindex="-1" data-element="session-expired"
         x-data x-init="$nextTick(() => { $el.scrollIntoView({ block: 'center' }); $el.focus({ preventScroll: true }) })">
        <div class="callout-title mb-20 d-flex align-items-center">
            <span>{{ __('fixcity::ticket_submit.session_expired.title') }}</span>
        </div>
        <p class="titillium text-paragraph">{{ __('fixcity::ticket_submit.session_expired.text') }}</p>
        <a class="btn btn-primary btn-sm" href="{{ $loginAgainUrl }}" target="_blank" rel="noopener">
            {{ __('fixcity::ticket_submit.session_expired.login_again') }}
            <span class="visually-hidden">({{ __('fixcity::ticket_submit.session_expired.new_tab') }})</span>
        </a>
    </div>
@endif
