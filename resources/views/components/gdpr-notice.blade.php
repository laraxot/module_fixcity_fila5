{{--
    Passo 1 del wizard: stesso markup di segnalazione-01-privacy (Design Comuni).
    Il testo e' l'informativa pubblicata dall'ente ($policyHtml, markdown sanitizzato, senza il titolo del
    documento: il titolo e' l'h1 della pagina). Nessuna copy qui dentro, solo chiavi di traduzione.
--}}
@if (is_string($policyHtml))
    {!! str_replace('<p>', '<p class="text-paragraph mb-lg-4">', $policyHtml) !!}
    <p class="text-paragraph mb-0">{{ __('fixcity::ticket.privacy.detail_prefix.text') }}<a href="{{ $privacyUrl }}" class="t-primary" target="_blank" rel="noopener">{{ __('fixcity::ticket.privacy.link.label') }}</a></p>
@else
    <p class="alert alert-warning mb-0" role="alert">
        {{ __('fixcity::ticket.privacy.not_configured') }}
        <a href="{{ $privacyUrl }}" class="t-primary" target="_blank" rel="noopener">
            {{ __('fixcity::ticket.privacy.open_notice') }}
        </a>
    </p>
@endif
