---
title: STORY-508 — Conferma, tracking e FO reale
type: story
status: in_progress
module: Fixcity
created: 2026-09-26
updated: 2026-09-26
tags:
- bmad
- confirmation
- tracking
- frontoffice
- ux
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
related:
- ../gap-analysis.md
- ../../wiki/concepts/user-journey-map.md
- ../../wiki/concepts/fixcity-architecture-contract-2026-09-26.md
qmd: STORY 508 confirmation tracking fo FixCity BMAD story
---

# STORY-508 — Conferma, tracking e FO reale

## Perché

Il cittadino inviava la segnalazione e arrivava a una conferma vuota; non poteva
tracciare per codice; la mappa pubblica e le pratiche erano demo/placeholder.

## Fatto in codice

- [x] `AllocateTicketCodeAction` + `CreateTicketAction` alloca code e fa flash CMS bag
- [x] `BuildTicketConfirmationDataAction` + Folio confirmation legge flash/`?code=`
- [x] CMS `<x-page>`: data bag pagina **override** i default del blocco
- [x] `GetPublicTicketByCodeAction`: code = capability token (anche PENDING)
- [x] Ricerca tracking limitata a 10 richieste/minuto per rate key Laravel
- [x] I nuovi codici usano 16 caratteri casuali (circa 83 bit) dopo il prefisso
- [x] Folio `/tickets/track` + `/tickets/track/{code}`
- [x] Tracking mostra gli eventi di stato con visibilità pubblica; gli eventi interni sono esclusi
- [x] Payload dettaglio: `status` e `slug` pubblici; il capability `code` viene restituito solo all’owner autenticato (STORY-510)
- [x] Area pratiche: lista `owner_id` reale
- [x] `/segnalazioni`: dati da `BuildPublicTicketsQueryAction`
- [x] `AssignTicketAction` scrive Activity (`assignment`)
- [x] Test Pest scritti (`CreateTicketConfirmationFlowTest`, CMS merge override)
- [x] Pest runtime verificato sul database SQLite condiviso in transazione; il grant MariaDB resta un controllo separato di staging
- [ ] Browser smoke del tracking con codice valido per guest e owner: Playwright/Chromium sono ora disponibili; resta da eseguire la matrice con ticket sintetico e account isolati. Il form guest vuoto è stato verificato in IT/EN/DE/ES a 320/1440 px (8 casi, HTTP 200, zero overflow/errori JS).
- [x] Form tracking usa la validazione HTML nativa e associa l'errore al campo con `aria-describedby`/`aria-invalid`
- [x] Form tracking responsive verificato in Chromium: 4 lingue × 6 breakpoint, input e CTA full-width sotto 576 px, layout inline sopra, label/help e focus da tastiera.
- Design atteso, confronto e correzione Sixteen: [tracking search form](../../../../../Themes/Sixteen/docs/bmad/tracking-search-form-layout-2026-09-27.md).
- [x] Rate limit (10/min), timeline pubblica e redazione dati personali verificati con Pest; i codici storici restano validi
- [ ] Widget redirect con `?code=` esplicito: lock wizard di altro agente

## AC

| # | Criterio | Stato |
|---|---|---|
| 1 | Dopo create, confirmation mostra codice | verificato con Pest SQLite |
| 2 | Guest con codice vede stato e sola timeline pubblica su `/tickets/track` | verificato con Pest SQLite |
| 3 | API dettaglio espone status/slug; il capability code solo al proprietario | verificato con Pest SQLite |
| 4 | Pratiche elenca ticket owner | query e isolamento verificati con Pest SQLite |
| 5 | Segnalazioni FO non usa ticket finti | lista live, filtri e paginazione verificati con Pest SQLite |
| 6 | PHPStan Actions level 10 | ✅ |
| 7 | UI focusable form track + pratiche CTA | markup presente |

## UI/UX check (statico)

- Track: label, help, required input, validazione HTML nativa (senza `novalidate`), errore not-found associato al campo con `aria-describedby`/`aria-invalid`, `aria-live` sul risultato
- Timeline: status-change con visibilità pubblica; activity interne escluse dalla pagina
- Pratiche: heading, empty state, CTA nuova segnalazione, link traccia
- Confirmation: riusa blocco Design Comuni `04-conferma` via CMS bag
