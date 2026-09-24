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

## Correlazioni aggiornate (2026-05)

- Ordine caricamento tema Sixteen `@vite` (ES module defer) vs Alpine bundled in `@livewireScripts`: tema [livewire-alpine-esm-order.md](../../../../../Themes/Sixteen/docs/wiki/concepts/livewire-alpine-esm-order.md).

## Aggiornamento 2026-05-22: debugbar visibile ma JS runtime rotto

Su `http://127.0.0.1:8000/it/segnalazione-crea` non assumere che l'assenza visiva della Debugbar significhi pacchetto mancante.

### Diagnosi verificata

- `DEBUGBAR_ENABLED=true`, `APP_DEBUG=true`, service provider `Fruitcake\LaravelDebugbar\ServiceProvider` presente e `app()->bound('debugbar') === true`.
- `php artisan route:list --path=_debugbar` mostra le route Debugbar.
- La risposta HTML contiene `phpdebugbar-id`, asset `_debugbar` e `window.phpdebugbar`.
- Gli errori bloccanti in console erano frontend: `geoMapPickerField is not defined`, `headerMobileNav is not defined`, `mobileNavOpen is not defined`.

### Root cause reale

Il bundle pubblico vecchio `public_html/themes/Sixteen/assets/app-BDBkID6g.js` chiamava:

```js
Alpine.data('geoMapPickerField', geoMapPickerField)
```

ma `geoMapPickerField` non era definito come variabile globale. Questo rompeva l'esecuzione del bundle Sixteen e impediva anche l'inizializzazione pulita di Alpine/header, facendo sembrare la Debugbar assente.

### Checklist prima di toccare Composer/debugbar

```bash
curl -s http://127.0.0.1:8000/it/segnalazione-crea -o /tmp/seg.html
rg -n "phpdebugbar-id|_debugbar|window.phpdebugbar|assets/app-.*\.js|geoMapPickerField|headerMobileNav" /tmp/seg.html
cd laravel && php artisan tinker --execute="dump(['public_path' => public_path(), 'debugbar' => config('debugbar.enabled'), 'bound' => app()->bound('debugbar')]);"
```

Se `window.phpdebugbar` e `phpdebugbar-id` sono presenti, la Debugbar e' iniettata: correggere prima asset/cache/runtime JS.

