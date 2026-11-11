---
title: "Fixcity — Audit dati, backend e performance"
type: audit
tags: [fixcity, audit, database]
created: 2026-11-05
updated: 2026-11-05
status: active
related:
  - ./README.md
  - ./COMPLETION_ROADMAP.md
---

> Analisi statica del branch `dev` del 2026-11-05: niente vendor/, PHPStan e Pest non eseguiti in questo ambiente.
> Lo stato aggiornato di ogni rilievo (risolto/aperto) è in [COMPLETION_ROADMAP.md](./COMPLETION_ROADMAP.md).

# Audit backend e dati — Fixcity (base_fixcity_fila5, branch dev)

Data: 2026-11-05. Audit in sola lettura, analisi statica (grep/ripgrep/python, `git log` per la storia dei file). Nessun artisan, PHPStan o test eseguito: niente vendor/ e PHP 8.3 locale contro ^8.4 richiesto.
Percorsi relativi a `laravel/` salvo dove indicato.

---

## 1. Come funziona

### 1.1 Modello dati

**Connessioni.** `Modules/Fixcity/app/Models/BaseModel.php:21` usa la connessione `fixcity` per tutti i modelli Fixcity. `TenantServiceProvider::mergeModuleConnections()` (`Modules/Tenant/app/Providers/TenantServiceProvider.php:138-158`) crea una connessione per ogni modulo (`fixcity`, `media`, `notify`…) copiando la connessione di default. Quindi in pratica sono tutte lo stesso database (`DB_DATABASE`, default `fixcity_data`, in `config/local/fixcity/database.php`). L'eccezione è `user`, che punta a un database separato (`fixcity_user`). `TicketSubscriber` vive su `user` (`Models/TicketSubscriber.php`, `$connection = 'user'`): le sottoscrizioni stanno nel database degli utenti, non accanto ai ticket.

**Base comune.** `BaseModel` estende `XotBaseModel` con `SoftDeletes` (`BaseModel.php:15`). Le migrazioni estendono `XotBaseMigration`:
- `tableCreate()` crea la tabella solo se manca;
- `tableUpdate()` aggiunge le colonne mancanti;
- `updateTimestamps()` aggiunge `created_at/updated_at/created_by/updated_by` e, se richiesto, `deleted_at/deleted_by`;
- `down()` di default fa **drop della tabella del modello** (`Modules/Xot/app/Database/Migrations/XotBaseMigration.php:271-274`).

**`tickets`.** Ci sono tre migrazioni "create" sovrapposte: `2023_01_01_000005`, `2026_04_29_110000`, `2026_06_10_140000`. Lo schema effettivo è l'unione delle tre più `2026_10_22_000002`.

| Gruppo | Colonne |
|---|---|
| Dominio | `id`, `name`, `content` (longText), `owner_id` string(36) NOT NULL, `responsible_id` string(36) null, `code` string null, `slug` **unique**, `status`/`type`/`priority` string null (enum PHP), `location` json, `latitude`/`longitude` decimal(20,18), `email` |
| Legacy da gestionale di progetto | `status_id`, `priority_id`, `project_id`, `epic_id`, `sprint_id`, `estimation`, `order`, `ticket_prefix`, `type_id` int **indicizzata** |
| Audit | timestamps, soft delete, `created_by`/`updated_by`/`deleted_by` |

Indici presenti su `tickets`: solo `slug` (unique) e `type_id`, che è legacy e non usata (`2026_06_10_140000_create_tickets_table.php:49,70`). Nessuna FK.

**Modello `Ticket`** (`Models/Ticket.php`):
- traits: HasComments (modulo Comment), HasSlug, HasStatuses di Spatie (con alias, la storia "statuses" è legacy), InteractsWithMedia, citizen rating, normalizzazione della location;
- `$fillable` comprende `citizen_rating` e `citizen_rated_at` (`:199-200`);
- cast: `status` → `TicketStatusEnum`, `type` e `type_id` → `TicketTypeEnum` (`:232-244`);
- default `PENDING` in `creating` (`:337-341`);
- normalizzazione `location` → `latitude/longitude` in `saving` (`:343-354`);
- media collection `attachments` (jpeg/png/pdf) e `ticket` (`:423-430`).

**Tabelle collegate:**
- `ticket_activities` (`2023_01_01_000000` + `2026_09_26_151800`): `ticket_id` **senza indice**, `event_type`, `payload` json (`from`/`to`/`v`), `visibility`, `reason`, `user_id` uuid indicizzata. È la **timeline canonica**: la scrive `RecordTicketActivityAction` e la legge `BuildTicketTimelineAction`.
- `ticket_subscribers` (database `user`): unique (`ticket_id`,`user_id`), soft delete.
- `ticket_comments` (legacy, `ticket_id` senza indice) e `ticket_relations` (nessun indice), entrambe senza scrittori nel codice applicativo.
- `ticket_hours` (legacy).
- `timeline_steps` + `timeline_attachments` (`2026_10_16_*`): l'unica FK verso `tickets` (cascadeOnDelete). È una seconda timeline parallela, usata solo dal widget `TimelineStepper`.
- `categories` (indici su name/parent/is_active/sort) e `reports` (FK a categories). `Report` non è usato da nessuna parte.
- `faqs`, `profiles` (tre migrazioni "create").

### 1.2 Ciclo di vita del ticket

**Enum** (`Modules/Fixcity/app/Enums`):
- `TicketStatusEnum`: draft, pending, in_review, in_progress, on_hold, resolved, closed, reopened, open.
  - `canViewByAll()` contiene gli stati pubblici, `canNotViewByAll()` = draft, pending.
  - `allowedTransitions()` (`:93-135`) è la fonte unica del workflow: pending→in_review; in_review→in_progress|on_hold; in_progress→on_hold|resolved; on_hold→in_progress|resolved; resolved→reopened|closed; reopened→in_progress; open→pending; draft e closed terminali.
  - `default()` restituisce **OPEN** (`:72`).
- `TicketLifecycleEventEnum`: received, approved, rejected, assigned, closed. È la mappatura da `TicketActivityEventTypeEnum` agli eventi notificati al proprietario.
- `TicketPriorityEnum`, `TicketTypeEnum` (15 tipi, string-backed).

**Creazione (front office).** `CreateTicketWizardWidget::save()` (`Filament/Widgets/CreateTicketWizardWidget.php:85-131`):
1. controlla autenticazione e informativa privacy;
2. `SubmitTicketOnceAction` fa idempotenza con cache lock (token per form);
3. stato `PENDING` e priorità `LOW` decisi dal server;
4. `CreateTicketAction` (`Actions/CreateTicketAction.php:22-47`): alloca `code` (`AllocateTicketCodeAction`, `TCK-` + 16 caratteri casuali), `Ticket::create`, dispatch di `TicketCreatedEvent`, `SendTicketReceiptAction`, flash dei dati di conferma;
5. solo dopo, `form->saveRelationships()` salva le foto (`:135-143`).

**Creazione (admin).** `Filament/Resources/TicketResource/Pages/CreateTicket.php` → `GetTicketFormDataForPersistAction`, che normalizza location e type e mette lo stato di default. Poi c'è il create standard di Filament: niente `code`, niente evento.

**Cambi di stato (operatore, Filament).** Header actions di `ViewTicket.php`, tutte con `Gate::authorize('changeStatus'|'assign')`:
- `ApproveTicketAction`: pending→in_review, evento Approved;
- `RejectTicketAction`: torna o resta pending, evento Rejected, visibilità AuthorOnly;
- `ResolveTicketAction` → `ChangeStatus`: resolved;
- `CloseTicketAction`: resolved→closed;
- `AssignTicketAction`: `lockForUpdate`, evento Assignment;
- Filament `ChangeStatus`: stato libero tra i target consentiti.

Ogni azione scrive lo stato e una riga `ticket_activities` dentro una transazione sulla connessione `fixcity`. `TicketActivityObserver` (`Observers/TicketActivityObserver.php`, `ShouldHandleEventsAfterCommit`, registrato in `FixcityServiceProvider.php:41`) chiama due action:
- `NotifyTicketSubscribersOfStatusChangeAction`: solo eventi `status_change` con visibilità pubblica, verso follower e proprietario;
- `NotifyTicketOwnerOfLifecycleEventAction`: approved, rejected, prima assegnazione, closed, solo al proprietario.

Le notifiche (`Notifications/*`) sono tutte `ShouldQueueAfterCommit`, canali `database` + `mail` (la mail solo se l'email è verificata e il cittadino non l'ha disattivata). Il modulo Notify **non** è usato da Fixcity.

**Ricezione.** `TicketCreatedEvent` → `NotifyTicketReceivedListener` → `NotifyTicketReceivedAction`: lifecycle `Received` al proprietario e `TicketReceivedStaffNotification` a **tutti** gli operatori PA (`Providers/EventServiceProvider.php:27`).

**Timeline.** `BuildTicketTimelineAction` legge `ticket_activities` filtrate per visibilità (Public / AuthorOnly per proprietario e operatore / Internal per l'operatore).

**Sottoscrizioni.** `FollowTicketAction` → `SetTicketSubscriptionAction`: transazione su `user`, restore del soft delete.

**Valutazione.** `SubmitCitizenTicketRatingAction` scrive `citizen_rating` e `citizen_rated_at` (solo il proprietario, solo resolved o closed).

### 1.3 Multi-tenant

Il "tenant" è una **cartella di configurazione per hostname**:
- `GetTenantNameAction` (`Modules/Tenant/app/Actions/GetTenantNameAction.php:26-36`) ricava `config/<parti host invertite>` da `SERVER_NAME`, per esempio `config/local/fixcity`;
- `TenantServiceProvider::registerDB()` (`:61-74`) carica `config/<tenant>/database.php` e ne fonde le connessioni;
- il database resta guidato da `env('DB_DATABASE')`, quindi dal `.env` del deployment;
- l'"ente" (comune titolare) è uno solo per tenant (`config/local/fixcity/ente.php`, `EnteData`).

Il risultato è un isolamento **per installazione/vhost** (modello silo), non per riga:
- nessuna colonna `tenant_id`/`comune` su `tickets`;
- nessuno scope globale;
- la tenancy Filament (`Modules/Xot/app/Actions/Panel/ApplyTenancyToPanelAction.php:32`) non è mai invocata.

Il codice stesso lo dice: "Il comune non e' una colonna del ticket: lo scoping per comune e' DA DECIDERE (story DCP-284, DD-1)" (`Actions/NotifyTicketReceivedAction.php:23`). I seeder demo distribuiscono ticket su molti comuni nello stesso database (`database/seeders/Demo/DemoMunicipalityProvider.php`).

### 1.4 Code, job, scheduler

- Code: `config/queue.php:18-20` usa `QUEUE_CONNECTION`, default `database`. `.env.fixcity:36` e `.env.latest:36` = `sync`; `.env.production.example:27` = `redis`. Le tabelle `jobs`, `job_batches`, `failed_jobs`, `exports` sono nel modulo Job.
- Lavoro accodato: tutte le notifiche Fixcity (`ShouldQueueAfterCommit`), le conversioni media (`config/local/fixcity/media-library.php:57`, `queue_conversions_by_default=true`), gli export Filament (`TicketExporter`, batch).
- Scheduler: solo `routes/console.php`, con retention (`activitylog:clean`, `model:prune AuthenticationLog`, `PurgeDeletedAccountsAction`) più il comando `inspire` orario. Nessuno schedule nei provider (il Job module ha solo codice commentato, `Modules/Job/app/Providers/JobServiceProvider.php:30-70`). Nessun job di dominio: niente auto-chiusura, solleciti SLA o reminder.
- Nel repo non c'è configurazione di worker o scheduler (supervisor, systemd, Procfile).

### 1.5 Modulo AI

Le Actions in `Modules/AI/app/Actions` sono tutte `QueueableAction` ma vengono chiamate in modo sincrono:
- `ClassifyTicketAction` e `SuggestSolutionsAction`: prompt → `MakeAIRequestAction` (HTTP OpenAI chat/completions, `model` fisso `'gpt-4'`, timeout/retry da `AIConfigData`: 30 s × 3 tentativi con backoff 2/4 s) → decodifica JSON con default di fallback, cache 1 h / 30 min. **Nessun chiamante applicativo**: solo `BuildAIPromptAction`/`AIPromptTemplates` e i test.
- `SentimentAction`: analizzatore locale (Transformers se presente, altrimenti basic), usato solo dalla pagina Filament `AI/Filament/Pages/Completion.php:72`.
- `CompletionAction`: facade OpenAI, modello `gpt-3.5-turbo-instruct`, usata solo dalla pagina `Completion`.
- `GeneratePredictionsAction`: genera mercati di previsione (politica/sport), un dominio diverso da Fixcity, usata solo in `AIConfigData`/settings.
- `MakeAIRequestAction`: chiave da `config('ai.openai_api_key')` o `config('services.openai.api_key')`, ma **nessuna delle due chiavi esiste** (`Modules/AI/config/config.php`; nessun `openai` in `config/services.php`).

**Conclusione: l'AI non è collegata al flusso dei ticket.** Nessun file di Fixcity importa `Modules\AI`.

---

## 2. Rilievi

Scala di gravità: Critica / Alta / Media / Bassa. La confidenza è da 0 a 100.

### DB-01 — La colonna `citizen_rating`/`citizen_rated_at` non ha più una migrazione
- **Gravità:** Alta. **Tipo:** bug dimostrato (sulle installazioni nuove).
- **Evidenza:**
  - `Models/Ticket.php:199-200,241-242` (fillable e cast);
  - `Actions/SubmitCitizenTicketRatingAction.php`, `Filament/Widgets/CitizenRatingOverviewWidget.php`, `Filament/Resources/TicketResource/Tables/TicketsTable.php:62-69` (colonne sortable), `Filament/Exports/TicketExporter.php:47`;
  - nessun file in `Modules/*/database/migrations` contiene `citizen_rating`;
  - storia git: la migrazione `2026_05_29_120000_add_citizen_rating_to_tickets_table.php` è stata aggiunta in `9254b4b309` e **cancellata** in `2744926b59` ("docs: sprint 6 completed…", 2026-05-29);
  - `database/seeders/Demo/DemoTicketWriter.php:479-480` fa già un `hasColumn` difensivo, segno che il problema è noto.
- **Attuale:** un database creato da zero non ha le colonne. Il salvataggio del rating, il widget media/conteggio, l'ordinamento in tabella e l'export finiscono in `Unknown column`. Le installazioni migrate prima della cancellazione funzionano: gli ambienti divergono.
- **Atteso:** una migrazione idempotente versionata che aggiunge le due colonne.
- **Impatto:** funzione "valuta la risoluzione" rotta e dashboard admin in errore sui nuovi Comuni.
- **Correzione:** ripristinare la migrazione (forward-only, `tableUpdate` + `hasColumn`) con un nuovo timestamp.
- **Test:** feature test che fa `migrate:fresh` e poi `Schema::hasColumns('tickets', ['citizen_rating','citizen_rated_at'])`, più il test di `SubmitCitizenTicketRatingAction` su DB pulito.
- **Confidenza 90:** prova diretta nel codice e in git. Non escludo un dump SQL esterno al repo.

### DB-02 — `down()` ereditato cancella tabelle intere, anche nelle migrazioni "di allineamento"
- **Gravità:** Alta. **Tipo:** bug dimostrato (operativo).
- **Evidenza:**
  - `Modules/Xot/app/Database/Migrations/XotBaseMigration.php:271-274` (`down()` = `dropIfExists(getTable())`);
  - non lo ridefiniscono: `2026_10_22_000002_allow_tickets_without_legacy_status.php` (model Ticket), `2026_10_22_000003_align_ticket_hours_user_id_with_uuid_users.php` (TicketHour), le "create" duplicate `2026_04_29_110000`/`2026_06_10_140000_create_tickets_table.php`, `2026_09_26_151800_create_ticket_activities_table.php`, `2026_06_10_123000_create_profiles_table.php`;
  - alcune dichiarano "down() rimosso" (`2024_03_14_create_reports_table.php:11`) ma ereditano comunque il drop.
- **Attuale:** `migrate:rollback` dell'ultimo batch che contiene `2026_10_22_000002` **droppa `tickets`**, cioè perde tutte le segnalazioni. Un rollback di una "create" duplicata droppa una tabella creata da una migrazione precedente.
- **Atteso:** le migrazioni che alterano non toccano la tabella in down; le duplicate hanno `down(): void {}`; la regola forward-only va resa esplicita nel codice.
- **Impatto:** perdita di dati in produzione per un comando di routine.
- **Correzione:** `down()` vuoto (o che lancia un'eccezione) in tutte le migrazioni che non sono la prima "create" della tabella. Valutare un `down()` base no-op in `XotBaseMigration`, con opt-in esplicito al drop.
- **Test:** test che esegue `migrate`, `migrate:rollback --step=1` e verifica `Schema::hasTable('tickets')`.
- **Confidenza 90:** lettura diretta. Il drop è certo, il batch reale dipende dall'ambiente.

### DB-03 — Indici mancanti sulle colonne filtrate e ordinate
- **Gravità:** Media (Alta oltre ~50k ticket). **Tipo:** debito tecnico.
- **Evidenza:**
  - `tickets`: solo `slug` e `type_id` (`2026_06_10_140000_create_tickets_table.php:49,70`);
  - filtri e ordinamenti reali: `status` (`BuildPublicTicketsQueryAction`, `GetTicketKpiAggregateAction:35`, `GetTicketSlaMetricsAction:39-57`), `type` (`BuildTicketsGeoJsonAction:41`, `api/tickets/list.blade.php`), `owner_id` (area personale, `BuildPublicTicketsQueryAction` orWhere), `responsible_id` (filtro assegnazione in `TicketsTable.php:96-110`), `created_at`/`updated_at` (ordinamento in `ListPublicTicketsAction.php` `sorted()`), `code` (DB-04), `deleted_at`;
  - `ticket_activities.ticket_id` senza indice (`2023_01_01_000000_create_ticket_activities_table.php`: `foreignId` senza `constrained`, quindi senza indice; la `tableCreate` del 2026 viene saltata), letto a ogni dettaglio e timeline (`BuildTicketTimelineAction`) e in `NotifyTicketOwnerOfLifecycleEventAction::isFirstAssignment`;
  - stesso problema per `ticket_comments.ticket_id` e `ticket_relations.ticket_id`.
- **Attuale:** full scan per mappa, lista, KPI, timeline.
- **Atteso:** indici composti, per esempio (`status`,`created_at`), (`owner_id`,`created_at`), (`responsible_id`), (`type`,`status`), unique su `code`, e (`ticket_id`,`created_at`) su `ticket_activities`.
- **Correzione:** nuova migrazione con `ensureIndexes()` (già presente in `XotBaseMigration:347`, idempotente).
- **Test:** test di schema che verifica gli indici con `Schema::getIndexes`, più `EXPLAIN` sulle query principali in staging.
- **Confidenza 85:** schema letto. La dimensione reale dei dati non è nota.

### DB-04 — `code` di tracciamento senza vincolo unique né indice
- **Gravità:** Media. **Tipo:** debito tecnico / altamente probabile.
- **Evidenza:** `2026_06_10_140000_create_tickets_table.php:28` (`string('code')->nullable()`); `Actions/AllocateTicketCodeAction.php:22-24` (controlla e poi inserisce, ignora i soft-deleted perché lo scope esclude i trashed); `Actions/GetPublicTicketByCodeAction.php:51` (lookup `where('code')`).
- **Attuale:** ogni allocazione e ogni accesso da link di tracciamento fa un full scan. L'unicità è solo probabilistica (16 caratteri alfanumerici: collisione trascurabile) e non è garantita dal database. Il `code` è una capability: chi lo conosce vede il ticket.
- **Correzione:** unique index su `code`, retry su `UniqueConstraintViolationException`, `withTrashed()` nel controllo.
- **Test:** test sull'indice; test che due ticket con lo stesso code forzato falliscano.
- **Confidenza 85.**

### DB-05 — Doppia notifica al cittadino alla creazione
- **Gravità:** Media. **Tipo:** altamente probabile.
- **Evidenza:** `Actions/CreateTicketAction.php:42-43`:
  - `TicketCreatedEvent::dispatch` porta a `NotifyTicketReceivedAction::confirmToOwner` (`NotifyTicketReceivedAction.php:48-51`, `TicketLifecycleNotification` Received);
  - subito dopo `SendTicketReceiptAction` (`SendTicketReceiptAction.php:35,76`) invia `TicketStatusChangedNotification` con lo stesso stato pending;
  - entrambe hanno canale database e mail (se l'email è verificata);
  - il test `tests/Feature/Actions/TicketReceivedNotificationTest.php:55` conta per classe (`assertSentToTimes(..., TicketLifecycleNotification::class, 1)`), quindi non può vedere il doppione.
- **Attuale:** due mail e due righe in "Notifiche" per ogni segnalazione.
- **Atteso:** una sola ricevuta.
- **Correzione:** tenere un solo canale. La lifecycle Received ha la lingua del destinatario, la `SendTicketReceiptAction` no: rimuovere la seconda e far leggere `mailedTo()` dalla prima.
- **Test:** `Notification::fake()` e verifica che il proprietario riceva una sola notifica in totale (`Notification::sent($owner)` su tutte le classi).
- **Confidenza 75:** non ho potuto eseguire. Esistono altri chiamanti di `SendTicketReceiptAction::mailedTo` per la pagina di conferma, da preservare.

### DB-06 — La creazione del ticket non è atomica: foto, notifiche e idempotenza possono divergere
- **Gravità:** Media. **Tipo:** altamente probabile.
- **Evidenza:**
  - `CreateTicketWizardWidget.php:135-143`: prima `CreateTicketAction` (insert, evento, notifiche, flash), poi `saveRelationships()` per le foto, fuori da qualunque transazione;
  - con `QUEUE_CONNECTION=sync` (`.env.fixcity:36`) le notifiche "after commit" partono subito, perché non c'è una transazione aperta;
  - `SubmitTicketOnceAction.php:76-77` memorizza il risultato solo se `$create()` termina senza eccezioni.
- **Attuale:** se il salvataggio delle foto fallisce (MIME non accettato da `acceptsMimeTypes` a `Ticket.php:425-428`, disco pieno, errore di conversione) resta un ticket senza foto, il cittadino ha già ricevuto la ricevuta e il token non è memorizzato. Un nuovo "Invia" crea un **secondo ticket**. Anche `MediaAdded` e la pulizia EXIF (`StripTicketPhotoMetadataListener`) avvengono dopo la ricevuta.
- **Atteso:** insert, media e activity in una transazione; evento e notifiche dopo il commit (`DB::afterCommit` o evento `ShouldDispatchAfterCommit`).
- **Correzione:** spostare `saveRelationships()` dentro `CreateTicketAction` (callback) in `DB::connection('fixcity')->transaction(...)`, rendere `TicketCreatedEvent` `ShouldDispatchAfterCommit` e chiamare la ricevuta dal listener.
- **Test:** feature test con upload che lancia un'eccezione: nessun ticket persistito, nessuna notifica, il secondo invio crea esattamente un ticket.
- **Confidenza 70:** il flusso è letto. Il comportamento del componente `CameraPhotoUpload` in errore non è verificato a runtime. Media e ticket sono su connessioni diverse (`media` e `fixcity`): con lo stesso database fisico serve comunque la transazione su entrambe.

### DB-07 — Race condition nei cambi di stato (check-then-act senza lock)
- **Gravità:** Media. **Tipo:** altamente probabile (sotto concorrenza).
- **Evidenza:**
  - `Actions/ChangeStatus.php:31-42` legge `resolveTicketStatusValue()` e fa `assertCanTransitionTo` **fuori** dalla transazione e senza `lockForUpdate`; la transazione (`:51`) copre solo la scrittura;
  - stesso schema in `ApproveTicketAction`, `CloseTicketAction`, `RejectTicketAction`;
  - `AssignTicketAction` invece usa `lockForUpdate()`.
- **Attuale:** due operatori sullo stesso ticket (uno "risolvi", uno "sospendi"), oppure un doppio click, superano entrambi il controllo sullo stato vecchio. Vince l'ultima scrittura e il log riporta due transizioni con lo stesso `from`. Il workflow "SSoT" si può violare (per esempio resolved→on_hold).
- **Correzione:** dentro la transazione, `newQuery()->whereKey()->lockForUpdate()->firstOrFail()`, poi rileggere lo stato e validare. In alternativa un update condizionale `where('status', $current)` con verifica delle righe toccate.
- **Test:** test con due istanze del modello caricate prima del cambio: la seconda deve fallire con `DomainException`.
- **Confidenza 70.**

### DB-08 — Workflow incompleto o incoerente: scarto non terminale, stato OPEN legacy, bypass delle transizioni
- **Gravità:** Media. **Tipo:** funzionalità incompleta.
- **Evidenza:**
  - `Actions/RejectTicketAction.php:33-55`: lo scarto riporta o lascia il ticket in **PENDING** (anche da IN_REVIEW, transizione non presente in `allowedTransitions`), con visibilità AuthorOnly;
  - non esiste uno stato REJECTED: il ticket scartato resta nella coda "da gestire", conta come pending nei KPI (`GetTicketKpiAggregateAction`), si può riapprovare (`ApproveTicketAction`: pending→in_review) e il cittadino non può valutarlo;
  - `TicketStatusEnum::default()` restituisce `OPEN` (`Enums/TicketStatusEnum.php:72`) mentre `Ticket::boot` imposta `PENDING` (`Models/Ticket.php:337-341`);
  - `OPEN` non ha transizioni in ingresso ma ha `open→pending` (`:128`), quindi un ticket pubblico può tornare privato;
  - `GenerateTicketAction.php:25` e `TicketFactory.php:56` producono ancora `OPEN`.
- **Atteso:** stato terminale `REJECTED` (o `CLOSED` con motivo), transizioni esplicite, `default()` = `PENDING`, migrazione dei dati `open` verso uno stato canonico.
- **Test:** test di proprietà su `allowedTransitions` (ogni stato raggiungibile da PENDING, nessuna transizione da pubblico a privato), test di `RejectTicketAction` sullo stato finale.
- **Confidenza 85.**

### DB-09 — Lo stato si può scrivere senza audit né notifica (nessun guard sul modello)
- **Gravità:** Media. **Tipo:** altamente probabile.
- **Evidenza:**
  - `Filament/Resources/TicketResource/Schemas/TicketForm.php:261-262`: `status` e `priority` sono `Hidden(...)->dehydrated()` nello schema usato anche da `EditTicket` (`XotBaseResource::form()` → `TicketForm`); un `$wire.set('data.status','closed')` dall'edit admin salva lo stato senza `ticket_activities` né notifiche;
  - `Actions/Workflow/TransitionTicketStatusAction.php:36` fa `update(['status'=>...])` senza registrare attività (non usata, vedi DB-20);
  - la regola SSoT vive solo nelle Actions, non nel modello.
- **Attuale:** si può aggirare la timeline, che è un registro amministrativo.
- **Correzione:** togliere `status` e `priority` dallo schema di edit (o `->dehydrated(fn ($op) => $op === 'create')`) e aggiungere un `Ticket::updating` che rifiuti un cambio di `status` non marcato dalle Actions, oppure lo registri.
- **Test:** Livewire test su `EditTicket` che imposta `data.status` e verifica che lo stato non cambi o che venga creata l'attività.
- **Confidenza 60:** il comportamento di `Hidden` in edit l'ho dedotto, non provato. L'accesso richiede comunque il ruolo `fixcity::admin` (`BaseUser::canAccessPanel`).

### DB-10 — Il ticket creato da admin non ha `code` né evento: il cittadino non riceverà mai notifiche
- **Gravità:** Media. **Tipo:** altamente probabile.
- **Evidenza:**
  - `Filament/Resources/TicketResource/Pages/CreateTicket.php:19-21` usa solo `GetTicketFormDataForPersistAction` (`Actions/GetTicketFormDataForPersistAction.php:24-38`): nessuna `AllocateTicketCodeAction`, nessun `TicketCreatedEvent`;
  - tutte le notifiche escono se `code` è vuoto: `NotifyTicketSubscribersOfStatusChangeAction.php:46-53`, `NotifyTicketOwnerOfLifecycleEventAction.php` ("ticket has no public tracking code"), `SendTicketReceiptAction.php:72-74`.
- **Attuale:** per i ticket inseriti dallo sportello (per esempio da telefonata) non partono né ricevuta né aggiornamenti, e `owner_id` è l'operatore.
- **Correzione:** far passare `CreateTicket::handleRecordCreation` da `CreateTicketAction`; prevedere un campo "segnalante" per le pratiche da sportello.
- **Test:** Livewire test su `CreateTicket`: `code` non vuoto e `TicketCreatedEvent` dispatchato.
- **Confidenza 80.**

### DB-11 — KPI e SLA calcolati su `updated_at` e comprensivi dei ticket cancellati
- **Gravità:** Media. **Tipo:** bug dimostrato (logico).
- **Evidenza:**
  - `Actions/GetTicketSlaMetricsAction.php:39-41,54-57,77-85` usa il query builder grezzo (`$connection->table($table)`), quindi **nessun filtro `deleted_at`**;
  - il tempo di risoluzione è `TIMESTAMPDIFF(created_at, updated_at)`;
  - `updated_at` cambia con qualsiasi modifica successiva: valutazione del cittadino (`SubmitCitizenTicketRatingAction`), assegnazione, priorità, chiusura;
  - anche `ListPublicTicketsAction` (`resolved_last_12_months`) usa `updated_at`.
- **Attuale:** tempi medi gonfiati e conteggi che includono le segnalazioni eliminate.
- **Atteso:** l'istante di risoluzione dalla prima `ticket_activities` con `payload->to = resolved`, oppure una colonna `resolved_at` valorizzata da `ChangeStatus`; escludere i soft-deleted.
- **Test:** ticket risolto, poi valutato 10 giorni dopo: la media non cambia; ticket soft-deleted escluso.
- **Confidenza 85.**

### DB-12 — `/api/tickets/geojson` non è paginata, non ha cache e restituisce tutte le colonne
- **Gravità:** Media (Alta in crescita). **Tipo:** debito tecnico (performance).
- **Evidenza:**
  - `Modules/Fixcity/resources/views/pages/api/tickets/geojson.blade.php:16-23` (Folio, pubblica);
  - `Actions/BuildTicketsGeoJsonAction.php:43` (`$scoped->latest()->get()` senza `select`, senza limite, carica `content` longText e tutte le colonne);
  - nessuna `Cache::` sul percorso;
  - consumatori: blocchi tema (`Themes/Sixteen/resources/views/components/blocks/ticket-list/design-comuni.blade.php:26`, `hero/split.blade.php:5`, `ticket/map-preview.blade.php:10`) e viste mobile che scaricano l'intera FeatureCollection per mostrare **un** ticket (`Themes/Sixteen/resources/views/mobile/tickets/show.blade.php:5,10`);
  - `map-lit` di default legge invece il file statico `/data/tickets.json` (DB-27).
- **Attuale:** O(n) in memoria e in rete a ogni apertura della mappa, senza indici (DB-03).
- **Correzione:** `select` delle sole colonne della feature, limite o bbox (`?bbox=` su `latitude/longitude` indicizzate), `Cache::remember` con chiave per filtri e invalidazione su ticket salvato, ETag; endpoint di dettaglio per le viste mobile.
- **Test:** test con 1000 ticket che misura query e memoria; verifica `Content-Length` e limite.
- **Confidenza 85.**

### DB-13 — N+1 sui media in `/api/tickets/list`
- **Gravità:** Bassa. **Tipo:** altamente probabile.
- **Evidenza:** `Modules/Fixcity/resources/views/pages/api/tickets/list.blade.php:32` (`->get()` senza `with('media')`), `:38-40` (`getMedia()` per ogni riga, fino a 50 righe, quindi fino a 50 query in più).
- **Correzione:** `->with('media')`.
- **Test:** `DB::enableQueryLog()` e conteggio costante al variare di `limit`.
- **Confidenza 85.**
- **Nota:** la tabella admin (`TicketsTable.php:60-61`, `owner.name`/`assignee.name`) dovrebbe essere eager-loaded automaticamente da Filament. Non verificato a runtime.

### DB-14 — Generazione dello slug lineare e soggetta a race
- **Gravità:** Bassa/Media. **Tipo:** altamente probabile.
- **Evidenza:** `Models/Ticket.php:320-328` (HasSlug da `name`, max 50, separatore `_`), con `slug` unique NOT NULL (`2026_06_10_140000_create_tickets_table.php:70`).
- **Attuale:** spatie/laravel-sluggable fa una query per ogni suffisso (`buca_stradale`, `buca_stradale_1`, …). I titoli dei cittadini si ripetono molto ("Buca", "Lampione spento"), quindi N query per insert. Due invii concorrenti con lo stesso titolo calcolano lo stesso suffisso: violazione unique e errore 500.
- **Correzione:** slug con suffisso univoco (id o code) dopo l'insert, oppure rimuovere il vincolo (lo slug non sembra usato negli URL: `detail_url` usa l'id).
- **Test:** 200 ticket con lo stesso titolo, conteggio delle query dell'ultimo insert.
- **Confidenza 65:** il comportamento di sluggable l'ho ricostruito dalla conoscenza della libreria, il vendor non è disponibile.

### DB-15 — Multi-comune non implementato a livello dati
- **Gravità:** Alta (se il prodotto prevede più Comuni su un'installazione), altrimenti Media. **Tipo:** mancante confermata.
- **Evidenza:**
  - nessuna colonna `tenant`/`comune` su `tickets` (sezione 1.1);
  - nessun `addGlobalScope` in Fixcity o Tenant;
  - `ApplyTenancyToPanelAction` non è richiamata da nessuno;
  - `NotifyTicketReceivedAction.php:23` (scoping "DA DECIDERE");
  - `TicketPolicy::viewAny/view` (`Policies/TicketPolicy.php:13-28`): ogni operatore PA vede tutto;
  - `alertStaff` avvisa **tutti** gli operatori;
  - nessun controllo che le coordinate ricadano nel territorio dell'ente: la regola `Rules/FilterCoordinatesInRadius.php` esiste ma non è usata;
  - il tenant è risolto dall'hostname (`GetTenantNameAction`) e il database dall'`.env`.
- **Attuale:** l'isolamento funziona solo con un deployment, un `.env` e un database per Comune. Il seed demo mescola molti Comuni nello stesso database.
- **Atteso:** una scelta documentata, cioè silo (un DB per ente, con provisioning e migrazioni per tenant) oppure pool (colonna `ente_id` + scope globale + tenancy Filament + policy). Più il geofence sul confine comunale.
- **Test:** con la soluzione pool, test che un operatore del Comune A non veda o modifichi i ticket di B (lista, view, geojson, export, notifiche).
- **Confidenza 85.**

### DB-16 — Dipendenza totale dal worker senza configurazione né monitoraggio; scheduler senza job di dominio
- **Gravità:** Media. **Tipo:** debito tecnico / funzionalità incompleta.
- **Evidenza:**
  - notifiche `ShouldQueueAfterCommit` (`Notifications/*.php`), compreso il canale `database`;
  - conversioni media e export Filament in coda;
  - `.env.production.example:27` = redis, ma nel repo non c'è nessuna configurazione di worker o scheduler;
  - `routes/console.php` contiene solo retention;
  - `Models/TimelineStep.php` `needsReminder()`/`followup_at` non vengono mai schedulati;
  - nessuna auto-chiusura di `RESOLVED`, anche se `TicketStatusEnum.php` documenta "CLOSED … o il termine e' scaduto".
- **Se il worker non gira (redis/database):** il cittadino non riceve nulla, nemmeno nell'area personale (anche il canale database è accodato), gli operatori non sono avvisati, le miniature non vengono generate, gli export restano "in corso". Con `sync` (ambiente fixcity) tutto gira dentro la richiesta HTTP: una SMTP lenta rallenta l'azione dell'operatore. Gli errori vengono loggati e assorbiti (`SendTicketNotificationAction.php:32-41`).
- **Correzione:**
  - unit systemd o supervisor e cron `schedule:run` versionati;
  - `queue:monitor` e un health check sulla tabella `failed_jobs`;
  - canale `database` sincrono (`viaQueues`/`viaConnections`), mail in coda;
  - job schedulato di auto-chiusura dei resolved dopo N giorni e reminder SLA.
- **Test:** test di schedule (`Schedule::events()` contiene gli eventi attesi); test che la notifica database esista anche con `Queue::fake()`.
- **Confidenza 75.**

### DB-17 — Modulo AI scollegato, non configurato e con rischi su PII e latenza
- **Gravità:** Media. **Tipo:** funzionalità incompleta.
- **Evidenza (collegamento):** `ClassifyTicketAction`/`SuggestSolutionsAction` hanno come unici riferimenti `AI/app/Actions/Prompt/*` e i test; nessun file Fixcity importa `Modules\AI`.
- **Evidenza (configurazione e codice):**
  - `MakeAIRequestAction.php:28`: chiavi di configurazione inesistenti, quindi `Bearer ` vuoto e 401;
  - `:49`: `'model' => 'gpt-4'` fisso, ignora `AIConfigData::$chat_model = 'gpt-4o-mini'` (`Datas/AIConfigData.php:34`), con costo circa 10–20 volte maggiore;
  - `:85`: logga `response->body()` più titolo e descrizione del ticket (dati personali dei cittadini nei log);
  - `:98`: `sleep()` bloccante; caso peggiore circa 30×3 + 2 + 4 = 96 s dentro una richiesta web;
  - `CompletionAction.php` usa `gpt-3.5-turbo-instruct` (endpoint completions legacy);
  - `GeneratePredictionsAction.php` riguarda un altro dominio (prediction market).
- **Fallback:** default statici se il JSON è vuoto (`ClassifyTicketAction.php` DEFAULTS); un'eccezione dopo i retry non viene messa in cache.
- **Correzione:**
  - decidere se integrare (job accodato `ClassifyTicketJob` su `TicketCreatedEvent`, che scrive un suggerimento visibile solo all'operatore, mai un cambio automatico) oppure disattivare il modulo;
  - chiave in `config/services.php`;
  - modello da config;
  - niente body o PII nei log;
  - timeout totale ≤ 10 s e nessun retry in richieste sincrone.
- **Test:** `Http::fake()` che verifica modello da config, timeout e log senza contenuto del ticket.
- **Confidenza 90.**

### DB-18 — Deduplicazione, protocollo informatico, App IO e pagoPA assenti
- **Gravità:** Media (per un prodotto PA). **Tipo:** mancante confermata.
- **Evidenza:**
  - nessuna occorrenza di `protocollo`, `pagopa`, App IO o `dedup`/`duplicat` in `Modules/Fixcity/app`, `Notify/app`, `User/app`;
  - il modulo Geo ha scope Haversine (`Geo/app/Models/Address.php:359`, `Location.php:120`) mai usati per i ticket;
  - `TicketRelation` (che potrebbe esprimere "duplicato di") non ha scrittori;
  - export presenti: Filament `TicketExporter` (CSV/XLSX, con sanitizzazione anti formula-injection in `Actions/Export/SanitizeSpreadsheetCellAction.php`) e GeoJSON statico (`GenerateTicketsJsonAction`);
  - nessun open data (DCAT/CSV pubblico).
- **Correzione:**
  - in fase di invio, suggerire i ticket aperti dello stesso tipo entro ~50 m (query bbox + Haversine su lat/lng indicizzate);
  - relazione `duplicate_of` gestita dall'operatore, con notifica ai follower;
  - adapter di protocollazione (numero e data di protocollo su `tickets`);
  - canale App IO nel modulo Notify.
- **Test:** test dell'Action di prossimità con fixture geografiche.
- **Confidenza 85:** basata su ricerca testuale.

### DB-19 — Foto delle segnalazioni non pubbliche servite dal disco `public` con percorso enumerabile
- **Gravità:** Media. **Tipo:** ipotesi / altamente probabile.
- **Evidenza:** `config/local/fixcity/media-library.php:34` (`disk_name` = `public`), `:99` (`DefaultPathGenerator`, cioè `/storage/{media_id}/{file_name}`); `config/local/fixcity/filesystems.php:51-56` (visibility public); gli ID media sono sequenziali.
- **Attuale:** le immagini di ticket PENDING o scartati (non visibili in front office per `TicketStatusEnum::canNotViewByAll`) restano raggiungibili da URL diretto, a chi indovina id e nome file. Se il webserver elenca le directory basta l'id. Le foto possono contenere volti o targhe (l'EXIF viene rimosso, il contenuto no).
- **Correzione:** disco privato più route firmata o con policy, oppure un path generator con UUID.
- **Test:** HTTP test: GET su l'URL di un media di un ticket pending, non autenticato, restituisce 403/404.
- **Confidenza 55:** la configurazione del webserver e del symlink `storage` in produzione non è verificabile.

### DB-20 — Codice morto, alcuni pezzi pericolosi se riattivati
- **Gravità:** Bassa (Media per la policy). **Tipo:** debito tecnico.
- **Evidenza:**
  - `Models/Policies/TicketPolicy.php`: ogni metodo `return true`. Non è registrata (`FixcityServiceProvider.php:40` registra `Policies\TicketPolicy`), ma il nome coincide con quello che l'auto-discovery di Laravel cercherebbe per primo (`Models\Policies`). Se la riga `Gate::policy` sparisse, tutto diventerebbe permesso.
  - `Filament/Widgets/CreateTicketWidget.php:224-246` `create()`: `Ticket::create($data)` con `status` Hidden scrivibile dal client, senza `owner_id` (NOT NULL), senza `code`, senza autenticazione. `submit()` (`:185-222`) non salva le foto e passa `$this->data` grezzo (mass assignment su `code`, `responsible_id`, `citizen_rating` in `$fillable`). Il widget non è montato da nessun JSON CMS né da nessuna vista (la dashboard `Fixcity/resources/views/filament/pages/dashboard.blade.php` è vuota), ma viene scoperto da `discoverWidgets` (`XotBasePanelProvider.php:143`).
  - `Listeners/TicketCreatedListener.php` non è registrato (`EventServiceProvider.php:23`).
  - `QueryBuilders/TicketQueryBuilder.php:42-60` filtra su `status_id`/`priority_id`/`type_id` legacy e non è collegato.
  - `Actions/Workflow/TransitionTicketStatusAction.php` non è usata.
  - `Models/Report.php` estende `BaseModel` con SoftDeletes, ma `reports` non ha `deleted_at`: qualsiasi query fallirebbe. Non è usato.
  - `Models/Category.php:91`: `hasMany(Ticket::class, 'category_id')`, colonna inesistente (lo dicono anche `database/seeders/CategorySeeder.php:20`).
  - `type_id` integer castato a `TicketTypeEnum` string (`Ticket.php:240`).
  - `database/seeders/DemoTicketsSeeder.php:36-38,46,56`: scrive `type_id` = `'street_furniture'`/`'animal'`/`'road'` (non sono case dell'enum, quindi ValueError) e stringhe di stato in colonne bigint (`old_status_id`/`new_status_id`). È rotto e non richiamato da nessun seeder.
  - `Ticket::casts()` (`:232`) non fonde `parent::casts()` (`BaseModel.php:32-43`).
- **Correzione:** eliminare i file morti (forward-only), sostituire la policy permissiva, rimuovere `CreateTicketWidget` o farlo passare da `CreateTicketAction` più `form->getState()`.
- **Test:** architecture test (Pest `arch()`): nessuna classe `*Policy` sotto `Models\Policies`; nessun `Ticket::create` fuori da `CreateTicketAction`.
- **Confidenza 80.**

### DB-21 — Migrazioni duplicate, legacy e non portabili
- **Gravità:** Bassa/Media. **Tipo:** debito tecnico.
- **Evidenza (duplicati e residui):**
  - tre `create_tickets_table` (`2023_01_01_000005`, `2026_04_29_110000`, `2026_06_10_140000`), tre `create_profiles_table` (`2021_01_01_000009`, `2026_06_05_090000`, `2026_06_10_123000`), due `create_categories_table`, due `create_ticket_activities_table`, `create`+`repair_ticket_subscribers`;
  - tre file `.php.old` nella cartella delle migrazioni e 15 file `.merged` in `database/migrations/_bak`;
  - nomi senza orario (`2024_03_14_create_*`).
- **Evidenza (portabilità):** `2026_10_22_000002_*.php` e `2026_10_22_000003_*.php` usano `ALTER TABLE … MODIFY` (solo MySQL/MariaDB) per qualsiasi driver diverso da sqlite (pgsql si rompe); il nome tabella `ticket_hours` è scritto a mano.
- **Evidenza (colonne legacy):** colonne da gestionale di progetto ancora presenti e in `$fillable` (`epic_id`, `sprint_id`, `project_id`, `estimation`, `order`, `ticket_prefix`, `status_id`, `priority_id`, `type_id`).
- **Evidenza (rischio):** nella prima migrazione, `slug` unique NOT NULL aggiunto a una tabella esistente fallirebbe con righe presenti (`2023_01_01_000005_create_tickets_table.php:62-64`).
- **Correzione:** consolidare in una migrazione "squash" documentata (o schema dump), nuova migrazione di drop delle colonne legacy dopo verifica dei dati, `->change()` portabile.
- **Test:** `migrate:fresh` su MariaDB e su sqlite in CI.
- **Confidenza 90.**

### DB-22 — Coordinate in doppio formato, senza indice spaziale né validazione territoriale
- **Gravità:** Bassa. **Tipo:** debito tecnico.
- **Evidenza:** `decimal(20,18)` (`2026_06_10_140000_create_tickets_table.php:52,55`): massimo 99,99…, va bene per l'Italia ma `longitude` non arriva a ±180 e 18 decimali sono inutili. Doppia fonte: `location` json e `latitude/longitude`, sincronizzate in `Ticket::boot` saving (`:343-354`); `BuildTicketsGeoJsonAction` legge prima il JSON. Nessun indice.
- **Correzione:** `decimal(10,7)`, indice (`latitude`,`longitude`) o colonna `POINT` con indice spaziale, una sola fonte di verità.
- **Test:** test di normalizzazione con longitudine 120 e con `location` che contraddice lat/lng.
- **Confidenza 80.**

### DB-23 — Notifiche ai follower non localizzate; query N per recipient
- **Gravità:** Bassa. **Tipo:** altamente probabile.
- **Evidenza:** `Actions/NotifyTicketSubscribersOfStatusChangeAction.php:84-89` chiama direttamente `Notification::send`, senza `->locale()`, a differenza di `SendTicketNotificationAction.php:30`, che è il percorso documentato (DCP-284) per la lingua del destinatario. Con il driver sync le preferenze email (`GetFixcityEmailNotificationPreferenceAction` in `via()`) generano query per ogni destinatario.
- **Correzione:** passare da `SendTicketNotificationAction`.
- **Test:** follower con locale `en` e operatore in `it`: la mail è in inglese.
- **Confidenza 70.**

### DB-24 — Nessuna integrità referenziale; sottoscrizioni su un altro database
- **Gravità:** Bassa/Media. **Tipo:** debito tecnico.
- **Evidenza:**
  - nessuna FK su `tickets`, `ticket_activities`, `ticket_comments`, `ticket_relations` (sezione 1.1);
  - `TicketSubscriber` su connessione `user` (`Models/TicketSubscriber.php`), quindi nessuna FK possibile né join, e la transazione di `SetTicketSubscriptionAction` non copre `fixcity`;
  - la cancellazione di un ticket non tocca né sottoscrizioni né attività;
  - `SetTicketSubscriptionAction` fa read-then-create: con due richieste concorrenti la unique (`ticket_subscribers_ticket_user_unique`) produce un 500.
- **Correzione:** FK dove la connessione coincide (`ticket_activities.ticket_id` → `tickets.id`), spostare `ticket_subscribers` sul database `fixcity`, `firstOrCreate` con catch della violazione unique.
- **Test:** due follow concorrenti non danno errore; la cancellazione di un ticket pulisce le sottoscrizioni.
- **Confidenza 75.**

### DB-25 — Dichiarazioni dei moduli incoerenti
- **Gravità:** Bassa. **Tipo:** debito tecnico (ipotesi su quale file prevale).
- **Evidenza:**
  - `laravel/modules_statuses.json` dichiara `Setting`, `Chart`, `Ticket`, che non esistono in `Modules/`, e non dichiara `Event`, che esiste (`Modules/Event/module.json`: `"active": 0`, `providers: []`, una migrazione con nome non standard `20260101000000_create_events_table.php`, nessun riferimento esterno);
  - `config/local/fixcity/modules.php:9` punta lo statuses-file del tenant a `config/local/fixcity/modules_statuses.json`, che contiene il segnaposto `"<nome progetto>"`, una dozzina di moduli fantasma (LU, Limesurvey, BarberShop, RealEstate, Booking, Food, Forum, Predict, Shop…) e non contiene `Mobile`;
  - `config/local/fixcity/morph_map.php:14,26` contiene `Modules\<nome progetto>\…`; le voci inesistenti vengono scartate in silenzio da `TenantServiceProvider::buildMorphMap()` (`@class_exists`).
- **Correzione:** un solo statuses file allineato a `Modules/`, eliminare i segnaposto, decidere se tenere `Event` (attivarlo o rimuoverlo).
- **Test:** test che confronta le chiavi di `modules_statuses.json` con le directory `Modules/*` e verifica che non ci siano `<` nelle config.
- **Confidenza 80:** quale dei due file viene letto per primo da nwidart non l'ho verificato a runtime.

### DB-26 — Timeline duplicata (`ticket_activities` vs `timeline_steps`)
- **Gravità:** Bassa. **Tipo:** debito tecnico.
- **Evidenza:** `Models/TimelineStep.php` (con `needsReminder`, `canClose`) è usata solo da `Filament/Widgets/TimelineStepper.php`; la timeline pubblica e le notifiche usano `ticket_activities` (`BuildTicketTimelineAction`).
- **Correzione:** unificare su `ticket_activities` (passi interni = `visibility=internal`) o documentare il ruolo di `timeline_steps`.
- **Confidenza 75.**

### DB-27 — GeoJSON statico versionato e non aggiornato
- **Gravità:** Bassa. **Tipo:** debito tecnico.
- **Evidenza:**
  - `public_html/data/tickets.json` è tracciato in git (500 feature, `generated_at` 2026-10-26, dati demo);
  - viene rigenerato solo a mano (`ListTickets.php` azione `export_map_json`, `GenerateTicketsJsonAction`) o dai seeder;
  - `Themes/Sixteen/resources/views/components/sections/map-lit.blade.php:3` lo usa come sorgente di default;
  - le modifiche di stato e le cancellazioni (per esempio per privacy) non lo aggiornano.
- **Correzione:** usare sempre l'API (con cache, DB-12), oppure rigenerare il file su evento o schedule e toglierlo da git.
- **Confidenza 75.**

### DB-28 — Stub e incompleti negli altri moduli
- **Gravità:** Bassa. **Tipo:** funzionalità incompleta.
- **Evidenza:**
  - Notify: `Notifications/Channels/TelegramChannel.php:26,34` (TODO, lancia eccezione), `GenericNotification.php:113` (Twilio TODO), `WhatsAppNotification.php:66`, `Filament/Clusters/Test/Pages/SendFirebasePushNotificationPage.php:112`;
  - file `.php-cs-fixer.dist.php` dentro `app/Actions` (`Geo/app/Actions/`, `Notify/app/Actions/SMS/`);
  - `Tenant/app/Models/TestSushiModel.php` nel codice di produzione;
  - file di backup (`.backup`, `.old`, `.bak`, `.merged`, `.LARAXOT_CORRECT`): Fixcity 19, Tenant 42, AI 20, Geo 59;
  - `TicketForm.php.backup` accanto allo schema attivo.
  - Fixcity, Tenant, AI e Geo non hanno TODO/FIXME reali; le occorrenze in Fixcity sono pattern regex in `AssessPrivacyPolicyAction.php:45` e `AssessAccessibilityStatementAction.php:40`.
- **Correzione:** pulizia forward-only; nascondere i canali Notify non implementati.
- **Confidenza 85.**

---

## 3. Riepilogo per priorità

1. **Subito:** DB-01 (migrazione rating mancante), DB-02 (rollback distruttivo).
2. **Prima del go-live su più Comuni:** DB-15 (multi-comune), DB-16 (worker e scheduler), DB-19 (foto pubbliche), DB-10 (ticket admin senza code).
3. **Qualità del workflow:** DB-05, DB-06, DB-07, DB-08, DB-09, DB-11.
4. **Performance:** DB-03, DB-04, DB-12, DB-13, DB-14.
5. **Pulizia:** DB-17, DB-20 – DB-28.

## 4. Cosa non ho potuto verificare

- Nessuna esecuzione: niente `artisan migrate`/`migrate:status`, PHPStan o Pest. Lo schema reale dei database di produzione e di staging (colonne aggiunte a mano, `citizen_rating` presente o no, indici creati fuori dalle migrazioni) non è noto.
- Il comportamento esatto delle librerie vendor l'ho dedotto dalla conoscenza dei pacchetti, non dal sorgente: Filament 5 (eager loading dei campi relazione, `Hidden` in edit, discovery dei widget), spatie/laravel-sluggable, spatie/laravel-medialibrary, nwidart (quale `modules_statuses.json` vince), Livewire (chiamabilità dei metodi di componenti non montati).
- Configurazione del webserver (listing di `/storage`, symlink), `.env` di produzione reale, presenza di worker o cron sui server.
- Volumi di dati reali, quindi l'impatto quantitativo di DB-03/DB-12/DB-14.
- Il componente `CameraPhotoUpload` e il percorso di errore degli upload (DB-06) non sono stati eseguiti.
- Rate limiting sulla creazione dei ticket e sulle API Folio: non analizzato in dettaglio.
- Contenuto completo dei moduli Notify e Geo oltre ai punti citati: verificati solo TODO, stub e il collegamento con Fixcity.
