---
title: AdminDashboardFlow
id: STORY-004
author: BMAD
status: done
priority: high
type: story
module: Fixcity
tags:
- bmad
- fixcity
created: legacy
updated: 2026-09-26
qmd: story 004 admin dashboard FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Descrizione
L'amministratore gestisce il dashboard delle segnalazioni. Il flusso include:
- Visualizzazione delle statistiche KPI
- Filtro per stato, tipo, data
- Timeline delle attività
- Azioni rapide (assegna, chiudi, priorizza)

## Attori
- **Admin** - gestisce il dashboard e i dati

## Inputs
- Filtri: stato, tipo, data range, responsabile
- Azioni: assegna, chiudi, priorizza

## Outputs
- Dashboard con KPI aggiornati
- Lista filtrato dei ticket
- Timeline delle attività

## Flusso
1. Admin apre il dashboard
2. Caricamento KPI aggregate (GetTicketKpiAggregateAction)
3. Visualizzazione filtri attivi
4. Selezione ticket e azione rapida
5. Esecuzione azione tramite Action
6. Aggiornamento della dashboard

## Decisione BMAD
- Usare `GetTicketKpiAggregateAction` per le statistiche
- Usare `BuildTicketFilterAggregateAction` per i filtri
- Usare `BuildTicketTimelineAction` per la timeline
- Non usare HTTP controllers per il dashboard (Folio pages + Actions)
- Usare XotBaseTableWidget per la tabella dei ticket

## Second Brain
- Documentazione in `docs/chat/` con riferimento alle storie BMAD
- Aggiornamento di `docs/wiki/log.md` con link alle storie
- Decisione registrata in `docs/chat/INDEX.md`
