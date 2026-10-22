---
title: "Decision log — Fixcity"
type: decision-log
module: Fixcity
status: active
created: 2026-10-16
updated: 2026-10-20
tags: [bmad, decision-log, fixcity]
qmd: "decision log fixcity"
related:
  - ../bmad-planning.md
  - ../bmad-brainstorming.md
  - ./project-context.md
  - ../phpstan-fixes.md
---

# Decision log — Fixcity

## 2026-10-20 — Gate PHPStan Modules zero; Filament blueprint SSoT UI admin

- **Decisione:** gate `analyse Modules` confermato a zero; ticket/segnalazioni BO e widget FO restano definiti in [filament-blueprint.md](./filament-blueprint.md), non duplicati nel tema.
- **Track:** voce sotto «Filament blueprint + PHPStan»; lifecycle [ticket-lifecycle-filament-blueprint.md](./ticket-lifecycle-filament-blueprint.md).

## 2026-10-20 — Filament blueprint + PHPStan (sorgente, non messaggio)

- **Decisione:** con Modules a zero, ogni nuova segnalazione Filament si risolve leggendo prima `filament-blueprint.md` (scopo UI) poi tipizzando; mai zittire.
- **Track:** BMAD + second brain + PHPStan standing order

## 2026-10-20 — Services ticket: assenti, Actions = SSoT (boy scout)

- **Stato:** `Modules/Fixcity/app/Services/` **assente** (0 `*Service.php`). Workflow/notifiche/ticket = cartella `app/Actions/` (es. `TransitionTicketStatusAction`, notify Actions, CRUD/timeline Actions). Census sera Xot: campione `laraxot/dev` Fixcity = **0** file in `app/Services/` (allineato a [no-services-rule](../../../bashscripts/ai/wiki/rules/no-services-rule.md)).
- **Da non migrare ora:** nessun monolite da spezzare in questo turn; eventuali test storici sotto `Unit/Services/` vanno riscritti su Actions se riesumati da merge, non reimportando Service.
- **Track:** [decisions/bmad-plan-services-to-actions.md](../decisions/bmad-plan-services-to-actions.md), census Xot [decision-log](../../Xot/docs/bmad/decision-log.md).

## 2026-10-20 — PHPStan Modules a zero verificato

- **Decisione:** il gate `analyse Modules` e' a `[OK] No errors` (cache cleared).
- **Perche:** standing order permanente (swarm + BMAD + second brain); zero = base per boy scout.
- **Track:** PHPStan Modules zero

## 2026-10-20 — Stati ticket: eventi ≠ status enum

- **Decisione:** `DemoTicketsSeeder` e ogni seed/demo usano solo case di
  `TicketStatusEnum` (`PENDING`, `IN_REVIEW`, `IN_PROGRESS`, `RESOLVED`,
  `CLOSED`, …). `Approved` / `Assigned` restano **eventi** di
  `TicketActivityEventTypeEnum`, non stati del ticket.
- **Perché:** lo scopo del workflow è la matrice `allowedTransitions()`;
  inventare case PascalCase (`Pending`, `Approved`) spezza demo e PHPStan
  (`classConstant.notFound`) e confonde moderazione/assegnazione con lo stato.
- **Route:** `routes/api_ticket.php` è stub senza controller (Folio + Actions).
- **Track:** PHPStan Modules zero + BMAD

## 2026-10-20 — PHPStan zero su `Modules/Fixcity/tests`

- **Decisione:** la suite test Fixcity resta analizzabile con Larastan senza
  `@phpstan-ignore`; i test verificano il contratto reale (modello `Ticket`,
  Actions, enum), non API legacy o case enum inventati.
- **Verifica:** snapshot `/tmp/phpstan-by-mod/Fixcity.json` (791 voci con
  `/tests/`); run `cd laravel && ./vendor/bin/phpstan analyse Modules/Fixcity/tests --no-progress` → **0 errori** (2026-10-20).
- **Pattern:** `method.notFound` / `property.nonObject` → factory `createOne()` +
  metodi esistenti; `classConstant.notFound` → case `TicketStatusEnum` reali
  (`PENDING`, non `Pending`; `Approved` è evento activity, non status);
  `pest.expectation.redundant` → assert semplificati; `missingType.return` →
  return type sulle closure Pest; `class.notFound` su Pest →
  `uses(\Modules\Fixcity\Tests\TestCase::class)` **prima** di `use TestCase`
  (FQCN obbligatorio per Larastan).
- **Ex-top file (snapshot):** `Feature/Livewire/TicketFormTest.php`,
  `Feature/Pages/TicketPagesTest.php`, `Unit/Services/NotificationServiceTest.php`,
  test enum ticket, `TicketControllerTest`, `TicketWorkflowIntegrationTest`.
- **Riferimento:** [tests-placement-policy.md](../tests-placement-policy.md),
  [wiki/concepts/phpstan-pest-testcase-helpers.md](../wiki/concepts/phpstan-pest-testcase-helpers.md).

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
