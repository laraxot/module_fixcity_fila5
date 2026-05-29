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
| `/api/ticket-details/{ticket}` | `api/ticket-details/[Ticket].blade.php` | `BuildTicketPublicDetailsPayloadAction` |

Registrazione: `FixcityServiceProvider::registerFolioApiRoutes()` — `Folio::path(.../pages/api)->uri('/api')`.

Consumer: `map-lit`, blocco `segnalazioni/layout` (`data-url`).
