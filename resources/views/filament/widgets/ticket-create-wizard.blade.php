{{-- Design Comuni parity: Blade wrapper only, Filament Wizard remains source of truth --}}
@php
    $currentStep = $this->wizardStartStep;
    $isDataStep = $currentStep === 2;
    $isSummaryStep = $currentStep === 3;
    $isPrivacyStep = !$isDataStep && !$isSummaryStep;
    $sprite = '/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg';
@endphp

<div class="segnalazione-wizard-root ticket-wizard-root" data-wizard-step="{{ $currentStep }}">
    <div class="container" id="main-container">

        {{-- Stepper header Bootstrap Italia --}}
        <div class="row">
            <div class="col-12">
                <div class="cmp-heading pb-3 pb-lg-4">
                    <h1 class="title-xxxlarge">{{ $pageTitle ?? __('fixcity::segnalazione.page.title.label') }}</h1>
                </div>
                <div class="steppers">
                    <div class="steppers-header">
                        <ul>
                            @foreach ($this->getWizardSteps() as $index => $step)
                                @php $pos = $index + 1; @endphp
                                <li class="{{ $pos < $currentStep ? 'confirmed' : ($pos === $currentStep ? 'active' : '') }}"
                                    @if ($pos === $currentStep) aria-current="step" @endif>
                                    {{ $step->getLabel() ?? 'Step ' . ($index + 1) }}
                                    @if ($pos < $currentStep)
                                        <svg aria-hidden="true" class="icon steppers-success">
                                            <use href="{{ $sprite }}#it-check"></use>
                                        </svg>
                                        <span
                                            class="visually-hidden">{{ __('fixcity::segnalazione.steps.confirmed.label') }}</span>
                                    @elseif($pos === $currentStep)
                                        <span
                                            class="visually-hidden">{{ __('fixcity::segnalazione.steps.active.label') }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <span aria-hidden="true" class="steppers-index">{{ $currentStep }}/3</span>
                    </div>
                </div>
                <p class="d-lg-none my-3 title-xsmall">
                    <span class="text-danger">*</span> {{ __('fixcity::segnalazione.fields.required_note.label') }}
                </p>
            </div>
        </div>

        {{-- Content row --}}
        <div class="row">

            {{-- Sidebar (step 2 only) --}}
            @if ($isDataStep)
                <div class="col-12 col-lg-3 d-lg-block d-none mb-4">
                    <div aria-labelledby="wizard-nav-title" class="cmp-navscroll sticky-top">
                        <nav aria-label="{{ __('fixcity::segnalazione.fields.sidebar_title.label') }}"
                            class="it-navscroll-wrapper navbar navbar-expand-lg">
                            <div class="navbar-custom" id="navbarNavProgress">
                                <div class="menu-wrapper">
                                    <div class="link-list-wrapper">
                                        <div class="accordion">
                                            <div class="accordion-item">
                                                <span class="accordion-header" id="wizard-nav-title">
                                                    <button aria-controls="wizard-nav-collapse" aria-expanded="true"
                                                        class="accordion-button pb-10 px-3"
                                                        data-bs-target="#wizard-nav-collapse" data-bs-toggle="collapse"
                                                        type="button">
                                                        {{ strtoupper(__('fixcity::segnalazione.fields.sidebar_title.label')) }}
                                                        <svg class="icon icon-xs right">
                                                            <use href="{{ $sprite }}#it-expand"></use>
                                                        </svg>
                                                    </button>
                                                </span>
                                                <div class="progress">
                                                    <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="0"
                                                        class="it-navscroll-progressbar progress-bar"
                                                        role="progressbar"></div>
                                                </div>
                                                <div aria-labelledby="wizard-nav-title"
                                                    class="accordion-collapse collapse show" id="wizard-nav-collapse"
                                                    role="region">
                                                    <div class="accordion-body">
                                                        <ul class="link-list" data-element="page-index">
                                                            <li class="nav-item"><a class="nav-link"
                                                                    href="#report-place"><span>{{ __('fixcity::segnalazione.fields.place.section.label') }}</span></a>
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link"
                                                                    href="#report-info"><span>{{ __('fixcity::segnalazione.fields.inefficiency.section.label') }}</span></a>
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link"
                                                                    href="#report-author"><span>{{ __('fixcity::segnalazione.fields.author.section.label') }}</span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            @endif

            {{-- Main content --}}
            <main class="col-12 {{ $isDataStep ? 'col-lg-9' : 'col-lg-10 offset-lg-1' }} it-form-wizard">

                <p class="d-none d-lg-block title-xsmall segnalazione-required-legend mb-4">
                    <span class="text-danger">*</span> {{ __('fixcity::segnalazione.fields.required_note.label') }}
                </p>

                {{-- Form Filament — single column --}}
                <x-filament-widgets::widget class="cmp-wizard-widget">
                    <div aria-live="polite" class="steppers-content">
                        {{-- Un solo <form>: Filament Schema già emette fi-sc-form; annidare <form> rompe DOM/Livewire. --}}
                        {{ $this->form }}

                        <nav aria-label="Step" class="steppers-nav mt-4">
                            <div class="steppers-actions d-flex flex-column flex-md-row gap-3 align-items-stretch justify-content-between w-100">
                                @if (!$isPrivacyStep)
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-bold steppers-btn-prev btn-prev" wire:click="previousStep">
                                        <svg aria-hidden="true" class="icon icon-sm me-1 segnalazione-back-icon"><use href="{{ $sprite }}#it-chevron-left"></use></svg>
                                        <span class="visually-hidden">{{ __('fixcity::segnalazione.actions.back.label') }}</span>
                                        <span aria-hidden="true">{{ __('fixcity::segnalazione.actions.back.label') }}</span>
                                    </button>
                                @endif

                                @if ($isSummaryStep)
                                    <div class="d-flex flex-column flex-md-row gap-2 ms-md-auto">
                                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold flex-fill" wire:click="saveDraft">
                                            {{ __('fixcity::segnalazione.actions.save.label') }}
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm fw-bold flex-fill" wire:click="submit">
                                            {{ __('fixcity::segnalazione.actions.submit.label') }}
                                        </button>
                                    </div>
                                @else
                                    <button type="button" class="btn btn-primary btn-sm fw-bold steppers-btn-confirm btn-next segnalazione-next-btn" wire:click="nextStep">
                                        {{ __('fixcity::segnalazione.actions.next.label') }}
                                    </button>
                                @endif
                            </div>
                        </nav>
                    </div>
                    <x-filament-actions::modals />
                </x-filament-widgets::widget>

            </main>
        </div>
    </div>
</div>

{{-- Inline wizard script removed. See docs/wiki/concepts/no-inline-blade-style-rule.md --}}
