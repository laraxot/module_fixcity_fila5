# Segnalazioni Elenco — Architettura Mappa e Lista

## Overview

La pagina `/it/tests/segnalazioni-elenco` mostra l'elenco delle segnalazioni (ticket) in due viste:
- **Mappa**: componente Lit Web Component `<map-lit>` con Leaflet + MarkerCluster
- **Lista**: card Bootstrap Italia con dati reali dal DB (top 20 + load more futuro)

## Pattern: Static JSON File (farmshops.eu)

Ispirato a https://github.com/CodeforKarlsruhe/farmshops.eu

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

### map-lit.js (canonical)
- **Path**: `Modules/Geo/resources/js/components/map-lit.js` — LitElement web component (estende `LitElement` con import `lit`)
- **Custom Element**: `<map-lit>` — registrato via `customElements.define('map-lit', MapLit)` con guard `if (!customElements.get('map-lit'))`
- **Attributi**: `data-url` (URL JSON GeoJSON), `class` (es. `w-full`)
- **API pubblica**: `element.filterByType(type)` / `element.filterByType(null)`
- **Regola**: usa `class="map-container"` mai `id="map"` (regola `leaflet-container-class-selector.md`)
- **Registrazione runtime**: il custom element è disponibile SOLO se `Themes/Sixteen/resources/js/app.js` importa `@modules/Geo/resources/js/components/map-lit.js`. Senza l'import, browser tratta `<map-lit>` come `HTMLUnknownElement` e il componente è inerte.

> **Storico nomi (deprecati — NON usare nei Blade nuovi):**
> - `<ticket-map-lit>` (mai esistito come file, riferimento errato della wiki precedente)
> - `<geo-map-lit>` (componente alternativo in `Modules/Geo/resources/js/components/geo-map-lit.js`, da NON usare per `segnalazioni-elenco`; il canonico è `<map-lit>` per decisione 2026-05-07)

### layout.blade.php (Themes/Sixteen)
- **Path**: `Themes/Sixteen/resources/views/components/blocks/segnalazioni/layout.blade.php`
- **Mappa**: `<map-lit data-url="/data/tickets.json">` — legge `public_html/data/tickets.json`
- **Filtri sidebar (stato attuale)**: conteggi da query DB `Ticket::query()` — **da allineare** a facet sullo stesso JSON (STORY-051)
- **Lista**: query DB `$filteredTicketsQuery` — target: subset coerente con JSON filtrato
- **Leaflet**: caricato via npm/Vite dal modulo Geo, non via CDN

Vedi story: [docs/stories/segnalazioni-elenco-marker-type-icon-parity.md](../../../../../docs/stories/segnalazioni-elenco-marker-type-icon-parity.md) — perché mappa e filtri devono condividere `tickets.json`.

## JSON Format

```json
{
  "type": "FeatureCollection",
  "generated_at": "2026-04-29T17:00:00Z",
  "total": 42,
  "features": [{
    "type": "Feature",
    "geometry": { "type": "Point", "coordinates": [12.251, 45.562] },
    "properties": {
      "id": 1, "title": "...",
      "type": {
        "value": "waste_collection",
        "label": "Raccolta Rifiuti",
        "color": "#4caf50",
        "icon": "heroicon-o-trash",
        "iconUrl": "/assets/ui/svg/trash.svg"
      },
      "address": "Via ...", "status": "pending", "url": "/it/tests/..."
    }
  }]
}
```

## Filtri per tipo

**Target (STORY-051):** facet costruiti aggregando `features[]` di `tickets.json` (`type.value`, conteggio N per tipologia). Label/color/icon dal oggetto `properties.type`.

**Oggi:** checkbox con `value` enum + conteggio da DB; submit GET `?types[]=`; script chiama `map.filterByTypes(selectedTypes)` sul JSON già caricato.

I checkbox usano `TicketTypeEnum::value`; devono coincidere con `properties.type.value` nel GeoJSON.

## Note sull'autenticazione header

L'header (SSoT: `v1.blade.php`) mostra già avatar+nome quando autenticato.
Se non appare verificare che `auth()->check()` sia true in quel contesto.

## Riferimenti

- Story: `.planning/stories/8-75-segnalazioni-elenco-map-list.story.md`
- Design Comuni reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html
- farmshops.eu pattern: https://github.com/CodeforKarlsruhe/farmshops.eu
- Regola leaflet: `Modules/Geo/docs/MAP-COMPONENTS-ARCHITECTURE.md`