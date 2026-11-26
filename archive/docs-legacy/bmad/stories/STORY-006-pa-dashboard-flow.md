---
title: PADashboardFlow
id: STORY-006
author: BMAD
status: done
priority: must
type: story
module: Fixcity
tags:
- bmad
- fixcity
created: legacy
updated: 2026-09-26
qmd: story 006 pa dashboard flow FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Flusso PA Dashboard (PA Operator → Tickets)

1. **Login** → PA Operator entra nel backoffice (`/admin/fixcity/tickets`)
2. **Lista** → Visualizza lista ticket filtrata per stato/tipo
3. **Filtro** → Applica filtri tramite `BuildTicketFilterAggregateAction`
4. **KPI** → Visualizza metriche tramite `GetTicketKpiAggregateAction`
5. **Timeline** → Visualizza attività tramite `BuildTicketTimelineAction`
6. **Azione** → Assegna tramite `AssignTicketAction`
7. **Aggiornamento** → Sistema refresha automaticamente la vista

## Attori
- **PA Operator** (dashboard user)
- **System** (data provider)
- **Citizen** (reporter, read-only access)

## Regole architetturali
- Resource Pages estendono `XotBase*` (non Filament diretto)
- Actions in `app/Actions/` con `QueueableAction`
- No HTTP Controllers - solo Folio pages + Actions
- `foreignIdFor()` pattern nelle migrazioni
- Migrazioni solo `updateTimestamps()` in `tableUpdate`

## Second Brain
- `docs/chat/pa-dashboard-flow.md`
- `docs/wiki/log.md` aggiornato
- Documentazione in `laravel/Modules/Fixcity/docs/bmad/stories/`

## Quality Gate
- PHPStan 0 errori su `Modules/Fixcity`
- Test `tests/Feature/Filament/TicketResourceTest.php` passa
- Test `tests/Feature/Filament/CreateTicketWizardWidgetTest.php` passa
