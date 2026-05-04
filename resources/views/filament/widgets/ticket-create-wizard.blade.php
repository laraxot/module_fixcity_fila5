{{-- Design Comuni parity: Tailwind layout + Filament icons; markup Filament resta sorgente di verità --}}
@php
    $currentStep = $this->getWizardDisplayStep();
    $isDataStep = $currentStep === 2;
@endphp

<div class="segnalazione-wizard-root ticket-wizard-root" data-wizard-step="{{ $currentStep }}">
    <a class="ticket-wizard-skiplink" href="#segnalazione-wizard-main">
        {{ __('fixcity::segnalazione.wizard_a11y.skip_to_main.label') }}
    </a>

    <div class="mx-auto w-full max-w-7xl px-4 lg:px-6">
        <div class="cmp-heading pb-3 pb-4 lg:pb-4">
            <h1 class="title-xxxlarge">
                {{ $pageTitle ?? __('fixcity::segnalazione.page.title.label') }}
            </h1>
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
                id="segnalazione-wizard-main"
                role="region"
                aria-label="{{ __('fixcity::segnalazione.wizard_a11y.main_region.label') }}"
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
                    </div>
                    <x-filament-actions::modals />
                </x-filament-widgets::widget>
            </div>
        </div>
    </div>
</div>
