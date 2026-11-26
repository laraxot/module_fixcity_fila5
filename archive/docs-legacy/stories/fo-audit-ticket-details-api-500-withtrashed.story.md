---
title: "FO-AUDIT - api/ticket-details: 500 su Ticket::withTrashed inesistente"
type: story
status: done
updated: 2026-11-13
tags: [fixcity, api, ticket-details, softdeletes, 500, front-office, audit, bmad]
qmd: "api ticket-details withTrashed SoftDeletes 500 404 Ticket query whereKey Folio SEC-12"
related:
  - ./ticket-details-no-demo-fallback-and-en.story.md
  - ./public-api-media-and-geojson.story.md
  - ../../Seo/docs/stories/fo-audit-seo-path-aliases-fixcity.story.md
---

# FO-AUDIT - api/ticket-details: 500 su Ticket::withTrashed inesistente

Issue: https://github.com/laraxot/base_fixcity_fila5/issues (audit front-office produzione)
Discussion: https://github.com/laraxot/base_fixcity_fila5/discussions

## Problema misurato

`GET /api/ticket-details/{id}` rispondeva **500** per ogni ticket non pubblico
(es. `pending`) e per ogni id inesistente. Log: `Call to undefined method
Modules\Fixcity\Models\Ticket::withTrashed()`.

Causa: il commit SEC-04 (`9508da5a1c`) ha sostituito
`Ticket::query()->whereKey($ticket)` con `Ticket::withTrashed()->whereKey($ticket)`
per coprire anche le segnalazioni cancellate nel controllo «ticket reale non
dovrebbe ricadere sul GeoJSON demo». Ma `Ticket` (via `Fixcity\Models\BaseModel` →
`XotBaseModel`) **non usa il trait SoftDeletes**: `withTrashed()` non esiste e
l'endpoint esplode prima del filtro di visibilità.

## Correzione

`abort_if(Ticket::query()->whereKey($ticket)->exists(), 404)`.

Senza SoftDeletes non esiste global scope su `deleted_at`: la query semplice
trova comunque le righe cancellate, quindi l'intento del commento (niente fallback
demo per ticket cancellati) resta garantito. Commento aggiornato per spiegare
perché non serve `withTrashed`.

## Verifica

- `/api/ticket-details/573` (pubblico) → 200 JSON
- `/api/ticket-details/583` (pending) → 404
- `/api/ticket-details/999999` → 404
- `/api/tickets/list` → 20 ticket, nessun pending/draft
- `/api/tickets/geojson` → 484 feature, nessun pending/draft

## Nota

`/it/api/...` (con prefisso lingua) risponde la pagina HTML del tema invece
dell'API: le pagine `api/` dei moduli sono montate su `/api` senza locale
(`FolioVoltServiceProvider`). Gli URL canonici sono `/api/...` senza `/it/`.
