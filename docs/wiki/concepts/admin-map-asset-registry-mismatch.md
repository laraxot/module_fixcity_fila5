# Admin map asset registry mismatch

## Problema

Nella route admin `fixcity/admin/tickets/create`, la mappa puo' non essere visibile quando la registrazione asset Filament punta a percorsi non allineati ai file deployati.

## Evidenza tecnica

### Registry attuale (Geo AdminPanelProvider)

- `asset('modules/geo/geo-map-widget.js')`
- `asset('modules/geo/map-picker.css')`
- `asset('modules/geo/map-picker.js')`

### File effettivi trovati nel deploy (`public_html`)

- presenti in `public_html/modules/geo/`: `map-picker.css`, `map-picker.js`
- **assente** in `public_html/modules/geo/`: `geo-map-widget.js`
- presente invece in `public_html/themes/Geo/js/`: `geo.js` (e altri bundle tema)

### Verifica visuale reale su route admin

URL verificata: `http://127.0.0.1:8000/fixcity/admin/tickets/create?step=form.data::data::wizard-step`

Esito:

- route raggiungibile in pannello admin autenticato
- sezione mappa presente nel form step `data`
- rendering mappa non ancora affidabile/consistente (visibile area non conforme rispetto al contratto "tile completi + controlli coerenti")

### Evidenza rete runtime (browser)

- `GET /modules/geo/geo-map-widget.js` -> **404**
- `HEAD /modules/geo/geo.js` -> **404**
- `GET /modules/geo/map-picker.js` -> 200
- `GET /themes/Geo/js/map-picker-component.js` -> 200 (fallback)
- `GET https://unpkg.com/leaflet@1.9.4/dist/leaflet.css` -> 200 (CDN fallback)

## Impatto

- una parte della catena runtime mappa viene risolta
- una parte resta mancante o su path diverso
- il risultato puo' essere render incompleto/non visibile nel panel admin

## Root-cause specifica emersa

Nel loader `public_html/modules/geo/map-picker.js` il ramo `fetch('/modules/geo/geo.js')` con `resp.ok === false` caricava il fallback `map-picker-component.js` senza registrare alias custom element:

- fallback espone `map-picker-element`
- i template usano `map-picker-lit`

Senza alias, il tag resta non inizializzato e la mappa non appare.

Fix applicato: alias `map-picker-lit -> map-picker-element` aggiunto anche nel ramo `resp.ok === false`.

## Hardening browser automation (problemi risolti)

Quando l'automazione browser dava esiti intermittenti, la causa non era il login ma la catena asset incompleta/instabile (404 + fallback CDN).

Soluzione implementata:

1. eliminato asset admin inesistente dalla registry panel (`geo-map-widget.js`)
2. aggiornato loader `modules/geo/map-picker.js` per preferire bundle locale `themes/Geo/js/geo.js`
3. mantenuto fallback ma con alias custom element coerente
4. nel fallback `map-picker-component.js` sostituita dipendenza primaria da CDN con asset locali (`/themes/Geo/js/leaflet.js`, `/themes/Geo/css/leaflet.css`) e CDN solo come backup

## Verifica post-fix (visuale + network)

- route admin raggiungibile e interattiva
- console senza errori bloccanti mappa
- network senza 404 su `geo-map-widget.js` / `geo.js`
- richiesta `HEAD + GET /themes/Geo/js/geo.js` in 200
- nessuna dipendenza primaria da `unpkg` per CSS Leaflet

## Regola

Per route admin Filament, la catena asset deve essere coerente end-to-end:

1. path registrato dal provider
2. file realmente presenti nel public path usato dal server
3. bundle JS/CSS necessari al picker effettivamente caricati

## Riferimenti

- [admin ticket create map visual contract](./admin-ticket-create-map-visual-contract.md)
- [filament admin panel map visibility contract](../../../Geo/docs/wiki/concepts/filament-admin-panel-map-visibility-contract.md)
