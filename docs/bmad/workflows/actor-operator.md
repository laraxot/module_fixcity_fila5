---
title: "BMAD actor — Operatore PA"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, actor, operator, pa, assign, fixcity]
module: Fixcity
qmd: "operator pa assign responsible_id change status ticket fixcity workflow"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-citizen.md
  - actor-supervisor.md
  - ../stories/STORY-002-assign-ticket.md
  - ../stories/STORY-499-pa-ticket-assignment.md
---

# Attore: Operatore PA

**Scopo:** prendere in carico le segnalazioni autorizzate, assegnarle e farle
avanzare nello stato macchina senza perdere audit/notifica.

## Happy path

1. Accede al backoffice Filament (`TicketResource` / `XotBase*`).
2. Filtra coda (`BuildPublicTicketsQueryAction` / filtri resource).
3. Apre dettaglio; verifica capability `view`/`update`/`assign`/`changeStatus`.
4. Assegna o si auto-assegna via `AssignTicketAction` → colonna `responsible_id`.
5. Cambia stato con `ChangeStatus` solo verso target in
   `TicketStatusEnum::allowedTransitions()`.
6. Registra attività (`RecordTicketActivityAction`) e motiva la transizione.
7. Il cittadino riceve aggiornamento (Notify) e vede timeline aggiornata.

## Limiti di capability

- Operatore e supervisore non eliminano ticket per ruolo. La cancellazione è
  soft-delete e richiede il ruolo `admin` o il permesso esplicito
  `ticket.delete`; l'azione bulk applica la stessa policy per ciascun record e
  registra `deleted_by`.
- Il percorso di eliminazione non deve essere presentato come azione operativa
  ordinaria della coda PA.

## Failure path

- assign/changeStatus negato da policy;
- transizione illegale;
- motivazione assente quando richiesta;
- activity/notifica fallita → blocker esplicito, non “successo silenzioso”.

## Prove

Feature test assign + transition; policy operator vs citizen; smoke BO. The
demo identity is provisioned by `DemoOperatorPanelAccessSeeder`: `operator`
grants ticket capabilities while `fixcity::admin` satisfies the panel gate.
STORY-526 verifies least privilege, citizen denial, idempotency and the live
ticket-queue panel smoke.

## SSoT colonna

`responsible_id` è la colonna reale; le relazioni `assignee`/`responsible` devono
allinearsi a essa (vedi STORY-499).
