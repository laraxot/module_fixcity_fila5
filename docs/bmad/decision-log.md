---
title: "Decision log — Fixcity"
type: decision-log
module: Fixcity
status: active
created: 2026-10-16
updated: 2026-10-19
tags: [bmad, decision-log, fixcity]
qmd: "decision log fixcity"
related:
  - ../bmad-planning.md
  - ../bmad-brainstorming.md
  - ./project-context.md
---

# Decision log — Fixcity

## 2026-10-19 — Niente `$model_class` se il file è `create_{modello}_table`

- **Decisione:** `2026_01_01_000001_create_users_table.php` non assegna
  `$model_class`. `XotBaseMigration::getModelClass()` legge
  `_create_users_table.php` nel modulo Fixcity e ottiene
  `Modules\Fixcity\Models\User`.
- **Perché:** una riga in più non aggiunge un contratto. Il nome file è già il contratto.

## 2026-10-16 — Kit BMAD owner

- **Decisione:** adottare `docs/bmad-planning.md` + `docs/bmad-brainstorming.md` + `docs/bmad/filament-blueprint.md` come SSoT di planning per questo owner.
- **Perche:** rimuovere stub copiati "Ticket Timeline" non pertinenti; allineare second brain e Filament blueprint allo scopo reale.
- **Track:** `bmad-method`

## 2026-10-18 — Ownership `ticket_subscribers` e UUID

- **Decisione:** una sola migration owner per `TicketSubscriber`; le migration
  storiche `update_*`/`align_*` restano archiviate come `*.php.old`.
- **Motivo:** `user_id` è UUID su connessione `user`; `foreignIdFor()` era
  incompatibile e `align_*` violava l'ownership per modello.
- **Artefatto:** [migration-ticket-subscribers-audit.md](migration-ticket-subscribers-audit.md)
- **Track:** `bmad-method` + Second Brain

## 2026-10-18 — Dati sacri e testing non distruttivo

- **Decisione:** vietati permanentemente `migrate:fresh`, `migrate:refresh`,
  `migrate:reset`, `db:wipe`, `RefreshDatabase` e `migrate --force`.
- **Ambito:** produzione, sviluppo, test, fixture, CI e script automatici.
- **Alternativa:** migrazioni forward-only e isolamento con transazioni o DB
  dedicato non condiviso.
- **Motivo:** il database contiene dati sacri e nessun test può cancellarli.

## 2026-10-18 — Filament user e connessione UUID

- **Decisione:** `Modules\Fixcity\Models\User` eredita la connessione `user`
  da `BaseUser`; non deve puntare a `fixcity.users` legacy con chiave intera.
- **Motivo:** autenticazione e `make:filament-user` generano UUID; il database
  owner `user.users` usa `varchar(36)`.
- **Artefatto:** [filament-user-uuid-connection-audit.md](filament-user-uuid-connection-audit.md)
- **Vincolo:** nessuna conversione o cancellazione della tabella legacy.
- **Anti-pattern:** non creare `update_users_id_to_string_uuid` su
  `fixcity.users` (archiviato come `.php.old`); il fix è la connessione, non
  lo schema legacy.

## 2026-10-18 — authentication_logs non in fixcity_data

- **Decisione:** i log auth restano nel modulo User (`fixcity_user`); Fixcity
  non possiede né migra `authentication_logs`.
- **Motivo:** stesso ownership di `users` UUID; errore prod su fixcity.eu
  (`fixcity_data.authentication_logs` missing) = env/migrate su connection
  `user`, non dominio Fixcity.
- **Artefatto User:** [authentication-logs-connection-ownership.md](../../User/docs/bmad/authentication-logs-connection-ownership.md)

## 2026-10-18 — Consumer vedono UserContract, non BaseUser

- **Decisione:** i consumer (Auth listener/action, policy, tema) riconoscono
  l'utente con `UserContract` (+ capability come `HasAuthentications`), mai
  con `instanceof BaseUser` / type-hint `BaseUser`.
- **Motivo:** `Modules\Fixcity\Models\User` è sostituibile via contratto;
  vincolare l'astratta rompe l'estensibilità cross-modulo.
- **Artefatto:** [usercontract-boundary](../../User/docs/wiki/concepts/usercontract-boundary.md),
  [user-model-architecture](../wiki/concepts/user-model-architecture.md).

## [2026-10-18] permissions su fixcity_user (non Fixcity)

- **Decisione:** Spatie `permissions` resta nel modulo User / `fixcity_user`.
- **Sintomo:** dettaglio ticket pubblico 500 via `BuildTicketTimelineAction`.
- **Mitigazione Fixcity:** soft-guard se tabella assente; ownership canonica User.
- **Artefatto:** [../../User/docs/bmad/permissions-connection-ownership.md](../../User/docs/bmad/permissions-connection-ownership.md)
