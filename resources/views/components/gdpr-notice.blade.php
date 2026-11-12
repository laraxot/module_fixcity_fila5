{{--
    Passo 1 del wizard: stesso markup di segnalazione-01-privacy (Design Comuni).
    DCP-456: avviso breve (`$notice`, chiave `fixcity::ticket.gdpr_notice.*`) + link
    all'informativa completa su /privacy. L'integrale non si ripete nel wizard.
    `$hasPolicy` falso = informativa non pubblicata: alert e checkbox disabilitato
    (gestito dallo schema, `GetPublishedPrivacyPolicyAction`).
--}}
@if ($hasPolicy)
    <p class="text-paragraph mb-lg-4">{{ $notice }}</p>
    <p class="text-paragraph mb-0">{{ __('fixcity::ticket.privacy.detail_prefix.text') }}<a href="{{ $privacyUrl }}" class="t-primary" target="_blank" rel="noopener">{{ __('fixcity::ticket.privacy.link.label') }}</a></p>
@else
    <p class="alert alert-warning mb-0" role="alert">
        {{ __('fixcity::ticket.privacy.not_configured') }}
        <a href="{{ $privacyUrl }}" class="t-primary" target="_blank" rel="noopener">
            {{ __('fixcity::ticket.privacy.open_notice') }}
        </a>
    </p>
@endif
