---
title: "BMAD 03 — Architecture FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, architecture, boundaries, fixcity]
module: Fixcity
qmd: "bmad architecture actions folio usercontract policy xotbase fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - 05-implementation.md
  - ../../wiki/concepts/fixcity-architecture-contract-2026-09-26.md
  - ../../wiki/concepts/ticket-workflow-state-machine.md
---

# Architecture

**Perché:** i boundary Laraxot proteggono ownership del modulo e coerenza del
dominio Ticket; una violazione qui costa più di un bug UI.

## Invarianti da verificare

1. Namespace `Modules\Fixcity\...` da `composer.json` del modulo.
2. Business logic in Actions; niente Services layer; niente HTTP Controllers.
3. FO: Folio (+ Volt/widget); BO: Filament `XotBase*`.
4. Authz: `UserContract`; policy `TicketPolicy → BasePolicy → UserBasePolicy`.
5. Assegnazione PA: colonna `responsible_id` (relazioni `assignee`/`responsible`).
6. Transizioni: `TicketStatusEnum::allowedTransitions()` come SSoT.
7. Migration forward-only; FK nuove con `foreignIdFor` (+ `XotData` per User).
8. Tema Sixteen: solo presentazione; schema/Actions restano nel modulo.

## Passi

1. Cercare implementazione esistente prima di nuove classi/docs.
2. Aggiornare il contratto architetturale solo se cambia una regola stabile.
3. Registrare ADR/breve nota se la decisione è nuova.

## Gate

Nessuna violazione di boundary; decisione scritta nel owner docs.

## Output

Contratto/ADR aggiornato + impatto su story.
