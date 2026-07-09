@php
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
@endphp

@php
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
@endphp

@php
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
@endphp

@php
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
// Fixcity Blade view — see Modules/Fixcity/docs/wiki.
@endphp

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
    {{--
        Lingua dominio Ticket (ticket.php): stesso bounded context di TicketForm / Filament —
        NON usare qui fixcity::segnalazione.* (copy frontoffice/CMS).
        Rif: laravel/Modules/Fixcity/docs/wiki/concepts/fixcity-ticket-vs-segnalazione-lang.md
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
    --}}
    {{-- Sezione COSA --}}
    <section class="it-page-section mb-5" id="summary-section-cosa">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="h5 mb-0 fw-bold text-uppercase text-primary">
                {{ __('fixcity::ticket.sections.summary_cosa.label') }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::ticket.sections.contacts.edit_action') }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::ticket.fields.type.label')  }}</dt>
            <dd class="col-sm-9 text-dark">{{ $typeName }}</dd>
        </dl>
    </section>

    {{-- Sezione DOVE --}}
    <section class="it-page-section mb-5" id="summary-section-dove">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h3 class="h5 mb-0 fw-bold text-uppercase text-primary">
                {{ __('fixcity::ticket.sections.summary_dove.label') }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::ticket.sections.contacts.edit_action') }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::ticket.fields.address.label') }}</dt>
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
                {{ __('fixcity::ticket.sections.summary_dettagli.label') }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::ticket.sections.contacts.edit_action') }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::ticket.fields.title.label') }}</dt>
            <dd class="col-sm-9 text-dark fw-bold">{{ $formData['name'] ?? '' }}</dd>
            
            <dt class="col-sm-3 fw-semibold text-muted small uppercase mt-2">{{ __('fixcity::ticket.fields.content.label') }}</dt>
            <dd class="col-sm-9 text-dark mt-2">{{ $formData['content'] ?? '' }}</dd>

            @if(count($images) > 0)
            <dt class="col-sm-3 fw-semibold text-muted small uppercase mt-3">{{ __('fixcity::ticket.fields.images.label') }}</dt>
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
                {{ __('fixcity::ticket.sections.author.label') }}
            </h3>
            <a href="javascript:void(0)" class="btn btn-link btn-sm d-flex align-items-center gap-1" 
               wire:click="goToStep('data')">
                <svg class="icon icon-xs"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                <span>{{ __('fixcity::ticket.sections.contacts.edit_action') }}</span>
            </a>
        </div>
        <dl class="row g-0">
            <dt class="col-sm-3 fw-semibold text-muted small uppercase">{{ __('fixcity::ticket.fields.name.label') }}</dt>
            <dd class="col-sm-9 text-dark">{{ $this->getAuthUserName() }}</dd>
            
            <dt class="col-sm-3 fw-semibold text-muted small uppercase mt-2">{{ __('fixcity::ticket.fields.email.label') }}</dt>
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
