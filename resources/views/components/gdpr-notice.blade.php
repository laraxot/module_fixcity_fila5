{{--
    Passo 1 del wizard: stesso markup di segnalazione-01-privacy (Design Comuni).
    DCP-456: avviso breve (`$notice`, chiave `fixcity::ticket.gdpr_notice.*`) + link
    alle pagine legali. L'integrale non si ripete nel wizard.
    `$hasPolicy` falso = informativa non pubblicata: alert e checkbox disabilitato
    (gestito dallo schema, `GetPublishedPrivacyPolicyAction`).
--}}
@if ($hasPolicy)
    <p class="text-paragraph mb-lg-4">{{ $notice }}</p>
    <p class="text-paragraph mb-0">{{ __('fixcity::ticket.privacy.detail_prefix.text') }}<a href="{{ $legalLinks['privacy']['url'] }}" class="t-primary" target="_blank" rel="noopener">{{ $legalLinks['privacy']['label'] }}</a></p>
    <p class="text-paragraph mb-0 mt-2">
        {{ __('fixcity::ticket.privacy.links_prefix.text') }}
        @foreach (array_slice($legalLinks, 1) as $legalLink)
            @if (! $loop->first) · @endif
            <a href="{{ $legalLink['url'] }}" class="t-primary" target="_blank" rel="noopener">{{ $legalLink['label'] }}</a>
        @endforeach
    </p>
@else
    <p class="alert alert-warning mb-0" role="alert">
        {{ __('fixcity::ticket.privacy.not_configured') }}
        <a href="{{ $legalLinks['privacy']['url'] }}" class="t-primary" target="_blank" rel="noopener">
            {{ __('fixcity::ticket.privacy.open_notice') }}
        </a>
    </p>
@endif
