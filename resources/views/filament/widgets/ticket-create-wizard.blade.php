{{-- Design Comuni parity: Tailwind layout + Filament icons; markup Filament resta sorgente di verità --}}
@php
    $currentStep = $this->wizardStartStep;
    $isDataStep = $currentStep === 2;
    $isSummaryStep = $currentStep === 3;
    $isPrivacyStep = ! $isDataStep && ! $isSummaryStep;
@endphp

<div class="segnalazione-wizard-root ticket-wizard-root" data-wizard-step="{{ $currentStep }}">
    <a class="ticket-wizard-skiplink" href="#segnalazione-wizard-main">
        {{ __('fixcity::segnalazione.wizard_a11y.skip_to_main.label') }}
    </a>

    <div class="mx-auto w-full max-w-7xl px-4 lg:px-6">
        <div class="cmp-heading pb-3 pb-4 lg:pb-4">
            <h1 class="text-3xl font-bold text-[#007a52] lg:text-4xl">
                {{ $pageTitle ?? __('fixcity::segnalazione.page.title.label') }}
            </h1>
        </div>

        <div class="steppers">
            <div class="steppers-header">
                <ul class="step-list">
                    @foreach ($this->getWizardSteps() as $index => $step)
                        @php $pos = $index + 1; @endphp
                        <li
                            class="step-item {{ $pos < $currentStep ? 'completed' : ($pos === $currentStep ? 'active' : 'disabled') }}"
                            @if ($pos === $currentStep) aria-current="step" @endif
                        >
                            <span class="step-icon">{{ $pos }}</span>
                            <span class="step-title">{{ $step->getLabel() ?? 'Step '.$pos }}</span>
                            @if ($pos < $currentStep)
                                <x-filament::icon
                                    icon="heroicon-o-check-circle"
                                    class="ms-1 h-5 w-5 shrink-0 text-green-600"
                                    aria-hidden="true"
                                />
                                <span class="sr-only">{{ __('fixcity::segnalazione.steps.confirmed.label') }}</span>
                            @elseif ($pos === $currentStep)
                                <span class="sr-only">{{ __('fixcity::segnalazione.steps.active.label') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <span aria-hidden="true" class="steppers-index">{{ $currentStep }}/3</span>
            </div>
        </div>

        <p class="mb-3 text-sm lg:hidden">
            <span class="text-red-600">*</span> {{ __('fixcity::segnalazione.fields.required_note.label') }}
        </p>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            @if ($isDataStep)
                <div class="mb-4 hidden lg:col-span-3 lg:block">
                    <div aria-labelledby="wizard-nav-title" class="cmp-navscroll sticky-top">
                        <nav
                            aria-label="{{ __('fixcity::segnalazione.fields.sidebar_title.label') }}"
                            class="it-navscroll-wrapper navbar navbar-expand-lg"
                        >
                            <div class="navbar-custom" id="navbarNavProgress">
                                <div class="menu-wrapper">
                                    <div class="link-list-wrapper">
                                        <div class="accordion">
                                            <div class="accordion-item">
                                                <span class="accordion-header" id="wizard-nav-title">
                                                    <button
                                                        aria-controls="wizard-nav-collapse"
                                                        aria-expanded="true"
                                                        class="accordion-button flex items-center gap-2 pb-10 px-3"
                                                        data-bs-target="#wizard-nav-collapse"
                                                        data-bs-toggle="collapse"
                                                        type="button"
                                                    >
                                                        {{ strtoupper(__('fixcity::segnalazione.fields.sidebar_title.label')) }}
                                                        <x-filament::icon
                                                            icon="heroicon-m-chevron-down"
                                                            class="ms-auto h-4 w-4 shrink-0"
                                                            aria-hidden="true"
                                                        />
                                                    </button>
                                                </span>
                                                <div class="progress">
                                                    <div
                                                        aria-valuemax="100"
                                                        aria-valuemin="0"
                                                        aria-valuenow="0"
                                                        class="it-navscroll-progressbar progress-bar"
                                                        role="progressbar"
                                                    ></div>
                                                </div>
                                                <div
                                                    aria-labelledby="wizard-nav-title"
                                                    class="accordion-collapse collapse show"
                                                    id="wizard-nav-collapse"
                                                    role="region"
                                                >
                                                    <div class="accordion-body">
                                                        <ul class="link-list" data-element="page-index">
                                                            <li class="nav-item">
                                                                <a class="nav-link" href="#report-place">
                                                                    <span>{{ __('fixcity::segnalazione.fields.place.section.label') }}</span>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" href="#report-info">
                                                                    <span>{{ __('fixcity::segnalazione.fields.inefficiency.section.label') }}</span>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" href="#report-author">
                                                                    <span>{{ __('fixcity::segnalazione.fields.author.section.label') }}</span>
                                                                </a>
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

            <div
                role="main"
                id="segnalazione-wizard-main"
                @class([
                    'it-form-wizard w-full',
                    'lg:col-span-9' => $isDataStep,
                    'lg:col-span-10 lg:col-start-2' => ! $isDataStep,
                ])
            >
                <p class="segnalazione-required-legend title-xsmall mb-4 hidden text-sm lg:block">
                    <span class="text-red-600">*</span> {{ __('fixcity::segnalazione.fields.required_note.label') }}
                </p>

                <x-filament-widgets::widget class="cmp-wizard-widget">
                    <div aria-live="polite" class="steppers-content">
                        {{ $this->form }}

                        <nav aria-label="Step" class="steppers-nav mt-4">
                            <div
                                class="steppers-actions flex w-full flex-col items-stretch justify-between gap-3 align-middle md:flex-row"
                            >
                                @if (! $isPrivacyStep)
                                    <button
                                        type="button"
                                        class="steppers-btn-prev btn-prev flex items-center gap-1 font-semibold"
                                        wire:click="previousStep"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-o-arrow-left"
                                            class="h-4 w-4 shrink-0"
                                            aria-hidden="true"
                                        />
                                        <span class="sr-only">{{ __('fixcity::segnalazione.actions.back.label') }}</span>
                                        <span aria-hidden="true">{{ __('fixcity::segnalazione.actions.back.label') }}</span>
                                    </button>
                                @endif

                                @if ($isSummaryStep)
                                    <div class="ms-md-auto flex flex-col gap-2 md:flex-row">
                                        <button
                                            type="button"
                                            class="flex-1 font-semibold"
                                            wire:click="saveDraft"
                                        >
                                            {{ __('fixcity::segnalazione.actions.save_draft.label') }}
                                        </button>
                                        <button
                                            type="button"
                                            class="steppers-btn-confirm btn-next flex-1 font-semibold segnalazione-next-btn"
                                            wire:click="submit"
                                        >
                                            {{ __('fixcity::segnalazione.actions.submit.label') }}
                                        </button>
                                    </div>
                                @else
                                    <button
                                        type="button"
                                        class="steppers-btn-confirm btn-next segnalazione-next-btn ms-auto font-semibold"
                                        wire:click="nextStep"
                                    >
                                        {{ __('fixcity::segnalazione.actions.next.label') }}
                                    </button>
                                @endif
                            </div>
                        </nav>
                    </div>
                    <x-filament-actions::modals />
                </x-filament-widgets::widget>
            </div>
        </div>
    </div>
</div>
