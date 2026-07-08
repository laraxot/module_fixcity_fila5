---
title: "CommentsRelationManager duplicato in Fixcity"
type: redundancy
owner: Modules/Fixcity
severity: medium
created: 2026-05-22
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/90"
related:
  - ../../../Comment/docs/redundancy-report.md
  - ./fixcity-cross-module-duplicate-surfaces.md
---

# CommentsRelationManager — due path nello stesso modulo

## File

1. `app/Filament/Resources/TicketResource/RelationManagers/CommentsRelationManager.php`
2. `app/Filament/Resources/RelationManagers/CommentsRelationManager.php`

## Impatto

Filament può scoprire la classe sbagliata; fix commenti ticket in un path, regressione nell’altro.

## Fix

- Una sola implementazione sotto `TicketResource/RelationManagers/`.
- Rimuovere la copia in `Resources/RelationManagers/` dopo verifica `TicketResource::getRelations()`.

## Tracker

[#90](https://github.com/laraxot/base_fixcity_fila5/issues/90).
