---
title: "BMAD 05 — Implementation FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, implementation, actions, fixcity]
module: Fixcity
qmd: "bmad implementation actions create ticket assign change status fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - 03-architecture.md
  - 06-quality-assurance.md
  - actor-citizen.md
  - actor-operator.md
---

# Implementation

**Perché:** il minimo cambiamento corretto chiude un AC; duplicare schema/policy
rompe la slice civica.

## Passi

1. Acquisire lock sui file owner.
2. Preferire Actions esistenti: `CreateTicketAction`, `AssignTicketAction`,
   `ChangeStatus`, `RecordTicketActivityAction`, `BuildTicketTimelineAction`,
   `SubmitCitizenTicketRatingAction`, query/geo builder già presenti.
3. FO: Folio/widget; BO: Filament action/resource `XotBase*`.
4. Nessun controller HTTP, nessun Service layer nuovo.
5. Dopo ogni pezzo: lint mirato + test del comportamento toccato.
6. Aggiornare story e docs owner (non il tema, salvo UI).

## Gate

PHPStan sul perimetro, test del comportamento, diff senza marker di conflitto.

## Output

Codice + test + story aggiornata.
