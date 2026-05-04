# Segnalazioni Elenco — Architettura Mappa e Lista

## Overview

La pagina `/it/tests/segnalazioni-elenco` mostra l'elenco delle segnalazioni (ticket) in due viste:
- **Mappa**: componente Lit Web Component `<ticket-map-lit>` con Leaflet + MarkerCluster
- **Lista**: card Bootstrap Italia con dati reali dal DB (top 20 + load more futuro)

## Pattern: Static JSON File (farmshops.eu)

Ispirato a https://github.com/CodeforKarlsruhe/farmshops.eu

```
[Backoffice Action] → GenerateTicketsJsonAction
    └─ scrive: public_html/data/tickets.json

[Frontend]
    └─ <ticket-map-lit data-url="/data/tickets.json">
           └─ fetch() → L.geoJSON() → MarkerCluster
```

Il file è **statico** e **leggero** — anche con migliaia di punti resta < 1MB.

## Componenti

### GenerateTicketsJsonAction
- **Path**: `Modules/Fixcity/app/Actions/GenerateTicketsJsonAction.php`
- **Trigger**: HeaderAction "Esporta JSON mappa" nel pannello admin Filament (ListTickets)
- **Output**: `public_html/data/tickets.json` (GeoJSON FeatureCollection)
- **Filtro**: solo ticket con `location` non null e coordinate valide

### ticket-map-lit.js
- **Path**: `Modules/Geo/resources/js/components/ticket-map-lit.js` — Lit Web Component (no LitElement dependency, plain HTMLElement) — vive in Geo perché riutilizzabile
- **Custom Element**: `<ticket-map-lit>`
- **Attributi**: `data-url` (URL del JSON), `style="height:450px"`
- **API pubblica**: `element.filterByType(type)` / `element.filterByType(null)`
- **Regola**: usa `class="map-container"` mai `id="map"` (regola leaflet-class-selector)

### layout.blade.php (Themes/Sixteen)
- **Path**: `Themes/Sixteen/resources/views/components/blocks/segnalazioni/layout.blade.php`
- **Filtri sidebar**: generati dinamicamente da `TicketTypeEnum::cases()` + conteggi reali
- **Lista**: query `Ticket::latest()->take(20)->get()` (no mock)
- **Leaflet**: caricato via CDN (unpkg) — no Vite dependency

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
      "id": 1, "title": "...", "type": "waste_collection",
      "type_label": "Raccolta Rifiuti", "type_color": "#4caf50",
      "address": "Via ...", "status": "pending", "url": "/it/tests/..."
    }
  }]
}
```

## Filtri per tipo

I checkbox nella sidebar hanno `value="{{ $case->value }}"` (TicketTypeEnum value).
Il click chiama `ticketMap.filterByType(value)` che re-renderizza i marker senza reload.

## Note sull'autenticazione header

L'header (SSoT: `v1.blade.php`) mostra già avatar+nome quando autenticato.
Se non appare verificare che `auth()->check()` sia true in quel contesto.

## Riferimenti

- Story: `.planning/stories/8-75-segnalazioni-elenco-map-list.story.md`
- Design Comuni reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html
- farmshops.eu pattern: https://github.com/CodeforKarlsruhe/farmshops.eu
- Regola leaflet: `Modules/Geo/docs/MAP-COMPONENTS-ARCHITECTURE.md`
