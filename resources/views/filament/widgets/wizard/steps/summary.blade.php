@php
    $formData = $formData ?? [];
    // Normalizzazione per evitare errori di indice
    $location = $formData['location'] ?? [];
    $images = $formData['images'] ?? [];
    $typeId = $formData['type_id'] ?? null;
    $typeName = '';
    if ($typeId) {
        $type = $typeId instanceof \Modules\Fixcity\Enums\TicketTypeEnum 
            ? $typeId 
            : \Modules\Fixcity\Enums\TicketTypeEnum::tryFrom((string)$typeId);
        $typeName = $type ? $type->getLabel() : (string)$typeId;
    }
@endphp

<div class="wizard-summary-parity" x-data="{}">
    {{-- Sezione COSA --}}
    <section class="it-page-section mb-5" id="summary-section-cosa">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="h5 mb-0 fw-bold text-uppercase text-primary">
                {{ __('fixcity::segnalazione.sections.summary_cosa.label') !== 'fixcity::segnalazione.sections.summary_cosa.label' ? __('fixcity::segnalazione.sections.summary_cosa.label') : '1. Cosa' }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::segnalazione.actions.edit_action.label') ?? __('fixcity::segnalazione.sections.contacts.edit_action') ?? 'Modifica' }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::segnalazione.fields.type.label')  }}</dt>
            <dd class="col-sm-9 text-dark">{{ $typeName }}</dd>
        </dl>
    </section>

    {{-- Sezione DOVE --}}
    <section class="it-page-section mb-5" id="summary-section-dove">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="h5 mb-0 fw-bold text-uppercase text-primary">
                {{ __('fixcity::segnalazione.sections.summary_dove.label') !== 'fixcity::segnalazione.sections.summary_dove.label' ? __('fixcity::segnalazione.sections.summary_dove.label') : '2. Dove' }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::segnalazione.actions.edit_action.label') ?? __('fixcity::segnalazione.sections.contacts.edit_action') ?? 'Modifica' }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::segnalazione.fields.address.label') }}</dt>
            <dd class="col-sm-9 text-dark">
                {{ $location['address'] ?? ($location['latitude'] . ', ' . $location['longitude']) }}
            </dd>
        </dl>
        @if(isset($location['latitude']) && isset($location['longitude']))
        <div class="map-preview-container mt-3 rounded overflow-hidden border" style="height: 200px;">
            <div class="bg-light d-flex align-items-center justify-content-center h-100 text-muted small">
                <span class="d-flex align-items-center gap-2">
                   <svg class="icon icon-sm"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-map-marker"></use></svg>
                   {{ $location['latitude'] }}, {{ $location['longitude'] }}
                </span>
            </div>
        </div>
        @endif
    </section>

    {{-- Sezione DETTAGLI --}}
    <section class="it-page-section mb-5" id="summary-section-dettagli">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="h5 mb-0 fw-bold text-uppercase text-primary">
                {{ __('fixcity::segnalazione.sections.summary_dettagli.label') !== 'fixcity::segnalazione.sections.summary_dettagli.label' ? __('fixcity::segnalazione.sections.summary_dettagli.label') : '3. Dettagli' }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::segnalazione.actions.edit_action.label') ?? __('fixcity::segnalazione.sections.contacts.edit_action') ?? 'Modifica' }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::segnalazione.fields.title.label') }}</dt>
            <dd class="col-sm-9 text-dark fw-bold">{{ $formData['name'] ?? '' }}</dd>
            
            <dt class="col-sm-3 fw-semibold text-muted small uppercase mt-2">{{ __('fixcity::segnalazione.fields.details.label') }}</dt>
            <dd class="col-sm-9 text-dark mt-2">{{ $formData['content'] ?? '' }}</dd>

            @if(count($images) > 0)
            <dt class="col-sm-3 fw-semibold text-muted small uppercase mt-3">{{ __('fixcity::segnalazione.fields.images.label') }}</dt>
            <dd class="col-sm-9 mt-3">
                <div class="row row-cols-2 row-cols-md-4 g-2">
                    @foreach($images as $image)
                        <div class="col">
                            <div class="ratio ratio-1x1 bg-light border rounded overflow-hidden">
                                <div class="d-flex align-items-center justify-content-center text-muted small">
                                    <svg class="icon icon-sm"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-file"></use></svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </dd>
            @endif
        </dl>
    </section>

    {{-- Sezione SEGNALATORE --}}
    <section class="it-page-section mb-4" id="summary-section-segnalatore">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="h5 mb-0 fw-bold text-uppercase text-primary">
                {{ __('fixcity::segnalazione.sections.author.label') }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::segnalazione.actions.edit_action.label') ?? __('fixcity::segnalazione.sections.contacts.edit_action') ?? 'Modifica' }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::segnalazione.fields.name.label') }}</dt>
            <dd class="col-sm-9 text-dark">{{ $this->getAuthUserName() }}</dd>
            
            <dt class="col-sm-3 fw-semibold text-muted small uppercase mt-2">{{ __('fixcity::segnalazione.fields.email.label') }}</dt>
            <dd class="col-sm-9 text-dark mt-2">{{ $formData['email'] ?? '-' }}</dd>
        </dl>
    </section>
</div>

<style>
    .wizard-summary-parity .it-page-section h3 {
        letter-spacing: 0.05em;
        font-size: 0.9rem;
    }
    .wizard-summary-parity dl dt {
        font-size: 0.75rem;
        line-height: 1.5;
        padding-top: 0.25rem;
    }
    .wizard-summary-parity dl dd {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    .wizard-summary-parity .text-muted.small.uppercase {
        text-transform: uppercase;
    }
    .wizard-summary-parity .btn-link {
        text-decoration: none;
        color: #0066CC;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .wizard-summary-parity .btn-link:hover {
        text-decoration: underline;
    }
</style>
