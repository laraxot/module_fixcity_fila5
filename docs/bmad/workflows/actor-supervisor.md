---
title: "BMAD actor — Supervisore"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, actor, supervisor, sla, audit, fixcity]
module: Fixcity
qmd: "supervisor sla reassignment audit escalation fixcity workflow"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-operator.md
  - actor-admin.md
  - ../../wiki/concepts/actor-flow-map.md
---

# Attore: Supervisore

**Scopo:** garantire SLA, correttezza delle assegnazioni e audit sulla coda ente.

## Happy path

1. Vede coda ente e KPI (`GetTicketKpiAggregateAction`, `GetTicketSlaMetricsAction`).
2. Riassegna ticket (`AssignTicketAction`) e corregge priorità se policy lo consente.
3. Supervisiona escalation / ticket fuori SLA.
4. Approva o controlla transizioni sensibili e audit/timeline.
5. Usa export/report senza bypassare policy.

## Failure path

- riassegnazione fuori perimetro;
- metriche SLA non allineate allo stato reale;
- audit mancante dopo change status (gap noto se activity non scritta).

## Prove

Test SLA/KPI dove presenti; policy supervisor; campionamento audit su staging.

## Nota gap

Automazioni, trust score e audit append-only completi restano tracciati in
[actor-flow-map](../../wiki/concepts/actor-flow-map.md) — non inventare Actions.
