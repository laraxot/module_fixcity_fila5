---
title: "STORY-022 — Supervisione ed escalation SLA"
type: story
module: Fixcity
status: not_started
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, fixcity, supervisor, sla, escalation]
qmd: "supervisor escalation SLA overdue tickets notifications audit"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../workflows/actor-supervisor.md
  - ../../wiki/concepts/actor-flow-map.md
---

# Obiettivo

Consentire al supervisore di identificare ticket fuori SLA, motivare l'escalation e controllarne la presa in carico.

## Criteri di accettazione

1. La coda distingue ticket prossimi alla scadenza, scaduti e in escalation.
2. La soglia SLA è configurabile dall'ente e non viene inventata nel codice.
3. L'escalation registra attore, motivazione, timestamp e destinatario.
4. La notifica è idempotente e non rivela note interne al cittadino.
5. Il supervisore può riassegnare secondo policy; l'operatore standard non può modificare la configurazione SLA.
6. Pest e browser provano permessi, soglie, retry e stato vuoto.

## Decisioni necessarie prima dell'implementazione

Definire per ogni tipologia/priorità le soglie, calendario lavorativo, sospensioni, destinatari e comportamento in caso di mancato responsabile. Nessun valore SLA è dedotto da questa story.

## Riscontro attuale

La UI KPI/SLA e le Action di metriche sono parziali; non risulta un workflow completo di escalation automatica. Story in backlog, nessuna implementazione dichiarata.
