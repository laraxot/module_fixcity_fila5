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

## 2026-10-26 - Igiene di sicurezza: radice web, gate, bootstrap, login API, proxy fidati, auto-writer (DCP-282)

- **Radice web:** i file di prova e debug di `public_html` (`info.php` con `phpinfo()`, `test.txt`, `serve-root-test.txt`, `hot.stale*`,
  `data/tickets_{test,big}.json`, `data/tickets-test.json`) sono ritirati con `.old` (ignorato da git: non viaggia col deploy).
  Erano tornati con un commit di sync dopo che DCP-186 li aveva tolti. Il gate ha ora `REPO-03`: fallisce per ogni `.php`/`.phtml`/`.phar`
  diverso da `index.php`, per ogni `.env*` (anche `.env.old`) e per un `index.php` che chiama `phpinfo(`.
- **Gate onesto su B-02:** `LEG-04` conta anche `data-element="accessibility-incomplete"`, il marcatore reale della dichiarazione di
  accessibilita' vuota (prima solo `legal-incomplete`). Pagine con avviso: da 12 a 16 su 20.
- **Provider di sviluppo:** Boost e Mcp in `bootstrap/providers.php` passano da `class_exists`; con `--no-dev` il boot non cade.
- **Rotta orfana:** `POST /livewire/form-create-ticket` rimossa (nessun chiamante, senza `auth` ne' `throttle`); il wizard parla con Livewire.
- **Login API:** `POST /api/user/login` ha `throttle:user-login`, 5 richieste per 60 secondi per `email|IP`, stesso messaggio e stesse soglie del
  `LoginWidget` (G-07), logica in `BuildApiLoginLimitAction`. Differenza voluta: il middleware conta ogni richiesta, il widget solo gli errori.
- **Proxy e host fidati:** `trustedproxy.proxies` da `TRUSTED_PROXIES`, default `127.0.0.1` (come prima). `env()` in `withMiddleware` non vede
  `.env` (il kernel e' risolto prima del caricamento): la lettura DEVE passare dalla config. `trustHosts` e' opt-in via `TRUSTED_HOSTS`
  (vuoto = nessuna restrizione; il framework lo salta con `APP_ENV=local` e nei test). Non attivato di default perche' il proxy di sviluppo
  192.168.1.40:8080 non e' provabile e NativePHP potrebbe usare altri `Host`.
- **Auto-writer di Lang:** il default di `lang.save_missing_translations` passa da `! runningUnitTests()` ad `app()->isLocal()`; l'override resta.
  Trappola: `Config::offsetUnset()` imposta la chiave a `null`, non la toglie, e `Config::get()` non restituisce piu' il default.
- **Aperto:** `Modules/Lang/config/lang.php` dice `true` (config non letta con quel nome), `config/security.php` `rate_limit` non e' letta da nessuno,
  `TRUSTED_HOSTS` obbligatoria in produzione e `ENV-10` nel gate, `404` come risposta alle credenziali errate dell'API. Story:
  [dcp-282](../stories/dcp-282-igiene-di-sicurezza-radice-web-bootstrap-e-gate.story.md).

## 2026-10-26 - Back office: stati consentiti, assegnatari PA, pagine legacy (DCP-281)

- **Decisione:** l'azione Filament `changeStatus` offre solo `TicketStatusEnum::allowedTargets()` dello stato corrente (nessuna
  matrice duplicata) ed e' nascosta negli stati terminali. Il campo «assegna a» elenca solo utenti con un ruolo di
  `BasePolicy::PA_ROLES` (unica fonte dei ruoli PA, costante ora `public`) tramite `ListAssignableTicketOperatorsAction`;
  `super-admin` non e' incluso perche' `isPaOperator` non lo include.
- **Pagine legacy:** `tickets/[slug]` e `tickets/[id]` del modulo erano oscurate dal tema in tutte le lingue (Folio: il primo
  percorso montato che combacia vince, e il tema e' montato prima dei moduli). Ritirate con `.old`/`.old2`; il dettaglio pubblico
  vive nel tema (DCP-148). Guardia: `tests/Feature/Pages/LegacyTicketPagesRetiredTest.php`.
- **Causa corretta di A-23:** `hasPermissionToOrCreate` NON scrive a ogni controllo; Spatie inserisce solo se il permesso manca.
  Toglierlo e' una decisione di architettura che tocca `Modules/User` (vedi la story, sezione «Da decidere»).
- **A-02 chiuso:** `AssignTicketAction` e `Actions/ChangeStatus` (riga 49) ora registrano l'UUID dell'attore con la stessa forma
  `is_int || is_string` delle altre Action. Il lock su `ChangeStatus.php` (2026-09-26, `codex`, `fixcity-story-500-501`) era
  orfano e il coordinatore ne ha autorizzato la rimozione; il test `TicketActivityActorUuidTest` non ha piu' `skip`.
- **Correzione di una misura:** una prima versione della story diceva che il dettaglio pubblico `/{lang}/tickets/{id}` rispondeva
  404 per segnalazioni pubbliche. Era misurato sull'istanza sbagliata (id 29..32 non esistono nella fixture `:8091`). Rimisurato:
  200 per le pubbliche su entrambe le istanze, 404 per id assenti e per `pending`. Affermazione e causa ritirate.
- **Riferimento:** [DCP-281](../stories/dcp-281-rinforzo-back-office-e-pagine-legacy.story.md).

## 2026-10-26 — Gate PHPStan completo verificato

- **Decisione:** il gate canonico `cd laravel && ./vendor/bin/phpstan analyse Modules --no-progress --error-format=table` ha restituito `[OK] No errors` con exit code 0.
- **Perché:** il gate misura il working tree reale e conferma che non servono ignore, baseline, cast o modifiche a `phpstan.neon`.
- **Vincolo:** il risultato va rieseguito dopo ogni modifica PHP; non sostituisce i test runtime, i gate browser, l'accessibilità o la verifica staging.
- **Riferimento:** [prompt PHPStan canonico](../../../../../bashscripts/docs/prompts/11-phpstan.md).

## 2026-10-20 — Gate PHPStan Modules zero; Filament blueprint SSoT UI admin

- **Decisione:** gate `analyse Modules` confermato a zero; ticket/segnalazioni BO e widget FO restano definiti in [filament-blueprint.md](./filament-blueprint.md), non duplicati nel tema.
- **Track:** voce sotto «Filament blueprint + PHPStan»; lifecycle [ticket-lifecycle-filament-blueprint.md](./ticket-lifecycle-filament-blueprint.md).

## 2026-10-20 — Filament blueprint + PHPStan (sorgente, non messaggio)

- **Decisione:** con Modules a zero, ogni nuova segnalazione Filament si risolve leggendo prima `filament-blueprint.md` (scopo UI) poi tipizzando; mai zittire.
- **Track:** BMAD + second brain + PHPStan standing order

## 2026-10-20 — Services ticket: assenti, Actions = SSoT (boy scout)

- **Stato:** `Modules/Fixcity/app/Services/` **assente** (0 `*Service.php`). Workflow/notifiche/ticket = cartella `app/Actions/` (es. `TransitionTicketStatusAction`, notify Actions, CRUD/timeline Actions). Census sera Xot: campione `laraxot/dev` Fixcity = **0** file in `app/Services/` (allineato a [no-services-rule](../../../../../bashscripts/ai/wiki/rules/no-services-rule.md)).
- **Da non migrare ora:** nessun monolite da spezzare in questo turn; eventuali test storici sotto `Unit/Services/` vanno riscritti su Actions se riesumati da merge, non reimportando Service.
- **Track:** [decisions/bmad-plan-services-to-actions.md](../decisions/bmad-plan-services-to-actions.md), census Xot [decision-log](../../../Xot/docs/bmad/decision-log.md).

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
- **Artefatto User:** [authentication-logs-connection-ownership.md](../../../User/docs/bmad/authentication-logs-connection-ownership.md)

## 2026-10-18 — Consumer vedono UserContract, non BaseUser

- **Decisione:** i consumer (Auth listener/action, policy, tema) riconoscono
  l'utente con `UserContract` (+ capability come `HasAuthentications`), mai
  con `instanceof BaseUser` / type-hint `BaseUser`.
- **Motivo:** `Modules\Fixcity\Models\User` è sostituibile via contratto;
  vincolare l'astratta rompe l'estensibilità cross-modulo.
- **Artefatto:** [usercontract-boundary](../../../User/docs/wiki/concepts/usercontract-boundary.md),
  [user-model-architecture](../wiki/concepts/user-model-architecture.md).

## [2026-10-18] permissions su fixcity_user (non Fixcity)

- **Decisione:** Spatie `permissions` resta nel modulo User / `fixcity_user`.
- **Sintomo:** dettaglio ticket pubblico 500 via `BuildTicketTimelineAction`.
- **Mitigazione Fixcity:** soft-guard se tabella assente; ownership canonica User.
- **Artefatto:** [../../User/docs/bmad/permissions-connection-ownership.md](../../../User/docs/bmad/permissions-connection-ownership.md)

## [2026-10-26] notifiche del ciclo di vita (DCP-284, A-01)

- **Decisione:** gli eventi `approved`, `rejected`, `assignment` (solo la prima), `closed` notificano il solo proprietario (database, mail se verificata e preferenza attiva) dal punto unico `TicketActivityObserver::created`; `status_change` resta di `NotifyTicketSubscribersOfStatusChangeAction` (insiemi disgiunti, un cambio = un messaggio); `media_added` non notifica; follower esclusi finche' non si decide il codice di tracciamento nelle notifiche di stato (DD-3).
- **Creazione:** conferma al proprietario e avviso agli utenti con ruolo di `BasePolicy::PA_ROLES` (escluso il proprietario), con solo id e link al back office. Scoping per comune NON modellato (nessuna colonna sul ticket, tenant vuoti): DA DECIDERE (DD-1).
- **Trappola:** il listener di `TicketCreatedEvent` e' registrato con `Event::listen` in `EventServiceProvider::boot()`, non in `$listen`: `bootstrap/cache/events.php` riporta `[]` per questo provider e la cache degli eventi ignora `$listen`.
- **Artefatto:** [../stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md](../stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md)
