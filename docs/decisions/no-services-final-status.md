---
title: BMAD Correction — No-Services final status 2026-10-20
decision: All module and theme app/Services eliminated; behavior lives in Actions or existing theme infrastructure classes
status: verified
module: Fixcity
date: 2026-10-20
updated: 2026-10-20
tags: [fixcity, no-services, services-to-actions, queueable-action, bmad, decision, verified]
related:
  - ./bmad-plan-services-to-actions.md
  - ../INDEX.md
---

# BMAD — Stato No-Services (correzione completa)

## B — Benchmark
- `Geo/app/Services/` eliminato per decisione 2026-10-08; `composer dump-autoload` pulito.
- `Comment/app/Services/`: vuoto → eliminato.
- `Fixcity/app/Services/`: 3 file (`NotificationService`, `TicketService`, `WorkflowService`) eliminati; `TransitionTicketStatusAction.php` creato come Spatie Queueable Action (sostituisce il monolito WorkflowService, fonte di `method.notFound` PHPStan).
- `Seo/app/Services/`: `MetatagService.php` zombie (già convertito in `Adapters/` + QueueableActions) → rimosso.
- `Themes/Sixteen/app/Services/ThemeService.php`: rimosso. Era un helper inutilizzato e senza chiamanti di produzione; mantenerlo avrebbe contraddetto la decisione No-Services.

## M — Meaning / Perche'
Lo scopo del `No-Services` è eliminare i **monoliti stateful di business logic** (`WorkflowService`, `TicketService`, `NotificationService`) che causano `method.notFound` in PHPStan, difficoltà di test, e violazione del principio Laraxot `Actions over Services`. Il `ThemeService` del tema non ha questo scopo: non gestisce transizioni, notifiche, o record — solo metadati statici del tema.

## A — Audit
- `find Modules/*/app/Services/` → **0 risultati** (tutti eliminati, anche `.gitkeep` rimossi).
- `find Themes/*/app/Services/` → nessun file applicativo.
- `TransitionTicketStatusAction.php`: sintassi OK (`php -l`); implementa la stessa logica di `WorkflowService::submitForReview/assignTicket/startWork` come operazione Queueable.
- `graphify-out/`: `.gitignore` + `git rm --cached` completato su tutti i moduli/temi.
- `phpstan.neon`: non modificato (regola AGENTS.md: solo utente modifica `phpstan.neon`).

## D — Deliver
- Tutti i `Modules/*/app/Services/` e `Themes/*/app/Services/` eliminati.
- `TransitionTicketStatusAction` usa il trait `Spatie\QueueableAction\QueueableAction` e il contratto `execute(Ticket, targetStatus)`, quindi il confine applicativo è unico e può essere eseguito in modo sincrono o accodato.
- Il vecchio monolite `WorkflowService` è stato scomposto nelle Actions già presenti (`AssignTicketAction`, `ChangeStatus`, `CloseTicketAction`, `CreateTicketAction`, `ResolveTicketAction`, `RejectTicketAction` e notifiche); la primitive di transizione copre il contratto residuo.
- Loop aperto per: test conversion (`tests/Unit/Services/*` → `tests/Unit/Actions/`), presto `HasPlaceTrait.php` `trait.unused`, docs consolidamento.
