---
title: "BMAD Planning — Fixcity"
type: bmad-planning
status: active
module: Fixcity
track: bmad-method
created: 2026-10-16
updated: 2026-10-16
tags: [bmad, planning, second-brain, filament-blueprint, fixcity]
qmd: "bmad planning fixcity scopo onde filament"
related:
  - ./bmad/ticket-lifecycle-planning.md
  - ./bmad/ticket-lifecycle-brainstorming.md
  - ./bmad/ticket-lifecycle-filament-blueprint.md
  - ./bmad/ticket-lifecycle-blueprint.md
  - ./bmad-brainstorming.md
  - ./bmad/filament-blueprint.md
---

# Obiettivo

Trasformare una segnalazione cittadina in un procedimento PA trasparente, geolocalizzato e auditabile.

## Perché questo owner

- **Utenti:** cittadino, moderatore, operatore, supervisor
- **Track BMAD:** `bmad-method`
- **Religione:** Actions + Folio/Volt/Filament `XotBase*`; mai Controllers; docs solo in questo `docs/` (mai `bmad-output/`).

## Scope

### In
- Ticket Actions/policy
- TicketActivity ledger
- wizard/tracking Folio
- TicketResource
- comunicazioni via Notify

### Out
- seconda tabella timeline_steps
- Controllers
- ownership presentation Design Comuni (Sixteen)

## Onde di delivery

| Wave | Deliverable | Owner |
|---|---|---|
| 1 | Contratto timeline/visibility su TicketActivity | Fixcity |
| 2 | Moderazione/transizioni/chiusura condizionata | Fixcity |
| 3 | Assegnazione + mail/solleciti | Fixcity + Notify |
| 4 | Media + commenti autorizzati | Fixcity + Media + Comment |
| 5 | UI Design Comuni dettaglio | Sixteen |

## Filament blueprint

Resource/widget di competenza:

- TicketResource

Dettaglio contratto: [filament-blueprint.md](./bmad/filament-blueprint.md).

## Second brain

- Costituzione sintetica: questo file + brainstorming gemello.
- Decisioni durature -> `docs/bmad/decision-log.md` (o dossier topic se gia esiste).
- QMD: indicizzare titoli/qmd; ownership resta nel modulo/tema.

## Definition of Done (planning)

- [ ] Scope in/out condiviso e non contraddetto dal codice owner
- [ ] Onde senza dipendenze circolari cross-owner
- [ ] Blueprint Filament aggiornato (o esplicitamente N/A)
- [ ] Link bidirezionali ai dossier/topic gia esistenti
- [ ] Nessun artefatto in `bmad-output/`
