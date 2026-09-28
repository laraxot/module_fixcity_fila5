@if (is_string($policyHtml))
    <div class="prose mb-3">{!! $policyHtml !!}</div>
    <a href="{{ $privacyUrl }}" class="t-primary" target="_blank" rel="noopener">
        {{ __('fixcity::ticket.privacy.open_notice') }}
    </a>
@else
    <p class="alert alert-warning mb-0" role="alert">
        {{ __('fixcity::ticket.privacy.not_configured') }}
        <a href="{{ $privacyUrl }}" class="t-primary" target="_blank" rel="noopener">
            {{ __('fixcity::ticket.privacy.open_notice') }}
        </a>
    </p>
@endif
