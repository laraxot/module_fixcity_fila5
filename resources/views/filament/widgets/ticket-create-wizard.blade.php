{{-- Design Comuni parity: Blade wrapper only, Filament Wizard remains source of truth --}}
@php
    $stepQuery = (string) request()->query('step', '');
    $isDataStep = str_contains($stepQuery, 'dati-della-segnalazione') || $stepQuery === '2';
    $isSummaryStep = str_contains($stepQuery, 'riepilogo') || $stepQuery === '3';
    $currentStep = $isSummaryStep ? 3 : ($isDataStep ? 2 : 1);
    $progressPercent = (int) round(($currentStep / 3) * 100);
@endphp

<div class="segnalazione-wizard-root ticket-wizard-root" data-wizard-step="{{ $currentStep }}">
    <div class="container" id="main-container">
        <div class="row">
            @if($isDataStep)
                <aside class="col-12 col-lg-3 mb-4 wizard-sidebar" aria-label="{{ __('fixcity::segnalazione.fields.sidebar_title.label') }}">
                    <div class="wizard-side-index-box sticky-top">
                        <h3 class="wizard-side-index-title">{{ __('fixcity::segnalazione.fields.sidebar_title.label') }}</h3>
                        <ul class="wizard-side-index" data-element="page-index">
                            <li class="nav-item"><span class="d-block py-1 text-paragraph-small">{{ __('fixcity::segnalazione.fields.place.section.label') }}</span></li>
                            <li class="nav-item"><span class="d-block py-1 text-paragraph-small">{{ __('fixcity::segnalazione.fields.inefficiency.section.label') }}</span></li>
                            <li class="nav-item"><span class="d-block py-1 text-paragraph-small">{{ __('fixcity::segnalazione.fields.author.section.label') }}</span></li>
                        </ul>
                    </div>
                </aside>
            @endif

            <main class="col-12 {{ $isDataStep ? 'col-lg-9' : 'col-lg-10' }} it-form-wizard">

                <h1 class="title-xxxlarge mb-4">{{ $pageTitle ?? __('fixcity::segnalazione.page.title.label') }}</h1>

                {{-- Stepper orizzontale Bootstrap Italia --}}
                <div class="steppers wizard-stepper mb-4" aria-label="{{ __('fixcity::segnalazione.page.title.label') }}">
                    <ul class="step-list">
                        @foreach($this->getWizardSteps() as $index => $step)
                        @php
                            $position = $index + 1;
                            $stepClass = $position < $currentStep ? 'completed' : ($position === $currentStep ? 'active' : 'disabled');
                        @endphp
                        <li class="step-item {{ $stepClass }}" @if($position === $currentStep)aria-current="step"@endif>
                            <span class="steppers-content" aria-label="{{ $step->getLabel() ?? 'Step '.($index+1) }}">
                                <span class="step-icon">{{ $index + 1 }}</span>
                                <span class="step-title">{{ $step->getLabel() ?? 'Step '.($index+1) }}</span>
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    <div class="progress-step">
                        <div class="progress-bar" style="width: {{ $progressPercent }}%" role="progressbar" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                            <span class="progress-bar-label">{{ $currentStep }}/3</span>
                        </div>
                    </div>
                </div>

                {{-- Required fields note --}}
                <p class="title-xsmall segnalazione-required-legend mb-4">
                    <span class="text-danger">*</span> {{ __('fixcity::segnalazione.fields.required_note.label') }}
                </p>

                {{-- Form Filament — single column --}}
                <x-filament-widgets::widget class="cmp-wizard-widget">
                    <form wire:submit="submit">
                        {{ $this->form }}
                    </form>
                    @php
                        $isPrivacyStep = !$isDataStep && !$isSummaryStep;
                    @endphp
                    @if(!$isSummaryStep)
                        <div class="wizard-actions mt-4">
                            @if(!$isPrivacyStep)
                                <button type="button" class="wizard-btn-back" wire:click="previousStep">
                                    ← {{ __('fixcity::segnalazione.actions.back.label') }}
                                </button>
                            @endif
                            <button type="button" class="wizard-btn-next" wire:click="nextStep">
                                {{ __('fixcity::segnalazione.actions.next.label') }} →
                            </button>
                        </div>
                    @else
                        <div class="wizard-actions mt-4">
                            <button type="submit" class="wizard-btn-next">
                                {{ __('fixcity::segnalazione.actions.next.label') }}
                            </button>
                        </div>
                    @endif
                    <x-filament-actions::modals />
                </x-filament-widgets::widget>

            </main>
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.querySelector('.page-content[data-slug]');
            if (!root) return;
            const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
            const nodes = [];
            while (walker.nextNode()) {
                if (/\|---LINE:\d+---\|/.test(walker.currentNode.textContent ?? '')) nodes.push(walker.currentNode);
            }
            nodes.forEach(n => { n.textContent = (n.textContent ?? '').replace(/\|---LINE:\d+---\|/g, ''); });
            const slug = root.getAttribute('data-slug');
            if (slug !== null && /\|---LINE:\d+---\|/.test(slug)) {
                root.setAttribute('data-slug', slug.replace(/\|---LINE:\d+---\|/g, ''));
            }
        });
    </script>
@endonce
