{{-- Design Comuni parity: Blade wrapper only, Filament Wizard remains source of truth --}}
@php
    $stepQuery = (string) request()->query('step', '');
    $isDataStep = str_contains($stepQuery, 'dati-della-segnalazione') || $stepQuery === '2';
    $isSummaryStep = str_contains($stepQuery, 'riepilogo') || $stepQuery === '3';
    $isPrivacyStep = !$isDataStep && !$isSummaryStep;
    $currentStep = $isSummaryStep ? 3 : ($isDataStep ? 2 : 1);
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
                            @foreach($this->getWizardSteps() as $index => $step)
                            @php $pos = $index + 1; @endphp
                            <li class="{{ $pos < $currentStep ? 'confirmed' : ($pos === $currentStep ? 'active' : '') }}"
                                @if($pos === $currentStep)aria-current="step"@endif>
                                {{ $step->getLabel() ?? 'Step '.($index+1) }}
                                @if($pos < $currentStep)
                                    <svg aria-hidden="true" class="icon steppers-success"><use href="{{ $sprite }}#it-check"></use></svg>
                                    <span class="visually-hidden">{{ __('fixcity::segnalazione.steps.confirmed.label') }}</span>
                                @elseif($pos === $currentStep)
                                    <span class="visually-hidden">{{ __('fixcity::segnalazione.steps.active.label') }}</span>
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
        <div class="row" x-data="{ accordionOpen: true }">

            {{-- Sidebar (step 2 only) --}}
            @if($isDataStep)
            <div class="col-12 col-lg-3 d-lg-block d-none mb-4">
                <div aria-labelledby="wizard-nav-title" class="cmp-navscroll sticky-top">
                    <nav aria-label="{{ __('fixcity::segnalazione.fields.sidebar_title.label') }}" class="it-navscroll-wrapper navbar navbar-expand-lg">
                        <div class="navbar-custom" id="navbarNavProgress">
                            <div class="menu-wrapper">
                                <div class="link-list-wrapper">
                                    <div class="accordion">
                                        <div class="accordion-item">
                                            <span class="accordion-header" id="wizard-nav-title">
                                                <button aria-controls="wizard-nav-collapse" aria-expanded="true"
                                                    class="accordion-button pb-10 px-3"
                                                    data-bs-target="#wizard-nav-collapse" data-bs-toggle="collapse"
                                                    type="button" @click="accordionOpen = !accordionOpen">
                                                    {{ strtoupper(__('fixcity::segnalazione.fields.sidebar_title.label')) }}
                                                    <svg class="icon icon-xs right"><use href="{{ $sprite }}#it-expand"></use></svg>
                                                </button>
                                            </span>
                                            <div class="progress">
                                                <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="0"
                                                    class="it-navscroll-progressbar progress-bar" role="progressbar"></div>
                                            </div>
                                            <div aria-labelledby="wizard-nav-title" class="accordion-collapse collapse show"
                                                id="wizard-nav-collapse" role="region" x-show="accordionOpen" x-cloak>
                                                <div class="accordion-body">
                                                    <ul class="link-list" data-element="page-index">
                                                        <li class="nav-item"><a class="nav-link" href="#report-place"><span>{{ __('fixcity::segnalazione.fields.place.section.label') }}</span></a></li>
                                                        <li class="nav-item"><a class="nav-link" href="#report-info"><span>{{ __('fixcity::segnalazione.fields.inefficiency.section.label') }}</span></a></li>
                                                        <li class="nav-item"><a class="nav-link" href="#report-author"><span>{{ __('fixcity::segnalazione.fields.author.section.label') }}</span></a></li>
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
            <main class="col-12 {{ $isDataStep ? 'col-lg-8 offset-lg-1' : 'col-lg-10 offset-lg-1' }} it-form-wizard">

                <p class="d-none d-lg-block title-xsmall segnalazione-required-legend mb-4">
                    <span class="text-danger">*</span> {{ __('fixcity::segnalazione.fields.required_note.label') }}
                </p>

                {{-- Form Filament — single column --}}
                <x-filament-widgets::widget class="cmp-wizard-widget">
                    <div aria-live="polite" class="steppers-content">
                        {{-- Un solo <form>: Filament Schema già emette fi-sc-form; annidare <form> rompe DOM/Livewire. --}}
                        {{ $this->form }}

                        <nav aria-label="Step" class="steppers-nav mt-4">
                            <div class="d-flex flex-column gap-3 align-items-stretch w-100">
                                @if(!$isPrivacyStep)
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-bold steppers-btn-prev btn-prev w-100 mb-2" wire:click="previousStep">
                                        <svg aria-hidden="true" class="icon icon-sm me-1" style="width:16px;height:16px;"><use href="{{ $sprite }}#it-chevron-left"></use></svg>
                                        <span class="visually-hidden">{{ __('fixcity::segnalazione.actions.back.label') }}</span>
                                        <span aria-hidden="true">{{ __('fixcity::segnalazione.actions.back.label') }}</span>
                                    </button>
                                @endif

                                @if($isSummaryStep)
                                    <div class="d-flex gap-2 w-100">
                                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold flex-fill" wire:click="saveDraft">
                                            {{ __('fixcity::segnalazione.actions.save_draft.label') }}
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

@once
    <style>
        /* Parity Design Comuni: keep one visible navigation row only. */
        .fi-sc-wizard-footer {
            display: none !important;
        }

        .segnalazione-next-btn {
            width: 100% !important;
            min-height: 48px !important;
            min-width: 100% !important;
            align-self: flex-start !important;
            margin-top: 0.5rem !important;
        }

        .segnalazione-wizard-root .steppers-nav {
            width: 100% !important;
            display: block !important;
        }

        .segnalazione-wizard-root .fi-sc,
        .segnalazione-wizard-root .fi-sc-wizard {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
        }

        .segnalazione-wizard-root .steppers-content {
            display: block !important;
            width: 100% !important;
        }

        @media (max-width: 991.98px) {
            .segnalazione-wizard-root .fi-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (min-width: 768px) {
            .segnalazione-next-btn {
                width: 348px !important;
                min-width: 348px !important;
            }
        }

        @media (min-width: 1024px) {
            .segnalazione-next-btn {
                width: 428px !important;
                min-width: 428px !important;
            }
        }

        /* Header parity: vedi `Sixteen/.../layouts/main.blade.php` (style fine <head>) + `header/v1.blade.php` (`theme-light-desk` su wrapper BI 2.18). */

        /* Allineamento hamburger/menu e logo */
        .navbar-custom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Nascondi la riga verde del stepper */
        .it-navscroll-progressbar {
            display: none !important;
        }
    </style>

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

            // Parity hard-stop: hide Filament default footer actions.
            document.querySelectorAll('.fi-sc-wizard-footer').forEach((el) => {
                el.style.setProperty('display', 'none', 'important');
            });

            // Header: niente colori inline — `.it-header-wrapper.is-segnalazione-crea` in tema Sixteen (`app.css`) + token slim; lo style inline batteva il CSS (!important) e rompeva parity (navbar vs fascia logo).
            if (root.getAttribute('data-slug') === 'tests.segnalazione-crea') {
                // Keep "Avanti" immediately under privacy checkbox on first step.
                const stepIndex = document.querySelector('.steppers-index')?.textContent?.trim() ?? '';
                if (stepIndex.startsWith('1/')) {
                    const nav = document.querySelector('.steppers-nav');
                    const privacyField = document.querySelector('[wire\\:partial$=\"privacyAccepted\"]');
                    if (nav && privacyField && privacyField.parentElement) {
                        privacyField.parentElement.insertBefore(nav, privacyField.nextSibling);
                    }
                }
            }
        });
    </script>
@endonce
