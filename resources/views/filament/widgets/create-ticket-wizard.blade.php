{{--
  Wizard widget template for Fixcity ticket creation.
  
  Architecture:
  - Module provides: page title, description (via getViewData in widget)
  - XotBaseWizardWidget provides: form schema with Wizard component
  - pub_theme::components.wizard provides: Design Comuni visual styling
  
  The template provides wrapper for page content; the Wizard component
  renders via {{ $this->form }} which delegates to theme for appearance.
--}}
@php
    // Step 2 is the "Dati della segnalazione" step
    $currentStep = $this->getCurrentStepIndex() + 1;
    $isDataStep = $currentStep === 2;
@endphp

<div class="segnalazione-wizard-container" data-wizard-step="{{ $currentStep }}">
    {{-- Skip link for accessibility --}}
    <a class="skip-link visually-hidden focusable" href="#wizard-main-content">
        {{ __('fixcity::segnalazione.wizard_a11y.skip_to_main.label') }}
    </a>

    <div class="container">
        {{-- Page heading --}}
        <div class="row">
            <div class="col-12">
                <h1 class="title-xxlarge mb-4">
                    {{ $pageTitle ?? __('fixcity::segnalazione.page.title.label') }}
                </h1>
                @if(!empty($pageDescription))
                    <p class="text-description mb-4">{{ $pageDescription }}</p>
                @endif
            </div>
        </div>

        {{-- Required fields note --}}
        <p class="text-sm text-muted mb-3">
            <span class="text-danger">*</span> {{ __('fixcity::segnalazione.fields.required_note.label') }}
        </p>

        {{-- Main content grid --}}
        <div class="row">
            {{-- Sidebar for data step --}}
            @if($isDataStep)
                <div class="col-lg-3 d-none d-lg-block">
                    @include('pub_theme::components.wizard.sidebar', [
                        'steps' => [],
                        'currentStep' => $currentStep,
                    ])
                </div>
            @endif

            {{-- Wizard content --}}
            <div class="col-12 {{ $isDataStep ? 'col-lg-9' : 'col-lg-8' }} col-xl-9" id="wizard-main-content">
                <x-filament-widgets::widget>
                    {{ $this->form }}
                </x-filament-widgets::widget>
            </div>
        </div>
    </div>
</div>