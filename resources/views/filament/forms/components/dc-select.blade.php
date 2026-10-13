{{--
    Select nativo del passo dati: markup di segnalazione-02-dati
    (`.select-wrapper.select-partials > label.visually-hidden + select.u-grey-dark`).
    Uso: `Select::make('type')->native()->view('fixcity::filament.forms.components.dc-select')`.
    Stato Livewire, `required`, errori e `aria-*` restano quelli del campo Filament. In errore il wrapper porta
    `data-field-wrapper` e il messaggio `data-validation-error`: il JS di Filament (scroll al primo errore) fa
    `closest('[data-field-wrapper]')` e senza lancerebbe un TypeError. Senza errori il tag resta identico al riferimento.
    Scelte volute: la prima opzione ripete la label come nel riferimento (il `placeholder()` di Filament non
    viene usato); niente `optgroup` (le opzioni sono piatte); solo classi Bootstrap Italia, nessuna utility Tailwind.
--}}
@php
    $id = $getId();
    $statePath = $getStatePath();
    $isDisabledField = $isDisabled();
    $hasError = $hasValidationError();
    $mark = ($isMarkedAsRequired() && (! $isDisabledField)) ? '*' : '';
    $options = $getOptions();
    $current = $getRawState();
    $current = $current instanceof \BackedEnum ? $current->value : $current;
    $current = is_scalar($current) ? (string) $current : '';
    $wrapperAttributes = $getExtraAttributeBag()
        ->merge($getExtraFieldWrapperAttributes(), escape: false)
        ->merge($hasError ? ['data-field-wrapper' => ''] : [], escape: false)
        ->class(['select-wrapper', 'p-md-3', 'p-lg-4', 'pb-lg-0', 'select-partials']);
@endphp
<div {{ $wrapperAttributes }}>
    <label for="{{ $id }}" class="visually-hidden">{{ $getLabel() }}{{ $mark }}</label>
    <select
        {{
            $getExtraInputAttributeBag()
                ->merge([
                    'id' => $id,
                    'autofocus' => $isAutofocused(),
                    'disabled' => $isDisabledField,
                    'required' => $isRequired() && (! $isDisabledField),
                    'aria-invalid' => $hasError ? 'true' : null,
                    'aria-describedby' => $hasError ? $id . '-error' : null,
                    'wire:key' => $hasDynamicOptions() ? ($getLivewireKey() . '.' . substr(md5(serialize($options)), 0, 64)) : null,
                    $applyStateBindingModifiers('wire:model') => $statePath,
                ], escape: false)
                ->class(['u-grey-dark', 'is-invalid' => $hasError])
        }}
    >
        @if ($canSelectPlaceholder())
            <option {!! $current === '' ? 'selected="selected" ' : '' !!}value="">{{ $getLabel() }}{{ $mark }}</option>
        @endif
        @foreach ($options as $value => $label)
            <option value="{{ $value }}"{!! ($current !== '' && $current === (string) $value) ? ' selected="selected"' : '' !!}{!! $isOptionDisabled($value, $label) ? ' disabled="disabled"' : '' !!}>{{ $label }}</option>
        @endforeach
    </select>
    @if ($hasError)
        <p id="{{ $id }}-error" data-validation-error class="form-feedback just-validate-error-label">{{ $errors->first($statePath) }}</p>
    @endif
</div>
