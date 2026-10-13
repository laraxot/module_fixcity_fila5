{{--
    Campo `location.address` del passo 2: markup di segnalazione-02-dati (`.cmp-input-autocomplete > .form-group` con
    `label.label-input.d-none`, `input[type=search].autocomplete` e `.link-wrapper > a.list-item` "Usa la tua posizione").
    Resta un campo Filament: stato Livewire sul suo state path (`data.location.address`, una chiave dello stato del CoordinatePicker),
    `required`, aria-* ed errori di validazione. Da usare con `->hiddenLabel()->view('fixcity::filament.forms.components.dc-place-autocomplete')`.

    "Usa la tua posizione" (solo Alpine, solo contesto sicuro): `navigator.geolocation` e poi l'evento `coords-changed` sul
    `coordinate-picker-lit` piu' vicino (stesso contratto della mappa: `detail.latitude|longitude`, bubbles, composed). Il wrapper del
    CoordinatePicker aggiorna lo stato `data.location` e fa `reverseGeocode` lato server, che riempie `address` (questo input). Senza HTTPS
    il link dichiara il motivo (messaggio sotto il link) invece di non fare nulla. Nessuna dipendenza da Bootstrap JS.
    Suggerimenti mentre si digita: non implementati (`.autocomplete-list` ha `display:none` nello scope e `autocomplete-list-show` non ha regole).
--}}
@php
    // ponytail: __() su una chiave mancante crea la riga DB e fa scrivere il file lingua dall'auto-writer. Finche' la chiave nuova non
    // esiste in lang/*/ticket_form.php si legge dal loader con testo di ripiego; poi sostituire con __().
    $line = static function (string $key, string $fallback): string {
        [$namespace, $path] = explode('::', $key, 2);
        [$group, $item] = explode('.', $path, 2);
        $value = \Illuminate\Support\Arr::get(app('translator')->getLoader()->load(app()->getLocale(), $group, $namespace), $item);

        return \is_string($value) ? $value : $fallback;
    };

    $id = $getId();
    $statePath = $getStatePath();
    $hasError = $errors->has($statePath);
    $isFieldDisabled = $isDisabled();
    $isFieldRequired = $isRequired() && (! $isFieldDisabled);
    $placeholder = ((string) ($getPlaceholder() ?? __('fixcity::ticket_form.fields.location.placeholder'))) . ($isFieldRequired ? '*' : '');
    $texts = [
        'locating' => __('geo::address.geolocation.locating'),
        'denied' => __('geo::address.geolocation.permission_denied'),
        'timeout' => __('geo::address.geolocation.timeout'),
        'unsupported' => __('geo::address.geolocation.not_supported'),
        'unavailable' => __('geo::address.geolocation.unavailable'),
        'insecure' => $line('fixcity::ticket_form.fields.location.geolocation_insecure', 'La posizione automatica richiede una connessione sicura (HTTPS). Seleziona il punto sulla mappa.'),
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
        locate() {
            if (this.locating) return
            if (! window.isSecureContext) return void (this.message = this.texts.insecure)
            if (! navigator.geolocation) return void (this.message = this.texts.unsupported)
            const picker = this.picker()
            if (! picker) return void (this.message = this.texts.unavailable)
            this.locating = true
            this.message = this.texts.locating
            navigator.geolocation.getCurrentPosition(
                ({ coords: { latitude, longitude } }) => {
                    this.locating = false
                    this.message = ''
                    picker.dispatchEvent(new CustomEvent('coords-changed', {
                        bubbles: true,
                        composed: true,
                        detail: { latitude, longitude, lat: latitude, lng: longitude, address: null, display_name: null, source: 'geolocation' },
                    }))
                },
                (error) => {
                    this.locating = false
                    this.message = this.texts[{ 1: 'denied', 3: 'timeout' }[error.code] ?? 'unavailable']
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 },
            )
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
