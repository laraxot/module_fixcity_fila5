---
title: "BMAD 01 — Discovery FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, discovery, actors, fixcity]
module: Fixcity
qmd: "bmad discovery citizen operator supervisor admin gap fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-citizen.md
  - ../actor-flows.md
  - ../gap-analysis.md
  - ../../wiki/concepts/actor-flow-map.md
---

# Discovery

**Perché:** FixCity serve quattro attori umani più il sistema; ogni gap non
verificato nel codice diventa falso “fatto” in documentazione.

## Passi

1. Elencare attori: `citizen`, `operator`, `supervisor`, `admin`, `system`.
2. Per ciascuno: trigger, permesso (`TicketPolicy` / `BasePolicy::PA_ROLES`),
   dati visibili, happy path, failure path.
3. Confrontare docs con codice reale (Actions, Folio, Filament, Enum).
4. Aggiornare [gap-analysis](../gap-analysis.md) e [actor-flow-map](../../wiki/concepts/actor-flow-map.md).
5. Eliminare story duplicate prima di crearne di nuove.

## Gate

Ogni attore ha ≥1 happy path e ≥1 failure path verificati nel codice o marcati gap.

## Output

Gap analysis + backlog senza duplicati + link ai workflow `actor-*`.
