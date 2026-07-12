---
title: "no app/Support — Fixcity QueueableAction"
type: concept
tags: [fixcity, actions, queueable-action, support, export]
created: 2026-07-12
updated: 2026-07-12
qmd: "Fixcity module no Support SpreadsheetCellSanitizer CSV export sanitize"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/372"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/273"
related:
  - ../../../../docs/wiki/concepts/no-app-support-monorepo-migration.md
  - ticket-export-security.md
---

# Fixcity — `app/Support/` eliminato

| Legacy | Action |
|--------|--------|
| `SpreadsheetCellSanitizer::sanitize` | `Actions/Export/SanitizeSpreadsheetCellAction` |

## Perché

Mitigazione **CSV formula injection** (FR-024) su export Filament ticket: la sanitizzazione è un use case testabile e invocabile con `app(...)->execute($state)`.

Consumer: `TicketExporter` (formatStateUsing).

## Collegamenti

- [no-app-support-monorepo-migration](../../../../docs/wiki/concepts/no-app-support-monorepo-migration.md)
