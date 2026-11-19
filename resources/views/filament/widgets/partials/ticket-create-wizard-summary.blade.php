@php
    /** @var \Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget $livewire */
    $livewire = $getLivewire();
    $state = is_array($livewire->data ?? null) ? $livewire->data : [];
    $issueTypeKey = (string) ($state['issueType'] ?? '');
    $issueTypeLabel = \Modules\Fixcity\Enums\TicketTypeEnum::tryFrom($issueTypeKey)?->label ?? $issueTypeKey;
    $emptyValue = (string) __('fixcity::create_ticket_wizard.summary.empty');
@endphp

<div class="cmp-card">
    <div class="card has-bkg-grey shadow-sm p-big p-lg-4">
        <div class="card-body p-0">
            <p class="text-paragraph mb-4">{{ __('fixcity::segnalazione.warning.message.label') }}</p>

            <dl class="mb-0">
                <dt class="form-label fw-semibold">{{ __('fixcity::create_ticket_wizard.fields.address.label') }}</dt>
                <dd class="mb-3">{{ $state['address'] ?: $emptyValue }}</dd>

                <dt class="form-label fw-semibold">{{ __('fixcity::create_ticket_wizard.fields.issueType.label') }}</dt>
                <dd class="mb-3">{{ $issueTypeLabel !== '' ? $issueTypeLabel : $emptyValue }}</dd>

                <dt class="form-label fw-semibold">{{ __('fixcity::create_ticket_wizard.fields.title.label') }}</dt>
                <dd class="mb-3">{{ $state['title'] ?: $emptyValue }}</dd>

                <dt class="form-label fw-semibold">{{ __('fixcity::create_ticket_wizard.fields.details.label') }}</dt>
                <dd class="mb-0">{{ $state['details'] ?: $emptyValue }}</dd>
            </dl>
        </div>
    </div>
</div>
