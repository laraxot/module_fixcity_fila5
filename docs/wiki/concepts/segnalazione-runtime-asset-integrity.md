# segnalazione runtime asset integrity

## Problema

Su `it/tests/segnalazione-crea` erano presenti errori concatenati:

- 404 su asset statici (`header-fix.css`, `mobile-map-fix.css`, `themes/Geo/js/geo.js`)
- warning Livewire asset out of date
- errori Alpine (`geoMapPickerField is not defined`) con widget mappa non inizializzato

## Causa

- inclusioni CSS hardcoded in layout verso path non pubblicati
- asset `Geo` non presente nel webroot runtime (`public_html/themes/Geo/js/geo.js`)
- registrazione Alpine del map picker legata solo a `alpine:init`
  (fragile quando Alpine è già inizializzato)

## Fix applicato

1. Rimossi link CSS hardcoded da `Themes/Sixteen/resources/views/components/layouts/main.blade.php`
   e mantenuto solo bundle Vite (`app.css`).
2. Copiato `geo.js` in `public_html/themes/Geo/js/geo.js`.
3. Pubblicati asset aggiornati:
   - `php artisan livewire:publish --assets`
   - `php artisan filament:assets`
   - `php artisan optimize:clear`
4. Resa robusta registrazione Alpine in
   `Modules/Geo/resources/views/filament/forms/components/map-picker.blade.php`
   con init immediata + fallback `alpine:init`.

## Verifica

- `GET /it/tests/segnalazione-crea` -> `200`
- `GET /themes/Geo/js/geo.js` -> `200`
- `GET /vendor/livewire/livewire.js` -> `200`
- `GET /js/filament/support/support.js` -> `200`
