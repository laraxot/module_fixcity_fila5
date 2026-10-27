---
title: "Analisi di progetto A: Fixcity e Mobile"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "analisi di progetto a: fixcity e mobile audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# Analisi lacune ambito A: Modules/Fixcity e Modules/Mobile

Data analisi: 2026-10-26. Analista in sola lettura. Nessun file del repository e' stato scritto, modificato o cancellato. Non sono stati eseguiti pest, phpstan, artisan, composer, npm, git commit. Sono stati usati solo Read, grep, ls, find, wc e git in lettura.

Convenzioni: VERIFICATO = letto o eseguito da me in questa analisi. RIPORTATO = affermato da una fonte (story, doc) che non ho ricontrollato. "non determinato" = non ho prove sufficienti. I documenti "remaining work / completion / backlog" del 2026-10-26 sono trattati come ipotesi.

Nota sul working tree: al momento dell'analisi ci sono modifiche non committate di un'altra sessione (BuildSitemapAction spostata dal modulo Fixcity al modulo Seo, widget sitemap). Non sono nel mio perimetro e non le ho valutate.

---

## 1. Perimetro e ownership

### 1.1 Fixcity (laravel/Modules/Fixcity)

- Identita (VERIFICATO, `docs/README.md` e `module.json`): modulo nwidart `Fixcity`, namespace `Modules\Fixcity`, repo git separato, provider `FixcityServiceProvider` e `Providers/Filament/AdminPanelProvider`.
- Possiede: segnalazioni (ticket), ciclo di vita e macchina a stati (`app/Enums/TicketStatusEnum.php`), wizard di creazione (`app/Filament/Widgets/CreateTicketWizardWidget.php` + `TicketForm`), dettaglio pubblico (`BuildPublicTicketDetailAction`), tracciamento per codice (`GetPublicTicketByCodeAction`), seguire una segnalazione (`FollowTicketAction`), back office PA (`TicketResource`, `ViewTicket`, `ListTickets`), notifiche di dominio, valutazione cittadino (`SubmitCitizenTicketRatingAction`), SLA/KPI, ticket_hours, geocoding, FAQ, matrici di conformita in `docs/compliance`.
- Non possiede: mappe e picker (Geo), layout e blocchi FO (tema Sixteen), motore pagine (Cms), identita e permessi (User), tenancy (Tenant), consensi e diritti dell'interessato (Gdpr), commenti/valutazioni/media (Comment, Rating, Media).
- Dimensioni misurate (VERIFICATO con find/wc, differiscono dal README del modulo): 75 file PHP in `app/Actions` (68 di primo livello; il README dice 64), 109 voci in `config/database/content/pages` (88 `tests.*`; il README dice 105), 143 file PHP di test (il README dice 128), 111 `.md` nella radice di `docs/` (il README dice 99), 785 `.md` in tutto `docs/`, 63 `.lock` in `docs/`, almeno 17 `.lock` in app/tests/config/migrations.
- Dipendenze reali (VERIFICATO con grep degli `use`): Xot 117 file, User 26, Comment 9, Rating 6, Media 4, Geo 3, Seo 2, Tenant 1, Cms 1, Activity 1. `composer.json` del modulo ha `require` vuoto e `module.json` non ha `requires` (vedi A-32).
- Connessioni (VERIFICATO): dati ticket sulla connessione `fixcity` (db `fixcity_data`), utenti sulla connessione `user` (`fixcity_user`) con ID UUID `varchar(36)`; nessuna FK tra `tickets.owner_id` e utenti.
- Story: 73 file in `docs/stories`, 99 in `docs/bmad/stories` e 68 in `docs/bmad/stories/_from-bmad-output` (conteggi separati, sovrapposizione non determinata).

### 1.2 Mobile (laravel/Modules/Mobile)

- Dichiarato (VERIFICATO, README del modulo): host NativePHP "agnostico", senza route ne seeder verticali.
- Reale (VERIFICATO): contiene codice di un dominio ristorante (cameriere, ordini, KDS), schermate native di Fixcity con dati demo, route file vuoti, un APK debug (`laravel/nativephp/android/app/build/outputs/apk/debug/app-debug.apk`) e `nativephp/mobile ^4.5` (installato 4.5.2, `composer.lock:8133`).
- `module.json` richiede fixcity, geo, user (VERIFICATO). Story: 12 file (il task ne indicava 11).
- PRODUCT.md (VERIFICATO) posiziona il prodotto come piattaforma web: l'app nativa non e' citata.

### 1.3 Decisioni umane (registro DEC)

`docs/stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md` ha DEC-01..51 (VERIFICATO, aggiornato 2026-10-15). Mancano voci per DCP-48, DCP-156, DCP-157, DCP-159, DCP-160, DCP-171 e per la strategia mobile. DEC-35 e DEC-46 sono stantii (vedi sezione 4). Nuove voci proposte in sezione 6.

---

## 2. Stato reale con evidenze

### 2.1 Cosa funziona davvero (VERIFICATO leggendo il codice, non eseguendolo)

| Area | Evidenza |
|---|---|
| Creazione server-side sicura nei campi critici | `CreateTicketWizardWidget.php:~85-96` impone `owner_id`, `status=PENDING`, `priority=LOW` lato server; `Ticket::boot()` imposta PENDING in `creating`. |
| Dettaglio pubblico con allow-list | `BuildPublicTicketDetailAction` espone un insieme esplicito di campi; test presenti sulla manomissione. |
| Freno al tracciamento per codice | `GetPublicTicketByCodeAction` MAX_MISSES=10 in 60 s; `GetPublicTicketByCodeThrottleTest` esiste; pagine tracking con `throttle:10,1`. |
| Follow | `web.php:37-41` ha `['auth','throttle:30,1']`; `TicketSubscriber::ticket()` usa `ticket_id` (`TicketSubscriber.php:70`). |
| Cancellazione solo admin | `TicketPolicy::delete` admin-only + `TicketDeletionAuthorizationTest`. |
| Notifica di cambio stato | `TicketActivityObserver::created` -> `NotifyTicketSubscribersOfStatusChangeAction` -> `TicketStatusChangedNotification` (coda dopo commit; mail solo con email verificata e preferenza); `NotificationRetryPolicyTest` esiste. |
| Valutazione cittadino | `SubmitCitizenTicketRatingAction` esiste. |
| Migrazione ticket_hours UUID | `2026_10_22_000003_align_ticket_hours_user_id_with_uuid_users.php` fa `MODIFY user_id VARCHAR(36)`. |
| Pagine 404/429 a tema, header di sicurezza | `ThemedNotFoundPageTest`, `SecurityHeadersTest` esistono (RIPORTATO: esito non rieseguito). |

### 2.2 Cosa e' a meta o assente (dettaglio nella tabella lacune)

- Ciclo di vita: la macchina a stati esiste ma ha una scorciatoia (Reject), nessun REOPENED, nessuna chiusura automatica, attore perso per utenti UUID in due Action. La notifica al cittadino copre solo `status_change` pubblico.
- Privacy: wizard bloccato finche l'informativa non e' pubblicata; informativa locale di 714 byte; il consenso non viene salvato; nessuna retention, nessuna cancellazione.
- Back office: un solo Resource (`TicketResource`), nessuna dashboard operativa (pagina vuota), nessun seed di ruoli per la produzione.
- API: `routes/api.php` e `routes/api_ticket.php` sono stub vuoti (VERIFICATO); le API sono pagine Folio in `pages/api/*` senza throttle, CORS, versioning o OpenAPI.
- Mobile: nessuna API mobile, schermate native con dati demo e stato simulato.

### 2.3 Test (VERIFICATO per elenco statico, NON eseguiti)

- 143 file PHP, circa 368 righe `it()/test()`. Ripartizione: Feature/Actions 12, Feature/Pages 43, Feature/Filament 12, Unit 31 piu sottocartelle, Playwright 8.
- Skip noti: i tre test di publication gate (`AccessibilityDeclarationPublicationGateTest`, `LegalNotesPublicationGateTest`, `PrivacyPolicyPublicationGateTest`, con `->skip(`), `TicketReviewSummaryStepViewTest.php:288` (condizionato da env), seeder test con `markTestSkipped`, `SeoLinksTest.php:93`.
- `tests/Feature/Filament/TicketResourceTest.php` esiste solo come `.lock` (nessun file di test).
- Zero file di test per: ChangeStatus, Approve, Reject, Resolve, Close, Transition, ChangeTicketPriority, RecordTicketCommunication, FollowTicketAction, BuildTicketsGeoJsonAction. AssignTicketAction e' coperta solo indirettamente da un test di notifica.
- Ultima suite completa riportata: 2026-10-14, 27 rossi, 1 todo, 13 skip, 1131 passati (RIPORTATO da DCP-103). Lo stato attuale e' non determinato.

### 2.4 Story: verifica a campione di 14 voci "done/completed" (VERIFICATO)

| Story | Esito della verifica |
|---|---|
| STORY-002 (cambio stato) | Parziale: l'Action esiste ma l'attore e' perso per utenti UUID (A-02). |
| STORY-003 (notifiche) | Contraddetta: cita `TicketAssignedEvent`, `StatusChangedEvent`, `CommentCreatedEvent`, `NotificationService`; nessuno esiste in `app/`. Notifica alla creazione non presente. |
| STORY-004 (dashboard KPI) | Contraddetta: `resources/views/filament/pages/dashboard.blade.php` contiene solo `<x-filament::page>`; i widget KPI sono montati solo in `ListTickets::getHeaderWidgets()` (righe 30-35). |
| STORY-010 (impostazioni /admin) | Non confermata nel modulo: non ho trovato in Fixcity le pagine `/admin/settings`, `/admin/modules`, `/admin/themes` citate. Non determinato altrove. |
| STORY-509 (follower) | Confermata (`TicketSubscriber.php:70`). |
| STORY-018 (rating) | Confermata (`SubmitCitizenTicketRatingAction`). |
| STORY-013 (preferenze notifica) | Confermata. |
| STORY-FIXCITY-NOTIFICATION-RETRY | Confermata (`NotificationRetryPolicyTest`). |
| STORY-FIXCITY-TICKET-DELETE-RBAC | Confermata (policy e test). |
| dcp-149 (throttle tracciamento) | Confermata. |
| dcp-151 (404/429 a tema) | Test presenti. |
| dcp-124 (redirectIntended) | Test presenti. |
| dcp-182 (anteprima foto riepilogo) | Test presenti. |
| dcp-210 (ticket_hours UUID) | Migrazione confermata, ma la funzione non ha UI (A-21). |
| ticketresource-final-override-removal | Confermata (`TicketResource.php` 27 righe). |

Esito: 9 su 14 confermate senza riserve, 1 parziale, 3 contraddette o non confermate, 1 confermata solo per la parte dati.

### 2.5 Story aperte: stato reale (VERIFICATO per front matter, stato effettivo verificato solo dove indicato)

- `docs/stories`: in-progress/in_progress 24 (tra cui 33, 35, 43, 44, 46, 47, 49, 54, 56, 130, 139, 154, 162, 177, 181, 241, 272, geocode-ticket-address-job, STORY-498, STORY-502); ready-for-dev 6 (42, 103, 202, 205, 208, 211); needs-decision 6 (48, 134, 155, 156, 157, 170); needs-data 4 (159, 160, 164, 171); open 6 (230, 231, STORY-493..498); in-review 1 (34); ready-for-decision 1 (200); superseded 1 (194).
- `docs/bmad/stories` (99 file): done 34, in_progress 18, in-progress 13, to_do 9, superseded 5, open 3, implemented 3, blocked 3, backlog 3, verified 2, draft 2, completed 2, ready-for-dev 1, not_started 1.
- Stantie (VERIFICATO): STORY-499 (assegnazione esiste), STORY-020 (filtri esistono), STORY-511 (follow esiste con test) risultano ancora in_progress. DCP-103 e' ready-for-dev ma il corpo dice che quasi tutto e' chiuso.
- Bloccate: STORY-500, STORY-FIXCITY-PRIVACY-ACKNOWLEDGEMENT-AUDIT, inbound-design-comuni-information-architecture.
- `plans/stories/` alla radice (15 story di pianificazione 1.1..5.3, es. 3.1 contratto del ciclo di vita, 3.2 consenso GDPR e retention, 3.3 idempotenza notifiche) sono tutte in stato backlog con task non spuntati (VERIFICATO su 3.1 e 3.3; le altre non lette). Contraddicono la regola "BMAD solo nei docs di modulo/tema" (vedi sezione 4).

### 2.6 Conformita e gate

- `docs/compliance/01-matrice-conformita-normativa.md` (datata 2026-10-13, VERIFICATO): 39 righe; conforme 0, parziale 10, assente 8, n/a 3, NON VERIFICATO 18; rischio alto 10. Righe N-04, N-14, N-15 sono superate dai fatti (vedi sezione 4).
- `bashscripts/tools/release/go-live-gate.py` (329 righe, VERIFICATO): controlla env, tenant, guardie legali, segnaposto, host di terze parti, robots/sitemap/security.txt, scansione LG, HTTPS, REPO-01/02. Non controlla niente sul dominio (coda, scheduler, ruoli, consenso, retention, ripristino backup). Base riportata dal committente: 12 controlli bloccanti falliti (config produzione, testi legali, contatto DPO, marker di merge in Rating/package-lock.json, nomi .md, HTTPS): non rieseguita da me.
- Marker di merge: nessuno in Fixcity ne in Mobile (VERIFICATO con grep).

---

## 3. Lacune

Legenda gravita: BLOCCA = impedisce o rende rischiosa la messa online del web; QUALITA' = difetto o buco da chiudere per un prodotto completo; RIFINITURA = pulizia. Gli slug sono proposte senza numero DCP: il numero va assegnato con il controllo che blocca (rischio di collisione di numeri, vedi memoria di progetto).

Chi decide/esegue: sviluppo = team tecnico; ente = amministrazione committente; titolare = titolare del trattamento / DPO; fornitore = fornitore esterno (store, push, hosting).

| ID | Descrizione | Evidenza | Gravita' | Chi | Dipendenze | Dim. | Slug story |
|---|---|---|---|---|---|---|---|
| A-01 | Il cittadino non riceve notifica per approvazione, chiusura, rifiuto, assegnazione, media; nessuna notifica alla creazione (ne alla PA ne al cittadino). Il codice notifica solo `status_change` pubblico verso stati visibili. Il listener di creazione e' scollegato. La promessa di trasparenza dello stato (PRODUCT.md) non e' mantenuta. | VERIFICATO: `NotifyTicketSubscribersOfStatusChangeAction.php:25` (return se `event_type !== StatusChange`); `app/Providers/EventServiceProvider.php` vuoto e `shouldDiscoverEvents()` vero solo per la classe base (vendor, riga 138), quindi `TicketCreatedListener` non e' registrato; STORY-003 "done" cita classi inesistenti. | BLOCCA go-live | sviluppo (copy e destinatari: ente) | DEC nuovo "chi viene notificato" (DEC-59); modulo Notify | M | notifiche-ciclo-di-vita-complete |
| A-02 | L'attore dell'audit si perde per utenti con UUID: `ChangeStatus` e `AssignTicketAction` salvano `user_id = null` perche accettano solo interi. Le altre Action (Approve, Reject, Close, Communication, MediaObserver) accettano `int` o `string`. Tracciabilita delle decisioni PA incompleta. | VERIFICATO: `ChangeStatus.php:49` e `AssignTicketAction.php:41` (`is_int($actorId) ? $actorId : null`); `ticket_activities.user_id` e' uuid (migrazione `2026_09_26_151800`); il PHPDoc del modello dice `int|null`. | BLOCCA go-live | sviluppo | nessuna | S | attore-audit-uuid-in-changestatus-e-assign |
| A-03 | Il wizard rifiuta l'invio se non esiste un'informativa pubblicata (fail-closed): finche non e' pubblicata nessuno puo segnalare. L'informativa locale e' di 714 byte, incompleta rispetto all'art. 13 secondo la matrice. Il controllo di pubblicazione verifica solo che il file esista e non sia un template; la completezza (`AssessPrivacyPolicyAction`) non e' un gate. I test dei gate di pubblicazione sono in skip. | VERIFICATO: `CreateTicketWizardWidget.php:69-73`; `laravel/config/local/fixcity/lang/it/policy.md` 714 byte; template in `Modules/Fixcity/resources/templates/privacy-policy.{it,en,de,es}.md`; skip in `PrivacyPolicyPublicationGateTest`. RIPORTATO: DCP-130 in-progress, matrice N-04. | BLOCCA go-live | titolare/ente (testo), sviluppo (gate) | DEC-02, DEC-07; DPO | M | informativa-pubblicata-e-gate-completezza |
| A-04 | La prova del consenso non viene salvata: `privacyAccepted` e' validato ma non persistito (chi, quando, quale versione dell'informativa). Una contestazione non e' difendibile. | VERIFICATO: grep, `privacyAccepted` compare solo in `TicketForm.php:148,169-183` e `CreateTicketWizardWidget.php:70`, nessuna scrittura. RIPORTATO: matrice N-02, SEC-12 di DCP-49, DEC-07/08, DCP-134. | BLOCCA go-live | sviluppo, titolare (versioning dell'informativa) | modulo Gdpr (consensi) | M | prova-del-consenso-persistita |
| A-05 | Nessuna conservazione limitata, nessun diritto dell'interessato, nessuna cancellazione: `DeleteTicketAction` fa soft delete senza activity; non esiste forceDelete/anonimizzazione. Il file statico pubblico `public_html/data/tickets.json` (24.442 byte, generato 2026-10-09, 27 feature demo) viene prodotto solo da `GenerateTicketsJsonAction` (export manuale e seeder) e non e' aggiornato su cancellazione. Nessun job di retention. | VERIFICATO: `DeleteTicketAction.php`; grep `forceDelete\|erase\|anonymi` in `app/` vuoto; `ls -l public_html/data/tickets.json`. RIPORTATO: matrice N-06, N-08; Gdpr DCP-140/141/204. | BLOCCA go-live | titolare (tempi e modalita), sviluppo | Gdpr; DEC-09, DEC-10; nuova DEC-60 | L | retention-cancellazione-e-diritti-sui-ticket |
| A-06 | Nessun seed di ruoli e permessi per la produzione: `operator` e il ruolo di accesso al pannello nascono solo nel seeder demo (limitato a local/testing/demo); il ruolo `supervisor` non viene mai creato (solo nominato in `BasePolicy::PA_ROLES`). Nessun percorso di onboarding del personale PA. | VERIFICATO: `DemoOperatorPanelAccessSeeder.php:22,57`; grep `supervisor` in tutto Fixcity/User: solo `BasePolicy`. | BLOCCA go-live | sviluppo, ente (chi sono gli operatori) | modulo User | M | ruoli-pa-seed-produzione-e-onboarding |
| A-07 | Nessuno scheduler registrato per Fixcity e nessuna verifica che coda e worker siano attivi: le notifiche sono accodate (`ShouldQueueAfterCommit`), quindi senza worker non partono. Mancano anche i job periodici naturali (chiusura automatica, retention, rigenerazione sitemap/GeoJSON). Il gate non lo controlla. | VERIFICATO: grep `Schedule::` in Fixcity, Gdpr, User, Notify, `app/` senza risultati; `routes/console.php` contiene solo `inspire`; `app/Console/Commands` e' vuota. | BLOCCA go-live | sviluppo, fornitore (hosting/supervisor) | DEC-27 (da rileggere: probabile pertinenza, non ricontrollato); modulo Job | M | scheduler-e-worker-predisposti-e-verificati |
| A-08 | Rotta orfana `POST /livewire/form-create-ticket` verso `CreateTicketWizardWidget@submit` senza middleware `auth` ne `throttle`. Nessun chiamante nei temi, in Mobile, nei test o in Fixcity (solo README e una story). L'effetto per un ospite non e' determinato (il widget deriva l'owner dall'utente corrente). | VERIFICATO: `laravel/routes/web.php:27`; grep dei chiamanti vuoto. | BLOCCA go-live | sviluppo | nessuna | S | rimuovere-o-proteggere-rotta-form-create-ticket |
| A-09 | Pagina Folio legacy `tickets/[slug].blade.php` nel modulo: `withTrashed()`, solo `auth` senza autorizzazione (qualunque utente loggato vede qualunque ticket per slug, anche cancellati), mostra nome ed email dell'utente corrente come "autore", chiama Nominatim lato server con User-Agent segnaposto, convive con `tickets/[id].blade.php`. Se il tema non la oscura e' una fuga di dati. L'oscuramento da parte del tema e' probabile (Folio registra prima il tema) ma non determinato (route:list non eseguito). | VERIFICATO: `pages/tickets/[slug].blade.php:11,13,28,46`; ordine di registrazione in `Cms/.../FolioVoltServiceProvider.php`. | BLOCCA go-live | sviluppo | verifica con route:list a cura di chi puo eseguirlo | S | eliminare-pagina-legacy-tickets-slug |
| A-10 | API pubblica `/api/tickets/list` restituisce gli URL delle immagini originali (possibili EXIF/GPS), in contrasto con la regola di `BuildPublicTicketDetailAction` ("immagini originali: mai"). `api/ticket-details/[ticket]` ricade ancora sui dati demo di `tickets.json` per id non presenti. Nessun throttle, CORS, versioning, OpenAPI. | VERIFICATO: `pages/api/tickets/list.blade.php:49` (`getFullUrl()`); `LoadPublicTicketsGeoJsonAction` legge `../public_html/data/tickets.json`. RIPORTATO: DEC-24 aperta. | BLOCCA go-live | sviluppo, titolare (DEC-24) | DEC-24; Media | M | api-pubbliche-solo-derivate-e-throttle |
| A-11 | Strategia mobile non decisa: PRODUCT.md dice piattaforma web con "invio da mobile dopo l'accesso"; il modulo Mobile e' un host NativePHP con schermate simulate. Non c'e' decisione tra PWA, app nativa o solo web responsive, ne ambito per il go-live, ne credenziali store/FCM/APNs. Il go-live web non dipende da questo, ma la pianificazione si. | VERIFICATO: PRODUCT.md; `Mobile/docs` e story STORY-MOBILE-INVESTOR-DEMO (in_progress, criteri tutti non spuntati). Nessuna DEC corrispondente nel registro. | BLOCCA go-live (solo se l'app e' nel perimetro; altrimenti decisione di pianificazione) | ente/titolare | proposta DEC-57 | S (decisione) | decisione-strategia-mobile |
| A-12 | `RejectTicketAction` porta il ticket da IN_REVIEW a PENDING con `setStatus` diretto, bypassando `assertCanTransitionTo`; non esiste uno stato terminale "rifiutato"; l'attivita e' AuthorOnly e il cittadino non e' avvisato. L'intestazione dell'enum dichiara la matrice come unica fonte di verita. | VERIFICATO: `RejectTicketAction.php:47`; `TicketStatusEnum::allowedTransitions()`. | QUALITA' | ente (semantica del rifiuto), sviluppo | proposta DEC-58 | M | rifiuto-ticket-stato-terminale-e-notifica |
| A-13 | Azione Filament `ChangeStatus` mostra tutti gli stati dell'enum, non solo i target consentiti; una transizione non valida lancia `DomainException` senza try/catch ne notifica d'errore all'operatore. | VERIFICATO: `app/Filament/Actions/ChangeStatus.php:38` (`->options(TicketStatusEnum::class)`). | QUALITA' | sviluppo | A-12 | S | changestatus-solo-target-consentiti |
| A-14 | Nel `ViewTicket` il campo "assegna a" elenca tutti gli utenti (nomi dei cittadini inclusi) e permette di assegnare un cittadino. Non e' limitato ai ruoli PA. | VERIFICATO: `ViewTicket.php:59` (`->pluck('name','id')` su tutti gli utenti). | QUALITA' | sviluppo | A-06 | S | assegnazione-solo-operatori-pa |
| A-15 | Nessun percorso per REOPENED (nessuna Action o UI lo imposta tranne il selettore generico) e nessuna chiusura automatica RESOLVED -> CLOSED; nessuna UI cittadino per integrare, ritirare o riaprire. La policy permette al proprietario di modificare in qualsiasi stato. | VERIFICATO: grep REOPENED; `TicketPolicy::update`; assenza di `Schedule::` (A-07). | QUALITA' | ente (termini), sviluppo | A-07, DEC-58 | M | riapertura-e-chiusura-automatica |
| A-16 | SLA/KPI non affidabili: il tempo medio di risoluzione e' `avg(updated_at - created_at)`, ma `updated_at` cambia a ogni modifica (rating incluso). Manca il tempo di prima presa in carico (KPI citato da PRODUCT e dalla mappa decisioni), soglie e allarmi SLA. `GetTicketKpiAggregateAction` conta solo globalmente. | VERIFICATO: `GetTicketSlaMetricsAction.php:79,83`. | QUALITA' | sviluppo, ente (soglie) | A-02 (serve l'attivita come fonte) | M | kpi-da-attivita-prima-presa-in-carico |
| A-17 | Dashboard back office vuota: la view contiene solo `<x-filament::page>` e `XotBaseDashboard::getWidgets()` ritorna `[]`; i KPI stanno solo nell'header di `ListTickets`. STORY-004 e' "done". Esistono view orfane stile Jira senza classi Page. | VERIFICATO: `resources/views/filament/pages/dashboard.blade.php`; `ListTickets.php:30-35`; `filament/pages/{board,jira-import,kanban,road-map,scrum,timesheet-export}.blade.php`. | QUALITA' | sviluppo | A-16 | S | dashboard-operativa-con-widget-kpi |
| A-18 | Geocoding scollegato: `GeocodeTicketAddressAction` non ha chiamanti; la story in_progress dice che `app/Jobs/GeocodeTicketAddressJob.php` e' stato creato ma il file non esiste; due test puntano a `Modules\Fixcity\Jobs\GeocodeTicketAddressJob` inesistente (e assumono una colonna `address` che non c'e'). | VERIFICATO: grep chiamanti; `laravel/tests/Feature/Feature/Jobs/GeocodeTicketAddressJobTest.php:8`; `laravel/tests/Unit/Modules/Fixcity/Jobs/GeocodeTicketAddressJobTest.php:7`. | QUALITA' | sviluppo | A-07 (se asincrono), DEC-46 | S | geocoding-collegare-o-rimuovere |
| A-19 | Assenza di test sul ciclo di vita e sul back office: nessun test per ChangeStatus, Approve, Reject, Resolve, Close, Transition, ChangeTicketPriority, RecordTicketCommunication, FollowTicketAction, BuildTicketsGeoJsonAction; nessuno per `ViewTicket` e `ListTickets`; `TicketResourceTest` esiste solo come `.lock`. | VERIFICATO: elenco `tests/` e grep dei nomi delle Action. | QUALITA' | sviluppo | A-02, A-12, A-13 | L | test-ciclo-di-vita-e-azioni-back-office |
| A-20 | Stato reale della suite non determinato: ultimo dato 27 rossi del 2026-10-14; `PersonalAreaSettingsPagesTest` risulta ancora aperto (RIPORTATO); tre gate di pubblicazione sono in skip per costruzione. Il README dice che la suite completa non e' stata eseguita. Pest non eseguito da me come da vincolo. | VERIFICATO: elenco skip; RIPORTATO: DCP-103. | QUALITA' | sviluppo | ambiente di test isolato (`pest-scratch-db`) | M | baseline-suite-fixcity-misurata |
| A-21 | `ticket_hours` non ha alcuna UI di inserimento: modello, factory, relazioni e migrazione UUID esistono, ma in `app/Filament` c'e' solo `CommentsRelationManager`. DEC-35 e' ancora senza esito nel registro. | VERIFICATO: `dcp-210` riga 25; elenco `app/Filament`. | QUALITA' | ente (serve davvero?), sviluppo | DEC-35 | M | ticket-hours-ui-o-ritiro |
| A-22 | Lingua delle notifiche non gestita: `TicketStatusChangedNotification` non ha `locale()` ne `HasLocalePreference` (nemmeno su User): email e testo in-app salvato usano la lingua del worker. Mancano in `de` ed `es` i file `ticket_notification.php`, `ticket_citizen_rating.php`, `ticket_sla_overview.php`, `ticket_overview.php`, `change_status.php`, `change_priority.php` e altri (it 77 file, en 82, de 34, es 37). Effetto del fallback non determinato (non sono riuscito a leggere i file di lingua perche usano `merge_translation_files()`). | VERIFICATO: elenco file lang; grep `locale(`/`HasLocalePreference`. | QUALITA' | sviluppo, ente (lingue obbligatorie, DCP-160) | modulo Lang | M | notifiche-localizzate-e-lang-de-es |
| A-23 | `hasPermissionToOrCreate` esegue `Permission::findOrCreate` a ogni controllo: scrittura su DB in un percorso di lettura (le policy di Fixcity ereditano da `BasePolicy`). | VERIFICATO: `User/app/Models/Traits/HasSpatiePermission.php:20`. | QUALITA' | sviluppo | modulo User | S | permessi-senza-scrittura-nel-controllo |
| A-24 | `tickets.code` (capability pubblica di tracciamento) e' `nullable` e senza indice unico; `AllocateTicketCodeAction` e' check-then-insert con `Str::random(16)` in maiuscolo. Probabilita di collisione trascurabile ma non vincolata dal DB. | VERIFICATO: `2026_06_10_140000_create_tickets_table.php:28` (solo `slug` e' unique). | QUALITA' | sviluppo | migrazione su dati esistenti | S | codice-ticket-indice-unico |
| A-25 | Migrazioni duplicate: tre `create_tickets_table`, tre `create_profiles_table`, due `create_ticket_activities_table`, due `create_categories_table`, in contrasto con la regola R3 (una migrazione per tabella). Rischio di ordine e di schema divergente su installazioni nuove. | VERIFICATO: `ls database/migrations`. | QUALITA' | sviluppo | database sacro (nessun reset): solo analisi di equivalenza | M | consolidare-migrazioni-duplicate |
| A-26 | Nessun limite di frequenza sulla creazione di segnalazioni (spam e carico sulla PA); i soli throttle sono su tracciamento, follow e form accessibilita. | VERIFICATO: grep `RateLimiter\|throttle` in Fixcity. | QUALITA' | sviluppo | A-08 | S | throttle-creazione-ticket |
| A-27 | Foto originali: `media-library.php` applica `--strip-all` solo alle conversioni; la gestione di EXIF/GPS sugli originali caricati e' non determinata. Rischio privacy (matrice N-07 RIPORTATA). | VERIFICATO: `config/media-library.php:142,149`; il resto non determinato. | QUALITA' | sviluppo, titolare | Media | M | rimozione-exif-sugli-originali |
| A-28 | Tipologie di segnalazione fisse (enum a 15 casi), nessuna configurazione per comune e nessun instradamento o assegnazione automatica per tipo o zona. Il modello multi-comune non e' sfruttato. `Category`, `Report`, `Activity` sono modelli legacy non usati. | VERIFICATO: `TicketTypeEnum`; assenza di Resource per Category. | QUALITA' | ente | proposta DEC-61; Tenant | L | tassonomia-configurabile-e-instradamento |
| A-29 | FAQ senza amministrazione: nessun Resource Filament; `ListPublishedFaqsAction` dipende da `Database\Seeders\Demo\CivicFaqCatalog` (classe demo) e inghiotte `Throwable`. | VERIFICATO: `app/Actions/Faq/ListPublishedFaqsAction.php`; elenco `app/Filament`. | QUALITA' | sviluppo, ente (contenuti) | Cms | M | faq-gestibili-da-backoffice |
| A-30 | Accessibilita: i difetti ACC-01..05 del wizard e la prova con tecnologie assistive (NVDA) risultano non corretti o non verificati nei documenti; nel modulo non ho una prova di tastiera/screen reader. Obiettivo WCAG AA (PRODUCT.md). | RIPORTATO: `a11y-audit-dcp143`, matrice. VERIFICATO solo che i test Playwright esistono (8). | QUALITA' | sviluppo, ente (dichiarazione AgID) | tema Sixteen | M | wizard-accessibilita-acc-01-05-e-prova-assistiva |
| A-31 | Il gate di go-live non ha controlli di dominio: coda attiva, scheduler, ruoli seminati, informativa completa, consenso persistito, retention, notifica effettivamente recapitata, backup ripristinabile. | VERIFICATO: lettura di `go-live-gate.py` (329 righe). | QUALITA' | sviluppo | A-03..A-07 | M | gate-go-live-controlli-di-dominio |
| A-32 | Dipendenze non dichiarate: `composer.json` di Fixcity ha `require` vuoto e `module.json` non ha `requires`, ma il codice usa 10 altri moduli (A sez. 1.1). Esistono anche notifiche di ticket duplicate in `Notify` (`TicketAssignedNotification`, `TicketStatusChangedNotification`) usate solo da un test di Notify. | VERIFICATO: lettura dei file e grep degli `use`. | QUALITA' | sviluppo | regola Nwidart modulare | S | dichiarare-dipendenze-e-rimuovere-notifiche-duplicate |
| A-33 | Conformita normativa: L.241/90 (responsabile del procedimento e termini, DCP-156), codice fiscale dell'autore (DCP-48), SPID/CIE (N-24, DCP-144), NIS2 (DCP-159), lingue minoritarie (DCP-160), vocabolari controllati (DCP-171) sono ferme in needs-decision/needs-data e non hanno una voce nel registro DEC. Terze parti (tile OSM, Nominatim, font) non dichiarate nell'informativa (N-17). La matrice ha 0 righe conformi. | VERIFICATO: front matter delle story e matrice; RIPORTATO: contenuto delle righe N-17, N-24, N-34. | QUALITA' | ente/titolare | proposte DEC-52..56 | M | decisioni-normative-aperte-nel-registro |
| A-34 | Mobile contiene un dominio ristorante: azioni (`TakeOrderAction`, `SplitBillAction`, `KdsNotificationAction`, `ViewFloorPlanAction`, `SyncOfflineOrdersAction`), modelli (`OrderQueue`, `WaiterSession`), Data (`Category`, `Product`, `Table`), contratto `RestaurantGateway`, notifiche KDS, seeder, view, migrazione senza prefisso di data. `SyncOfflineOrdersAction` ha come default l'host esterno `https://api.sottana.com`. | VERIFICATO: elenco file; `SyncOfflineOrdersAction.php:32`. README e story 1.11 (ready-for-dev) ammettono il confine da ripulire. | QUALITA' | sviluppo | A-11 | M | mobile-rimuovere-dominio-ristorante |
| A-35 | Le schermate native di Fixcity sono dimostrative: dati demo (`getDemoTicket`, `getDemoTimeline`, `getDemoTickets`), `MapScreen.php:39` conta 27 marker "dal seed demo", `TicketDetailScreen::updateStatus/subscribe` modificano solo lo stato locale, `userCanUpdate()` e' simulato. Non esiste una API mobile (crea segnalazione, mie segnalazioni, registrazione dispositivo/push). `auth_guard` e' `api` ma Fixcity non ha token. Push `onesignal` configurato senza implementazione. Nessuna firma release, nessun iOS. | VERIFICATO: lettura delle schermate e di `config/mobile.php`; routes `api/mobile/web` ritornano `[]`. STORY-MOBILE-INVESTOR-DEMO: criteri tutti non spuntati. | QUALITA' | sviluppo, fornitore (store, FCM/APNs) | A-11, A-10 | L | mobile-api-e-schermate-reali |
| A-36 | Mobile: `app/Native/Screens/CreateTicketScreen.php:10` dichiara `class TicketDetailScreen` (stessa classe di `TicketDetailScreen.php:10`): conflitto di classe e di PSR-4, e non esiste una vera schermata di creazione. `MobilePluginRegistry.php` esiste identico in `app/` e in `app/Plugins/` con lo stesso FQCN. I test coprono solo il ristorante (`TakeOrderActionTest`, `RestaurantDataTest`). | VERIFICATO: lettura dei file. | QUALITA' | sviluppo | A-34 | S | mobile-classi-duplicate-e-test-ticket |
| A-37 | Pagina FO legacy `tickets/[id].blade.php` con testi italiani fissi ("Passaggi", "Passaggio", righe 59, 60, 64) e passa il modello intero invece di `BuildPublicTicketDetailAction`; pagine di modulo orfane (`pages/home.blade.php`, `pages/prova.blade.php` con `x-ui.*`, `pages/lista-categorie.blade.php`); widget legacy `CreateTicketWidget` (247 righe) che crea con `Ticket::create` senza Action, codice o stato imposto, usato solo dal proprio test. | VERIFICATO: lettura dei file. | RIFINITURA | sviluppo | A-09 | S | rimuovere-pagine-e-widget-legacy |
| A-38 | `app/View/Components/Blocks/TicketList.php` ha conteggi di categoria fissi inventati ("(21)", "(208)", "(321)", "(302)") e `Ticket::query()->latest()->paginate(10)` senza filtro di visibilita; uso non determinato. | VERIFICATO: `TicketList.php:27,30,42`. | RIFINITURA | sviluppo | verifica dell'uso nei JSON CMS | S | ticketlist-dati-reali-o-rimozione |
| A-39 | Governo delle story: 12 numeri duplicati (013, 020-026, 498, 510, 514, 515); `stories/index.md` omette 18 story; stati stantii (STORY-499, 020, 511); `plans/stories` alla radice contrasta con la regola sulle sedi BMAD; DEC-35 e DEC-46 non aggiornate. | VERIFICATO: confronto cartelle e indice. | RIFINITURA | sviluppo | regola "non rinumerare, collegare o marcare superseded" | S | riallineare-indice-e-stati-delle-story |
| A-40 | Deriva documentale e residui: conteggi del README superati; file `.old/.bak/.backup/.no_model` in app, database e resources; almeno 17 `.lock` nel codice; pagine web mobile con `<form action="#">`, categorie diverse da `TicketTypeEnum` (`street_lighting` contro `public_lighting`), Leaflet da `unpkg.com`, emoji nei titoli, testi fissi, dati da `/data/tickets.json`. | VERIFICATO: find e lettura di `Mobile/resources/views/tickets/create.blade.php` e `pages/mobile/map.blade.php`. | RIFINITURA | sviluppo | A-35 | S | pulizia-residui-e-viste-mobile-web | 

Riepilogo: 11 BLOCCA, 25 QUALITA', 4 RIFINITURA (totale 40).

---

## 4. Contraddizioni tra documenti e codice

1. `docs/README.md`: "64 file in app/Actions", "105 file" di pagine, "128 file PHP" di test, "99 `.md`" nella radice. Misure: 75, 109, 143, 111 (VERIFICATO).
2. `bmad-remaining-work.md` dice che servono story specifiche del modulo; esistono 73 file in `docs/stories` e altre centinaia in `docs/bmad` (VERIFICATO). Dice anche "PHPStan 0 errori su 9196 file verificato 2026-10-26": non ricontrollato (RIPORTATO). `completion-backlog.md` e' un modello generico, uguale riga per riga (righe 9-60) tra Fixcity e Mobile (VERIFICATO): non contiene evidenze del modulo.
3. `bmad/completion.md` rimanda a `plans/fixcity-completion-*.md` e a `plans/stories` alla radice (esistono, VERIFICATO), mentre le regole del progetto vogliono le story solo in `laravel/Modules/*/docs` o `Themes/*/docs`.
4. DEC-35 (ticket_hours) senza esito, ma `dcp-210` e' done con migrazione che ha scelto l'allineamento a UUID. DEC-46 e `dcp-241` e la story `geocode-ticket-address-job-location-json` parlano di un Job che non esiste (A-18).
5. STORY-003 (done) cita eventi e `NotificationService` assenti; STORY-004 (done) promette una dashboard KPI che e' vuota; STORY-010 (done) cita pagine `/admin/*` non trovate nel modulo.
6. `TicketStatusEnum` dichiara la matrice come unica fonte di verita, ma `RejectTicketAction.php:47` la aggira. La documentazione di REOPENED ("il cittadino non ha accettato") non ha un percorso nel codice.
7. README di Mobile: "agnostico, nessuna route ne seeder verticali". Nel modulo ci sono i tre file di route (vuoti), seeder `Waiter/MenuItem/Order/DemoData` e codice ristorante. La story 1.11 dice che i DTO sono in `app/Data`, ma la cartella reale e' `app/Datas`.
8. Il README delle migrazioni afferma "un proprietario per tabella"; ci sono migrazioni duplicate (A-25).
9. Matrice di conformita del 2026-10-13: N-14 (header di sicurezza) e N-15 (freno al tracciamento) sono superate da `SecurityHeadersTest` e da dcp-149 (done); N-04 (pagina informativa) dipende da DCP-130, ancora in-progress. Le altre righe non sono state riverificate.
10. `fixcity-nativephp-mobile.md` (in_progress) ammette superfici ristorante residue; il resto della documentazione parla di una app mobile come se fosse pronta (VERIFICATO: solo dati demo).
11. `Fixcity/docs/README.md`: "le operazioni asincrone sono Queueable Actions; non esistono Jobs/Services" e allo stesso tempo storie e test fanno riferimento a `Modules\Fixcity\Jobs\GeocodeTicketAddressJob` (A-18).

---

## 5. Documentazione mancante o da riscrivere

Percorsi proposti, tutti dentro `laravel/Modules/Fixcity/docs/` o `laravel/Modules/Mobile/docs/`. Nomi senza date ne suffissi numerici (la data va nel front matter). Prima di creare un `.md`: `python3 -I bashscripts/tools/docs/md-filenames.py name <percorso>`.

| Documento proposto | Contenuto | Motivo |
|---|---|---|
| `Fixcity/docs/wiki/concepts/ticket-lifecycle-contract.md` | Matrice transizioni, attore per transizione, evento di attivita, visibilita, notifica, regola di rifiuto/riapertura/chiusura automatica | Oggi la fonte e' sparsa tra enum, Action e story; la story di pianificazione 3.1 e' in backlog (A-01, A-12, A-15) |
| `Fixcity/docs/wiki/concepts/notification-matrix.md` | Evento -> destinatario -> canale -> lingua -> template, e cosa non viene inviato | STORY-003 e' contraddetta (A-01, A-22) |
| `Fixcity/docs/wiki/concepts/privacy-data-flow-and-retention.md` | Quali dati personali (owner, email, foto, coordinate), dove vivono, per quanto, come si cancellano, tickets.json | A-04, A-05, A-27 |
| `Fixcity/docs/wiki/how-to/operations-queue-scheduler-roles.md` | Worker, scheduler, ruoli da creare in produzione, controllo di salute | A-06, A-07 |
| `Fixcity/docs/wiki/concepts/public-surface-inventory.md` | Elenco delle rotte pubbliche (Folio, API, POST) con auth, throttle, campi esposti, chi le oscura | A-08, A-09, A-10 |
| `Fixcity/docs/wiki/concepts/kpi-definitions.md` | Definizioni: presa in carico, risoluzione, esito, copertura; fonte dati | A-16 |
| `Fixcity/docs/stories/` (aggiornare) | Riallineare `index.md` (mancano 18 story) e gli stati stantii | A-39 |
| `Fixcity/docs/README.md` (riscrivere la sezione "verificato nel codice") | Conteggi aggiornati; indicare che le cifre sono datate | Sezione 4, punto 1 |
| `Fixcity/docs/completion-backlog.md` e `bmad-remaining-work.md` | Sostituire con link a questo elenco di lacune o marcare superseded | Sezione 4, punti 2 e 3 |
| `Mobile/docs/wiki/concepts/mobile-scope-and-strategy.md` | Decisione PWA vs nativa, perimetro, requisiti store e push | A-11 |
| `Mobile/docs/wiki/concepts/mobile-api-contract.md` | Contratto API (crea, mie segnalazioni, dispositivo) e autenticazione | A-35 |
| `Mobile/docs/README.md` (riscrivere) | Dire la verita: cosa e' ristorante, cosa e' Fixcity, cosa e' demo | Sezione 4, punto 7 |
| `Fixcity/docs/compliance/` (aggiornare matrice) | Riverificare ogni riga con prove e data; marcare conforme dove vero | A-33, sezione 4 punto 9 |

---

## 6. Domande aperte per l'utente

Ogni voce e' una decisione umana. Dove esiste un DEC lo indico; le altre sono proposte di nuove voci del registro.

1. DEC-02 e DEC-07/08: chi pubblica e approva l'informativa e il testo del consenso, con quale versionamento? Senza questo A-03 e A-04 non si chiudono.
2. DEC-09/10 e nuova DEC-60: tempi di conservazione di segnalazioni, foto e log; cosa succede ai ticket cancellati in soft delete e al GeoJSON pubblico statico?
3. DEC-24: l'API pubblica deve restare, ritornare solo dati derivati e perdere il fallback demo?
4. DEC-27 (da rileggere, non ricontrollata): chi gestisce coda e scheduler in produzione?
5. DEC-35: confermare la scelta "allineare a UUID" e decidere se ticket_hours serve davvero (con UI) o va ritirato.
6. Nuova DEC-46 (da aggiornare): il geocoding e' sincrono, asincrono o non serve?
7. Nuova DEC-52 (DCP-48): serve il codice fiscale dell'autore? Con quale base giuridica?
8. Nuova DEC-53 (DCP-156): la segnalazione e' un'istanza ai sensi della L.241/90? Chi e' il responsabile del procedimento e quali termini?
9. Nuova DEC-54 (DCP-159): obblighi NIS2 per l'ente e dati organizzativi.
10. Nuova DEC-55 (DCP-160): lingue minoritarie richieste dall'ente?
11. Nuova DEC-56 (DCP-171): vocabolari controllati da adottare.
12. Nuova DEC-57: strategia mobile (PWA, app nativa o solo web), perimetro del go-live, store e credenziali FCM/APNs, rimozione del codice ristorante.
13. Nuova DEC-58: semantica di rifiuto, ritiro, integrazione e riapertura; termine di chiusura automatica.
14. Nuova DEC-59: chi viene notificato alla creazione, all'approvazione, alla chiusura e al rifiuto (casella della PA, cittadino, entrambi).
15. Nuova DEC-61: le tipologie devono essere configurabili per comune, con instradamento automatico?
16. Chi sono gli operatori e i supervisori iniziali e come si creano gli account PA in produzione?
17. Posso verificare con `route:list` (a cura di chi puo eseguirlo) se `tickets/[slug].blade.php` del modulo e' oscurata dal tema?
18. La suite Fixcity va misurata in ambiente isolato (`pest-scratch-db`) per ottenere una baseline vera?
