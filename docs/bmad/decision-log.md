---
title: "Decision log — Fixcity"
type: decision-log
module: Fixcity
status: active
created: 2026-10-16
updated: 2026-10-16
tags: [bmad, decision-log, fixcity]
qmd: "decision log fixcity"
related:
  - ../bmad-planning.md
  - ../bmad-brainstorming.md
  - ./project-context.md
---

# Decision log — Fixcity

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
