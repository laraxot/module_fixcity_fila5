{{--
    Area di testo del passo dati: markup di segnalazione-02-dati
    (`.cmp-text-area > .form-group > label.d-block + textarea.text-area + span.label`).
    Uso: `Textarea::make('content')->rows(2)->maxLength(200)->view('fixcity::filament.forms.components.dc-textarea')`.
    Il limite e' quello di `maxLength()`: attributo `maxlength` e testo di `span.label` (chiave `ticket_form.hints.max_length` con
    `:max`, ripiego in italiano se manca) vengono dallo stesso numero; senza `maxLength()` non c'e' ne' limite ne' testo.
    Stato Livewire, `required`, errori e `aria-*` restano quelli del campo Filament. In errore il wrapper porta
    `data-field-wrapper` e il messaggio `data-validation-error`: il JS di Filament (scroll al primo errore) fa
    `closest('[data-field-wrapper]')` e senza lancerebbe un TypeError. Senza errori il tag resta identico al riferimento.
    Etichetta flottante: il CSS DC la alza solo con `label.active`, classe che nel riferimento mette il runtime Bootstrap Italia
    (`InputLabel`), assente nel tema: qui la mette Alpine (`focused` oppure stato Livewire non vuoto), senza Bootstrap JS.
    Scelte volute: il `placeholder()` di Filament non viene emesso (il riferimento non ne ha); niente autosize ne'
    contatore in tempo reale (il riferimento ha solo il testo del limite; con `.is-invalid` il CSS DC lo nasconde).
--}}
@php
    $id = $getId();
    $statePath = $getStatePath();
    $isDisabledField = $isDisabled();
    $isReadOnlyField = $isReadOnly();
    $hasError = $hasValidationError();
    $mark = ($isMarkedAsRequired() && (! $isDisabledField)) ? '*' : '';
    $maxLength = $getMaxLength();
    // ponytail: __() su una chiave mancante crea la riga DB e fa scrivere il file lingua dall'auto-writer. Finche' `hints.max_length`
    // non esiste in lang/*/ticket_form.php si legge dal loader con testo di ripiego (stesso schema di dc-place-autocomplete); poi usare __().
    $hint = null;
    if ($maxLength !== null) {
        $line = Illuminate\Support\Arr::get(app('translator')->getLoader()->load(app()->getLocale(), 'ticket_form', 'fixcity'), 'hints.max_length');
        $hint = str_replace(':max', (string) $maxLength, is_string($line) ? $line : 'Inserire al massimo :max caratteri');
    }
    $describedBy = trim(($hint !== null ? $id . '-hint ' : '') . ($hasError ? $id . '-error' : ''));
    $wrapperAttributes = $getExtraAttributeBag()
        ->merge($getExtraFieldWrapperAttributes(), escape: false)
        ->merge($hasError ? ['data-field-wrapper' => ''] : [], escape: false)
        ->class(['cmp-text-area', 'm-0', 'p-3', 'px-lg-4', 'pt-lg-5', 'pb-lg-4', 'bg-white']);
@endphp
<div {{ $wrapperAttributes }}>
    <div class="form-group" x-data="{ focused: false }" x-on:focusin="focused = true" x-on:focusout="focused = false">
        <label for="{{ $id }}" class="d-block" :class="{ 'active': focused || $wire.$get(@js($statePath)) }">{{ $getLabel() }}{{ $mark }}</label>
        <textarea
            {{
                $getExtraInputAttributeBag()
                    ->merge([
                        'id' => $id,
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabledField,
                        'maxlength' => $maxLength,
                        'minlength' => $getMinLength(),
                        'readonly' => $isReadOnlyField,
                        'required' => $isRequired() && (! $isDisabledField) && (! $isReadOnlyField),
                        'rows' => $getRows(),
                        'aria-invalid' => $hasError ? 'true' : null,
                        'aria-describedby' => $describedBy !== '' ? $describedBy : null,
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
                    ->class(['text-area', 'is-invalid' => $hasError])
            }}
        ></textarea>
        @if ($hint !== null)
            <span id="{{ $id }}-hint" class="label">{{ $hint }}</span>
        @endif
        @if ($hasError)
            <p id="{{ $id }}-error" data-validation-error class="form-feedback just-validate-error-label">{{ $errors->first($statePath) }}</p>
        @endif
    </div>
</div>
