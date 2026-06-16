---
title: API pubbliche Fixcity — Folio, non Controller
type: concept
confidence: high
created: 2026-05-29
tags: [folio, api, fixcity, geojson]
related:
  - ../../../../docs/wiki/concepts/folio-api-no-controllers.md
  - map-lit-ticket-api.md
---

# API pubbliche Fixcity — Folio, non Controller

Vietato `app/Http/Controllers/**`. Entry HTTP: `resources/views/pages/api/` + `render()` + Actions.

| URL | Folio | Action |
|-----|-------|--------|
| `/api/tickets/geojson` | `api/tickets/geojson.blade.php` | `BuildTicketsGeoJsonAction` |
| `/api/ticket-details/{ticket}` | `api/ticket-details/[ticket].blade.php` | `BuildTicketPublicDetailsPayloadAction` |

Registrazione: `FixcityServiceProvider::registerFolioApiRoutes()` — `Folio::path(.../pages/api)->uri('/api')`.

Consumer: `map-lit`, blocco `segnalazioni/layout` (`data-url`).

## Dettaglio popup e SSoT `tickets.json`

`map-lit` visualizza i marker da `/data/tickets.json`. L'endpoint `/api/ticket-details/{ticket}` deve quindi restare coerente con quella sorgente pubblica:

1. cerca il ticket nella query pubblica live (`BuildPublicTicketsQueryAction`);
2. se non lo trova, cerca lo stesso id nella FeatureCollection pubblica (`LoadPublicTicketsGeoJsonAction`);
3. se l'id non e' presente nemmeno nel JSON pubblico, restituisce `404`.

Questa scelta evita errori console su marker gia' mostrati dal frontoffice quando `tickets.json` statico e DB live non sono temporaneamente allineati.
