---
title: "ponytail audit over engineering"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "ponytail audit over engineering"
issues: []
discussions: []
---

# Ponytail audit — Fixcity (over-engineering)

**Ultimo run storico:** 2026-07-09  
**Recheck filesystem:** 2026-09-26 — `app/Services/` assente (0 file)  
**Modulo:** dominio ticket/segnalazioni, workflow PA.

**Hub:** [../../../../docs/project/ponytail-audit-hub.md](../../../../docs/project/ponytail-audit-hub.md)  
**Remediation:** [../../../../docs/project/ponytail-audit-remediation.md](../../../../docs/project/ponytail-audit-remediation.md)  
**GitHub:** issue STORY-487 (base_fixcity_fila5)

## Vincoli Laraxot (ponytail × BMAD)

| Regola | Stato |
|--------|-------|
| No `app/Services/*` per business logic | ✅ nessuna directory `app/Services/` al recheck 2026-09-26 |
| No `Http/Controllers` | ✅ Folio + Actions |
| No `app/Repositories/` | ✅ rimosso 2026-07-09 (STORY-489) |
| QueueableAction owner | ✅ pattern target |

## Findings ranked

| # | Tag | Cosa | Sostituzione | Path | Stato |
|---|-----|------|--------------|------|-------|
| F1 | `delete` | `WorkflowService` — orchestrazione stati | `TicketStatusEnum` + Action/eventi | `app/Services/WorkflowService.php` | ✅ assente al recheck 2026-09-26 |
| F2 | `delete` | `TicketService` — CRUD/wrapper | `Ticket::` + Actions esistenti | `app/Services/TicketService.php` | ✅ assente al recheck 2026-09-26 |
| F3 | `delete` | `NotificationService` — notifiche | `Notify` Actions / eventi | `app/Services/NotificationService.php` | ✅ assente al recheck 2026-09-26 |
| F4 | `reuse` | GeoJSON / filtri già in Actions | Non duplicare Service | `LoadPublicTicketsGeoJsonAction`, ecc. | ✅ canon |

## Wave 1 (non toccare in audit automatico)

- Migrazione Services → Actions completata nel filesystem osservato; questa fotografia non sostituisce test/regressione dei flussi.
- `SegnalazioniFilterViewModel` — OK (no Services layer).

## Collegamenti

- [codebase-gap-matrix.md](./wiki/concepts/codebase-gap-matrix.md)
- [folio-api-no-controllers.md](./wiki/concepts/folio-api-no-controllers.md)
- [Xot ponytail audit](../../Xot/docs/ponytail-audit-over-engineering.md)
