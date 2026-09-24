# Segnalazioni Elenco — Architettura Mappa e Lista

## Overview

La pagina `/it/tests/ticket-list` mostra l'elenco delle segnalazioni (ticket) in due viste:
- **Mappa**: componente Lit Web Component `<map-lit>` con Leaflet + MarkerCluster
- **Lista**: card Bootstrap Italia con dati reali dal DB (top 20 + load more futuro)

## Pattern: Static JSON File (farmshops.eu)

Ispirato a https://github.com/CodeforKarlsruhe/farmshops.eu — matrice dettagliata: [farmshops-eu-applicability-fixcity.md](../../../../Geo/docs/wiki/concepts/farmshops-eu-applicability-fixcity.md)

```
[Backoffice Action] → GenerateTicketsJsonAction
    └─ scrive: public_html/data/tickets.json

[Frontend]
    └─ <map-lit data-url="/data/tickets.json">
           └─ fetch() → L.geoJSON() → MarkerCluster
```

Il file è **statico** e **leggero** — anche con migliaia di punti resta < 1MB.

## Componenti

### GenerateTicketsJsonAction
- **Path**: `Modules/Fixcity/app/Actions/GenerateTicketsJsonAction.php` (nwidart: **solo** sotto `app/` — vedi [incident-nwidart-class-outside-app.md](../../../../../docs/wiki/memories/incident-nwidart-class-outside-app.md))
- **Trigger**: HeaderAction "Esporta JSON mappa" nel pannello admin Filament (ListTickets)
- **Output**: `public_html/data/tickets.json` (GeoJSON FeatureCollection)
- **Filtro**: solo ticket con `location` non null e coordinate valide

### Filtri sidebar (server + Alpine)

- **Path**: `Themes/Sixteen/.../ticket/filters-sidebar.blade.php`
- **Dati facet**: `SegnalazioniFilterViewModel` ← `BuildSegnalazioniFilterAggregateAction` ← `tickets.json` (SSoT con mappa)
- **Icona filtro**: solo `iconUrl` da `fixcity::svg` (stesso file del glifo pin) — vedi [ticket-type-icon-fixcity-svg.md](./ticket-type-icon-fixcity-svg.md)
- **Eventi**: `filter-type-changed` → `filters-changed` → `map-lit#block-map.filterByTypes()`
- **Opzionale**: `<map-filter-lit>` (Geo) per pagine che non usano Blade DC — non è il path canonico su `/it`

### map-lit.js (canonical)
- **Path**: `Modules/Geo/resources/js/components/map-lit.js` — LitElement web component (estende `LitElement` con import `lit`)
- **Custom Element**: `<map-lit>` — registrato via `customElements.define('map-lit', MapLit)` con guard `if (!customElements.get('map-lit'))`
- **Attributi**: `data-url` (URL JSON GeoJSON), `class` (es. `w-full`)
- **API pubblica**: `element.filterByType(type)` / `element.filterByType(null)`
- **Regola**: usa `class="map-container"` mai `id="map"` (regola `leaflet-container-class-selector.md`)
- **Registrazione runtime**: il custom element è disponibile SOLO se `Themes/Sixteen/resources/js/app.js` importa `@modules/Geo/resources/js/components/map-lit.js`. Senza l'import, browser tratta `<map-lit>` come `HTMLUnknownElement` e il componente è inerte.

> **Storico nomi (deprecati — NON usare nei Blade nuovi):**
> - `<ticket-map-lit>` (mai esistito come file, riferimento errato della wiki precedente)
> - `<geo-map-lit>` (componente alternativo in `Modules/Geo/resources/js/components/geo-map-lit.js`, da NON usare per `ticket-list`; il canonico è `<map-lit>` per decisione 2026-05-07)

### grid/2col + column-main (Themes/Sixteen) — pagina `/it`
- **Path**: `Themes/Sixteen/resources/views/components/blocks/grid/2col.blade.php`, `ticket/column-main.blade.php`
- **Mappa**: `<map-lit id="block-map" data-url="/data/tickets.json">` — GPS al load se senza `lat`/`lng`
- **Filtri sidebar**: facet da JSON (STORY-053/127), non DB
- **Lista tab**: query DB live tickets (out of scope unificazione JSON — STORY-051 T7)
- **Leaflet**: caricato via npm/Vite dal modulo Geo, non via CDN

Vedi story: [docs/stories/ticket-list-marker-type-icon-parity.md](../../../../../docs/stories/ticket-list-marker-type-icon-parity.md) — perché mappa e filtri devono condividere `tickets.json`.

## JSON Format

```json
{
  "properties": {
    "id": 1,
    "title": "...",
    "type": {
      "value": "waste_collection",
      "label": "Raccolta Rifiuti",
      "iconUrl": "/assets/fixcity/svg/waste-collection.svg"
    },
    "status": {
      "value": "in_progress",
      "label": "In lavorazione",
      "color": "#0ea5e9"
    },
    "address": "Via ..."
  }
}
```

**Semantica:** `type` = `TicketTypeEnum` (una sola `iconUrl` da `fixcity::svg`). `status.color` = `TicketStatusEnum` (colore pin). Vedi [ticket-type-icon-fixcity-svg.md](./ticket-type-icon-fixcity-svg.md) e [map-legend-status-semantics.md](../../../../Geo/docs/wiki/concepts/map-legend-status-semantics.md).

## Filtri per tipo (`TicketTypeEnum`)

Facet da `tickets.json` → checkbox `data-filter-type` + count. Sidebar: **solo icona** (`iconUrl`), senza colore tipologia.

`map.filterByTypes(selectedTypes)` filtra su `properties.type.value`.

## Note sull'autenticazione header

L'header (SSoT: `v1.blade.php`) mostra già avatar+nome quando autenticato.
Se non appare verificare che `auth()->check()` sia true in quel contesto.

## Ricostruzione da documentazione

Se il codice JS/CSS/Blade va perso, ricostruire in ordine:

1. JSON: `GenerateTicketsJsonAction` → `public_html/data/tickets.json`
2. Geo: [geo-map-lit-reconstruction-guide.md](../../../../Geo/docs/wiki/concepts/geo-map-lit-reconstruction-guide.md)
3. Tema Sixteen: [geo-map-popup-leaflet-boundary.md](../../../../../Themes/Sixteen/docs/wiki/concepts/geo-map-popup-leaflet-boundary.md) · [global-header-css-leak-leaflet-popup.md](../../../../../Themes/Sixteen/docs/wiki/troubleshooting/global-header-css-leak-leaflet-popup.md)
4. Incidenti noti: [map-lit-it-incidents-2026-06.md](../../../../Geo/docs/wiki/troubleshooting/map-lit-it-incidents-2026-06.md)

## Riferimenti

- Story: `.planning/stories/8-75-ticket-list-map-list.story.md`
- Design Comuni reference: https://italia.github.io/design-comuni-pagine-statiche/sito/ticket-list.html
- farmshops.eu pattern: https://github.com/CodeforKarlsruhe/farmshops.eu
- Regola leaflet: `Modules/Geo/docs/MAP-COMPONENTS-ARCHITECTURE.md`
- Root SSOT filtri: [frontoffice-map-filters-ssot-architecture.md](../../../../../docs/wiki/architecture/frontoffice-map-filters-ssot-architecture.md)