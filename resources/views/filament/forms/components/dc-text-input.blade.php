{{--
    Campo di testo del passo dati: markup di segnalazione-02-dati
    (`.text-area-wrapper > .form-group.cmp-input > label.cmp-input__label + input.form-control`).
    Uso: `TextInput::make('name')->view('fixcity::filament.forms.components.dc-text-input')`.
    Stato Livewire, `required`, errori e `aria-*` restano quelli del campo Filament. In errore il wrapper porta
    `data-field-wrapper` e il messaggio `data-validation-error`: il JS di Filament (scroll al primo errore) fa
    `closest('[data-field-wrapper]')` e senza lancerebbe un TypeError. Senza errori il tag resta identico al riferimento.
    Etichetta flottante: il CSS DC la alza solo con `label.active`, classe che nel riferimento mette il runtime Bootstrap Italia
    (`InputLabel`), assente nel tema: qui la mette Alpine (`focused` oppure stato Livewire non vuoto), senza Bootstrap JS.
    Scelte volute: il `placeholder()` di Filament non viene emesso (il riferimento non ne ha, il prompt e' la label);
    niente mask, prefissi/suffissi, datalist o rivelazione password (testo semplice).
--}}
@php
    $id = $getId();
    $statePath = $getStatePath();
    $isDisabledField = $isDisabled();
    $isReadOnlyField = $isReadOnly();
    $hasError = $hasValidationError();
    $mark = ($isMarkedAsRequired() && (! $isDisabledField)) ? '*' : '';
    $wrapperAttributes = $getExtraAttributeBag()
        ->merge($getExtraFieldWrapperAttributes(), escape: false)
        ->merge($hasError ? ['data-field-wrapper' => ''] : [], escape: false)
        ->class(['text-area-wrapper', 'p-3', 'px-lg-4', 'pt-lg-5', 'pb-lg-0', 'bg-white']);
@endphp
<div {{ $wrapperAttributes }}>
    <div class="form-group cmp-input mb-0" x-data="{ focused: false }" x-on:focusin="focused = true" x-on:focusout="focused = false">
        <label class="cmp-input__label" for="{{ $id }}" :class="{ 'active': focused || $wire.$get(@js($statePath)) }">{{ $getLabel() }}{{ $mark }}</label>
        <input
            {{
                $getExtraInputAttributeBag()
                    ->merge([
                        'type' => $getType(),
                        'id' => $id,
                        'autocapitalize' => $getAutocapitalize(),
                        'autocomplete' => $getAutocomplete(),
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabledField,
                        'inputmode' => $getInputMode(),
                        'maxlength' => $getMaxLength(),
                        'minlength' => $getMinLength(),
                        'readonly' => $isReadOnlyField,
                        'required' => $isRequired() && (! $isDisabledField) && (! $isReadOnlyField),
                        'aria-invalid' => $hasError ? 'true' : null,
                        'aria-describedby' => $hasError ? $id . '-error' : null,
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
                    ->class(['form-control', 'is-invalid' => $hasError])
            }}
        />
        @if ($hasError)
            <p id="{{ $id }}-error" data-validation-error class="form-feedback just-validate-error-label">{{ $errors->first($statePath) }}</p>
        @endif
    </div>
</div>
