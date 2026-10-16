---
title: "Filament Blueprint — Fixcity"
type: filament-blueprint
status: active
module: Fixcity
created: 2026-10-16
updated: 2026-10-16
tags: [bmad, filament, blueprint, fixcity]
qmd: "filament blueprint fixcity xotbase resource widget"
related:
  - ../bmad-planning.md
  - ../bmad-brainstorming.md
---

# Scopo

Contratto Filament del modulo **Fixcity**: chrome BO e/o widget FO senza violare
i confini di dominio.

## Regole non negoziabili

1. Estendere solo `XotBaseResource` / `XotBaseWidget` / basi Xot — mai classi Filament dirette.
2. Nessun `->label()` hardcoded: chiavi in `lang/<locale>/` del modulo.
3. Business logic nelle Actions del modulo, non nei form hooks grassi.
4. FO: widget montati esplicitamente; non abusare `discoverWidgets` per chrome.

## Resource / widget

- `TicketResource`

## Form / Table / Infolist

Preferire triad schema dedicati (`*Form`, `*Table`, `*Infolist`) quando la Resource
cresce oltre pochi campi. Enum tipizzati al posto di option array.

## Policy e tenant

Ogni query Filament rispetta tenant + policy del modello owner. Nessun elenco
cross-tenant per comodita admin.

## Related

- Planning: [bmad-planning.md](../bmad-planning.md)
- Brainstorming: [bmad-brainstorming.md](../bmad-brainstorming.md)


## Lifecycle segnalazione (SSoT dettagliato)

- [ticket-lifecycle-filament-blueprint.md](./ticket-lifecycle-filament-blueprint.md)
- [ticket-lifecycle-blueprint.md](./ticket-lifecycle-blueprint.md)
- Ledger: `TicketActivity` — **non** `timeline_steps`.
