# Ponytail audit — Fixcity (over-engineering)

**Ultimo run:** 2026-07-09  
**Modulo:** dominio ticket/segnalazioni, workflow PA.

**Hub:** [../../../../docs/project/ponytail-audit-hub.md](../../../../docs/project/ponytail-audit-hub.md)  
**Remediation:** [../../../../docs/project/ponytail-audit-remediation.md](../../../../docs/project/ponytail-audit-remediation.md)  
**GitHub:** issue STORY-487 (base_fixcity_fila5)

## Vincoli Laraxot (ponytail × BMAD)

| Regola | Stato |
|--------|-------|
| No `app/Services/*` per business logic | ⚠️ 3 classi legacy |
| No `Http/Controllers` | ✅ Folio + Actions |
| No `app/Repositories/` | ✅ rimosso 2026-07-09 (STORY-489) |
| QueueableAction owner | ✅ pattern target |

## Findings ranked

| # | Tag | Cosa | Sostituzione | Path | Stato |
|---|-----|------|--------------|------|-------|
| F1 | `delete` | `WorkflowService` — orchestrazione stati | `TicketStatusEnum` + Action/eventi | `app/Services/WorkflowService.php` | aperto → STORY-392 |
| F2 | `delete` | `TicketService` — CRUD/wrapper | `Ticket::` + Actions esistenti | `app/Services/TicketService.php` | aperto → STORY-392 |
| F3 | `delete` | `NotificationService` — notifiche | `Notify` Actions / eventi | `app/Services/NotificationService.php` | aperto → STORY-402 |
| F4 | `reuse` | GeoJSON / filtri già in Actions | Non duplicare Service | `LoadPublicTicketsGeoJsonAction`, ecc. | ✅ canon |

## Wave 1 (non toccare in audit automatico)

- Migrazione Services → Actions è **multi-story** (392, 402); non delete cieco senza test Pest.
- `SegnalazioniFilterViewModel` — OK (no Services layer).

## Collegamenti

- [codebase-gap-matrix.md](./wiki/concepts/codebase-gap-matrix.md)
- [folio-api-no-controllers.md](./wiki/concepts/folio-api-no-controllers.md)
- [Xot ponytail audit](../../Xot/docs/ponytail-audit-over-engineering.md)
