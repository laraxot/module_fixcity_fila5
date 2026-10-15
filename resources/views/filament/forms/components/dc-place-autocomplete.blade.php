{{--
    Campo `location.address` del passo 2: markup di segnalazione-02-dati (`.cmp-input-autocomplete > .form-group` con
    `label.label-input.d-none`, `input[type=search].autocomplete` e `.link-wrapper > a.list-item` "Usa la tua posizione").
    Resta un campo Filament: stato Livewire sul suo state path (`data.location.address`, una chiave dello stato del CoordinatePicker),
    `required`, aria-* ed errori di validazione. Da usare con `->hiddenLabel()->view('fixcity::filament.forms.components.dc-place-autocomplete')`.

    "Usa la tua posizione" (solo Alpine): delega a `coordinate-picker-lit` piu' vicino (`_requestGeolocation()`), la stessa catena del
    pulsante della mappa: GPS se il browser lo permette, altrimenti posizione approssimativa da IP (solo centro, nessun punto) e, se il
    GPS e' negato o fallisce, silenzio. Il picker scrive lo stato `data.location` e, con l'indirizzo da Nominatim lato client, anche
    `address` (questo input). Nessun messaggio su HTTPS o permessi: l'utente non puo' risolverli. Nessuna dipendenza da Bootstrap JS.
    Suggerimenti mentre si digita: non implementati (`.autocomplete-list` ha `display:none` nello scope e `autocomplete-list-show` non ha regole).
--}}
@php
    $id = $getId();
    $statePath = $getStatePath();
    $hasError = $errors->has($statePath);
    $isFieldDisabled = $isDisabled();
    $isFieldRequired = $isRequired() && (! $isFieldDisabled);
    $placeholder = ((string) ($getPlaceholder() ?? __('fixcity::ticket_form.fields.location.placeholder'))) . ($isFieldRequired ? '*' : '');
    $texts = [
        'locating' => __('geo::address.geolocation.locating'),
    ];
@endphp
<div
    class="cmp-input-autocomplete"
    x-data="{
        message: '',
        locating: false,
        texts: @js($texts),
        picker() {
            for (let node = this.$root.parentElement; node; node = node.parentElement) {
                const found = node.querySelector('coordinate-picker-lit:not([readonly])')
                if (found) return found
            }
            return null
        },
        async locate() {
            if (this.locating) return
            const picker = this.picker()
            if (! picker || typeof picker._requestGeolocation !== 'function') return
            this.locating = true
            this.message = this.texts.locating
            try {
                await picker._requestGeolocation()
            } finally {
                this.locating = false
                this.message = ''
            }
        },
    }"
>
    <div class="form-group bg-white p-3 mb-0 mt-3">
        <label class="label-input d-none mb-2" for="{{ $id }}">{{ $placeholder }}</label>
        <input
            {{
                $attributes
                    ->merge($getExtraInputAttributes(), escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge([
                        'type' => 'search',
                        'class' => 'autocomplete',
                        'placeholder' => $placeholder,
                        'id' => $id,
                        'name' => $id,
                        'required' => $isFieldRequired,
                        'disabled' => $isFieldDisabled,
                        $applyStateBindingModifiers('wire:model') => $statePath,
                        'aria-invalid' => $hasError ? 'true' : null,
                        'aria-describedby' => $hasError ? $id . '-error' : null,
                    ])
            }}
        />
        @if ($hasError)
            <p id="{{ $id }}-error" class="form-feedback just-validate-error-label">{{ $errors->first($statePath) }}</p>
        @endif
        @unless ($isFieldDisabled)
            <div class="link-wrapper mt-3">
                <a class="list-item active icon-left" href="#" x-on:click.prevent="locate()" x-bind:aria-disabled="locating ? 'true' : null">
                    <span class="list-item-title-icon-wrapper">
                        <svg class="icon icon-sm icon-primary mb-1" aria-hidden="true"><use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-map-marker"></use></svg>
                        <span class="list-item-title t-primary">{{ __('geo::address.fields.address.use_my_location') }}</span>
                    </span>
                </a>
            </div>
            <p class="form-feedback just-validate-error-label" role="status" hidden x-bind:hidden="! message" x-text="message"></p>
        @endunless
    </div>
</div>
