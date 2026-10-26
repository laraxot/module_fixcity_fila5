{{--
    Campo `location.address` del passo 2: markup di segnalazione-02-dati (`.cmp-input-autocomplete > .form-group` con
    `label.label-input.d-none`, `input[type=search].autocomplete` e `.link-wrapper > a.list-item` "Usa la tua posizione").
    Resta un campo Filament: stato Livewire sul suo state path (`data.location.address`, una chiave dello stato del CoordinatePicker),
    `required`, aria-* ed errori di validazione. Da usare con `->hiddenLabel()->view('fixcity::filament.forms.components.dc-place-autocomplete')`.

    "Usa la tua posizione" (solo Alpine): delega a `coordinate-picker-lit` piu' vicino (`_requestGeolocation()`), la stessa catena del
    pulsante della mappa: GPS se il browser lo permette, altrimenti posizione approssimativa da IP (solo centro, nessun punto) e, se il
    GPS e' negato o fallisce, silenzio. Il picker scrive lo stato `data.location` e, con l'indirizzo da Nominatim lato client, anche
    `address` (questo input). Nessun messaggio su HTTPS o permessi: l'utente non puo' risolverli. Nessuna dipendenza da Bootstrap JS.
    Cerca (DCP-286, FO-3-07): Invio, o uscire dal campo con un testo nuovo, lancia la ricerca che la mappa ha gia' (il pannello della lente di
    `coordinate-picker-lit`: stesso fornitore, nessun geocoder nuovo). Si pilota il pannello perche' il bundle del tema non espone un metodo di
    ricerca: `searchQuery` + clic sul suo pulsante; l'esito arriva dall'evento `address-selected`. Con il primo risultato il picker imposta punto e
    indirizzo; senza risultato il campo lo dice e indica la mappa. Suggerimenti mentre si digita: non implementati.
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
        'searching' => __('fixcity::ticket_place_search.searching'),
        'not_found' => __('fixcity::ticket_place_search.not_found'),
        'too_short' => __('fixcity::ticket_place_search.too_short'),
    ];
@endphp
<div
    class="cmp-input-autocomplete"
    x-data="{
        message: '',
        locating: false,
        searching: false,
        lastQuery: '',
        texts: @js($texts),
        picker() {
            for (let node = this.$root.parentElement; node; node = node.parentElement) {
                const found = node.querySelector('coordinate-picker-lit:not([readonly])')
                if (found) return found
            }
            return null
        },
        async search(force) {
            const query = (this.$refs.field.value ?? '').trim()
            if (this.searching || (! force && query === this.lastQuery)) return
            if (query.length < 3) {
                if (force) this.message = this.texts.too_short
                return
            }
            const picker = this.picker()
            if (! picker) return
            this.lastQuery = query
            this.searching = true
            this.message = this.texts.searching
            let found = false
            const onFound = () => { found = true }
            picker.addEventListener('address-selected', onFound, { once: true })
            try {
                picker.searchQuery = query
                picker._searchOpen = true
                await picker.updateComplete
                picker.querySelector('.search-box .ctrl-btn:not(.geo-search-close)')?.click()
                // la ricerca e' asincrona: si aspetta che il picker finisca (al massimo 10 s), poi si guarda se ha scelto un luogo
                for (let waited = 0; waited < 100; waited++) {
                    await new Promise((resolve) => setTimeout(resolve, 100))
                    if (! picker.isSearching) break
                }
                await new Promise((resolve) => setTimeout(resolve, 50))
            } finally {
                picker.removeEventListener('address-selected', onFound)
                picker._searchOpen = false
                this.searching = false
                this.message = found ? '' : this.texts.not_found
            }
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
            x-ref="field"
            x-on:keydown.enter.prevent="search(true)"
            x-on:change="search(false)"
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
