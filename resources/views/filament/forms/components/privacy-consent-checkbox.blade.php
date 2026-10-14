{{--
    Consenso privacy del passo 1: markup di segnalazione-01-privacy (`.form-check > .checkbox-body > input + label`).
    Il Checkbox Filament emette l'input dentro la label, senza il fratello `input + label` che il CSS Bootstrap Italia
    richiede per disegnare il riquadro. Stato, validazione e attributi aria restano quelli del Checkbox.
--}}
@php
    $statePath = $getStatePath();
    $hasError = $errors->has($statePath);
@endphp
{{--
    `data-field-wrapper` e `data-validation-error` sono i contrassegni che il gestore `form-validation-error` di Filament cerca per portare
    l'utente al campo in errore: espande i contenitori (il passo del wizard si attiva) e scorre fino al campo. Questa vista sostituisce il
    wrapper standard del campo, quindi li ripete: senza, un "Invia" con il consenso mancante (es. aprendo `?step=` direttamente) falliva
    in silenzio perche' l'errore stava nel passo 1, nascosto (DCP-146).
--}}
<div class="form-check mt-4 mb-3 mt-md-40 mb-lg-40" data-field-wrapper @if ($hasError) data-validation-error @endif>
    <div class="checkbox-body d-flex align-items-center">
        <input
            type="checkbox"
            {{
                $attributes
                    ->merge([
                        'id' => $getId(),
                        'disabled' => $isDisabled(),
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraInputAttributes(), escape: false)
                    ->merge([
                        ...$getAccessibilityAttributes(),
                        'required' => $isRequired() && (! $isDisabled()),
                    ], escape: false)
                    ->class(['fi-invalid' => $hasError])
            }}
        />
        <label class="title-small-semi-bold pt-1" for="{{ $getId() }}">{{ $getLabel() }}</label>
    </div>
    @if ($hasError)
        {{-- Il testo dell'errore e' gia' annunciato dallo span `#ticket-privacy-error` della shell (aria-describedby). --}}
        <p class="form-feedback just-validate-error-label" aria-hidden="true">{{ $errors->first($statePath) }}</p>
    @endif
</div>
