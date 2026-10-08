---
title: "gap analysis"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-27
qmd: "gap analysis"
issues: []
discussions: []
---

# FixCity — Gap analysis BMAD

Il benchmark competitivo aggiornato e la priorità dei gap sono in
[competitor-benchmark.md](competitor-benchmark.md). In sintesi,
prima del pilota vanno chiusi o formalmente accettati routing/Open311 outbound,
workflow SLA/work-order, deduplica con merge auditato, privacy automatica dei media,
delivery osservabile e smoke staging cittadino/PA. Watch area, PWA, moderazione/open
data e partecipazione restano P1/P2 secondo la decisione prodotto.

## Bloccanti

| ID | Gap | Chiusura richiesta |
|---|---|---|
| G-01 | connessione MySQL test diretta negata (`fixcity_data_test`) | SQLite condiviso documentato consente la suite completa; resta da risolvere il MySQL per smoke HTTP/staging |
| G-02 | vertical slice non provato in staging | smoke cittadino → PA → chiusura |
| G-03 | Assegnazione PA | Action, UI, audit e notifica assignee implementati; Feature test passano su SQLite; resta prova MySQL/staging |
| G-04 | Notifiche workflow parziali | Stato pubblico e assegnazione hanno canale in-app + email verificata; STORY-013 implementa controllo email. Le due notification usano retry `ShouldQueueAfterCommit` (3 tentativi, backoff 60/300s). Restano worker/failed-jobs in staging, push, receipts/provider e smoke SMTP; evidenza in [retry story](stories/STORY-FIXCITY-NOTIFICATION-RETRY.md) |
| G-08 | Gestione follower | Follow/unfollow, lista personale, notifiche e opt-out email implementati; restano push e QA visuale |
| G-09 | Isolamento owner, accessibilità pubblica e campi audit | Chiuso e precisato: policy consente al proprietario il ticket privato, alla PA gli accessi autorizzati, e a chiunque i soli stati elencati in `TicketStatusEnum::canViewByAll()`; `created_by`/`updated_by` non sostituiscono `owner_id`. Il capability code resta visibile solo all'owner. Pest e Playwright con owner, secondo account e guest verificano ticket `pending` privato → altro account 403, ticket pubblico → altri account senza capability, guest via ID 403 e guest via code senza disclosure. E2E in [STORY-521](stories/STORY-521-demo-ticket-owner-assignment.md) |
| G-10 | Follow segnalazione | STORY-511: Action, CTA, UUID, pagina seguite, dispatch post-commit e opt-out email STORY-013; verificato dalla suite, restano push e verifica visuale |
| G-11 | Capability code nel dettaglio/notifiche | Payload dettaglio espone il codice solo all'owner; lista/notifiche usano il capability code e mai l'ID sequenziale. Pest verifica payload e destinatari; Playwright su SQLite verifica owner, secondo account e guest: codice visibile solo all'owner. Resta l'audit di delivery/log sul provider staging |
| G-12 | Filtri coda PA | Assegnatario, non assegnati e date coperti dai test Feature; manca smoke browser della coda |
| G-07 | Conferma/tracking FO | Lookup per codice e timeline verificati in Pest e browser con ticket sintetico su SQLite isolato; smoke staging con ticket persistito reale resta aperto |
| G-05 | Policy/isolamento end-to-end | Matrice policy e test Filament passano; resta verifica staging con cittadino A/B e ruoli PA |
| G-06 | accessibilità, backup, restore e monitoraggio non provati | report e runbook di release |
| G-13 | Rating FO — solo `owner_id` autorizza; `lockForUpdate()` serializza submit e Pest copre audit-only denial/doppio invio sequenziale. Riepilogo PA registrato nella coda, usa `XotBaseStatsOverviewWidget`; Livewire verifica dati popolati e empty state `— / 0`. Mancano gara DB concorrente e QA browser/accessibilità | test due transazioni MariaDB e screenshot/tastiera del widget |
| G-14 | Preferenze email per notifiche FixCity | STORY-013 implementata: opt-out nel profilo, UI impostazioni cittadino e controllo dei workflow stato/assegnazione | Suite Fixcity copre preferenze e workflow email. Chromium ora è disponibile tramite dipendenze estratte temporaneamente; smoke corrente copre login e tracking, non ancora la pagina preferenze. Mancano prova UI responsive delle preferenze e smoke SMTP/staging |
| G-15 | Area personale pratiche restituiva HTTP 500 per `$tickets` non condiviso con la view Folio | Risolto con `render()` e `View::with()`, aggiunto link impostazioni anche da pratiche/notifiche | Feature HTTP test delle tre pagine e suite Fixcity completa passati |
| G-16 | CTA della lista apriva il modal dettaglio e puntava a una route non canonica | Corretto: un CTA condiviso porta a `/it/tickets/create`; il modal resta ai marker | 2 Pest test / 9 asserzioni; browser click guest → `/it/auth/login` |
| G-17 | Titolo browser della lista era “Laravel” e la vista CMS leggeva metadati fuori dallo scope Volt | La pagina CMS risolve titolo localizzato e descrizione per slug prima del layout; corretto scope Blade | Browser: title “Segnalazioni”, HTTP 200; test regressione lista passati |
| G-18 | Titolo brand “Il mio Comune” veniva ellissato sul viewport mobile minimo | Risolto in Sixteen: font 16 px fino a 359 px; nessun clipping a 320 px | Browser: scroll width testo 95/95 px a 320 e 111/111 px a 360; build asset completata |
| G-19 | Etichette Codice/Stato tracking mostravano chiavi di traduzione non risolte | Aggiunte chiavi IT e EN ai cataloghi `ticket.fields`; il flusso valido e il recupero codice errato hanno testi localizzati | Test tracking IT/EN; browser IT 320 px e EN 1440 px senza chiavi grezze, overflow o errori JS |
| G-20 | Tracking mostrava `in_progress`, titolo Laravel e una riga codice vuota per il lookup pubblico | Stato via label canonica `TicketStatusEnum`, title tradotto, riga codice resa solo se il payload autorizzato contiene il valore | E2E browser su SQLite: owner vede codice; secondo account vede solo campi pubblici e riceve 403 sul ticket privato `pending`; guest via ID → 403; guest via code → stato/titolo senza capability. Smoke provider/staging resta aperto |
| G-21 | Wizard autenticato mostrava title browser “Laravel”, chiave EN grezza e perdeva il focus in validazione privacy | CMS title e cataloghi localizzati; stepper Sixteen legge il catalogo owner; checkbox comunica `aria-invalid`/`aria-describedby`; aggiunte le label IT/EN per il conteggio step | Pest IT/EN e Chromium submit fino alla conferma con title/codice localizzati; resta completa verifica tastiera/screen reader e smoke staging |
| G-22 | Riepilogo chiedeva dati personali senza persisterli | Rimossi codice fiscale, telefono, nome ed email ridondanti dal riepilogo autenticato; l'account identifica il proprietario e la conferma usa l'email dell'account | Pest completo Fixcity 367 test / 1.544 asserzioni; Chromium IT 320/768/1440 senza campi/overflow/errori JS |
| G-23 | Wizard FO scartava gli allegati: `getState()` invocava il salvataggio relazionale prima di avere il Ticket persistito | Legge e valida lo stato senza hook relazionali, crea il ticket, associa poi il modello al Form e chiama `saveRelationships()`; l'editor immagine è stato rimosso dopo errore JS Cropper riprodotto in Chromium | Pest e Chromium su SQLite effimero: upload Livewire senza HTTP/JS errori, ticket di prova `TCK-FQJ7YFEEBAKHLAYQ` e file Media nella collection `attachments`; test PA e smoke MySQL/staging restano aperti |
| G-24 | Nel riepilogo wizard UI mostrava chiavi letterali (`empty5`, `review_name`, ecc.) perché i cataloghi owner contenevano placeholder o mancavano in EN | Sostituite le label italiane con copy leggibile, aggiunto catalogo inglese e nascosto il titolo della Section vuota | Pest IT/EN; Chromium su `/it|en/tickets/create` a 320/768/1440: label corrette, zero chiavi grezze/overflow/errori JS |
| G-25 | Nel wizard EN il footer globale restava in italiano, con dati comunali dimostrativi e link placeholder `#` | Risolto in Sixteen: cataloghi IT/EN, brand FixCity, URL localizzati esistenti per home, invio e tracking; rimossi dati inventati e link `#` | Chromium su `/it/auth/login` e `/en/auth/login` a 320/768/1440 px: copy e tre destinazioni corrette, nessun overflow o errore JS; Pest mirato 2 test / 16 asserzioni e suite Fixcity 369 test / 1.570 asserzioni passano su SQLite isolato. `view:cache` passa |
| G-26 | Footer non può pubblicare contatti, titolare/privacy, note legali o dichiarazione di accessibilità: i dati ufficiali dell'ente non sono configurati | Aperto al proprietario dell'ente/CMS/Gdpr: fornire dati verificati e completare i contenuti legali; non inventare recapiti o approvazioni. La pagina privacy legge contenuto Markdown localizzato tenant | Il footer non espone recapiti dimostrativi. `/it/privacy` risponde 200 nel runtime locale con contenuto demo: questo non equivale ad approvazione del titolare né a sign-off per il go-live |
| G-27 | Guest inglese che apre `/en/tickets/create` veniva mandato a `/it/auth/login` dalla route nominata predefinita | Risolto nel bootstrap auth: il redirect usa `LaravelLocalization::getLocalizedURL()` per la locale della richiesta e conserva l'URL protetto come intended | Pest: 3 casi / 15 asserzioni; suite FixCity completa 372 test / 1.585 asserzioni. Chromium apre `/it|en/tickets/create`, atterra sul login della stessa lingua e mostra il footer corretto a 320/768/1440 px, senza overflow o errori JS |
| G-28 | Eliminazione ticket troppo ampia: owner e tutti i ruoli PA potevano eliminare; azioni singole e bulk non esplicitavano la policy | Risolto: solo admin o capability `ticket.delete`; dettaglio/modifica/bulk verificano `delete`/`deleteAny`; Action transazionale persiste `deleted_by` e applica solo soft delete | Pest policy + Livewire verifica ruoli e visibilità; suite FixCity, PHPStan e quality gate riportati nella story [ticket-delete-rbac](stories/STORY-FIXCITY-TICKET-DELETE-RBAC.md) |
| G-29 | Il rate limit del tracking guest restituiva la pagina Laravel 429 generica, in inglese e senza istruzioni per riprovare | Pagina errore FixCity IT/EN, messaggio accessibile e ritorno alla ricerca; conserva `Retry-After` e il limite esistente (10/minuto) | Pest mirato 10/89, suite FixCity 382/1.666, PHPStan, Pint, view cache e wiki gate verdi; browser responsive resta pending per dipendenza di sistema mancante. [Story](stories/STORY-FIXCITY-TRACKING-RATE-LIMIT-UX.md) |
| G-30 | Informativa FixCity: i testi demo non hanno sign-off legale e la UI deve distinguere disponibilità tecnica da approvazione ufficiale | La policy viene letta dal Markdown localizzato del tenant; la pubblicazione non costituisce approvazione. Il titolare deve validare copy, titolare, finalità, conservazione e contatti prima del go-live | `/it/privacy` risponde HTTP 200 nel runtime locale con contenuto tenant demo; non presentare la risposta 200 come prova di approvazione. Confermare locale e tenant di ogni lingua nel deploy candidato. [Story](stories/STORY-FIXCITY-PRIVACY-PUBLICATION-GATE.md) |
| G-31 | Semantica e tracciabilità del checkbox privacy non definite: `privacyAccepted` viene validato ma non persiste con il Ticket | Owner prodotto/titolare deve decidere se è presa visione o consenso, quali evidenze conservare e per quanto; solo dopo implementare la registrazione server-side o rimuovere il requisito non necessario. [Story](stories/STORY-FIXCITY-PRIVACY-ACKNOWLEDGEMENT-AUDIT.md) | Non implementare assunzioni giuridiche. AC e verifiche end-to-end pending la decisione owner |
| G-32 | I ticket demo potevano appartenere all'utente col record ID minimo, non al cittadino demo dichiarato | Risolto: `TicketDatabaseSeeder` assegna i ticket solo al cittadino identificato da `DemoUsersSeeder::CITIZEN_EMAIL`; se manca salta il seed, senza fallback. [STORY-521](stories/STORY-521-demo-ticket-owner-assignment.md) | Pest mirato 2/10: ownership con utente estraneo, idempotenza e skip senza citizen; Playwright isolato 1/1: owner vede codice, guest via ID 403, guest via code senza disclosure. PHPStan zero. La verifica non è un collaudo MySQL/staging |

## Evidenza aggiornata — 2026-09-27

- CTA segnalazione verificato in Pest (2 test / 9 asserzioni) e browser a 320/768/1.440 px. Il click guest raggiunge la pagina login localizzata con form visibile; nessun overflow e nessun errore JavaScript osservato. Screenshot di lavoro in `/tmp/fixcity-segnalazioni-{320,768,1440}-updated.png`.
- L’header mobile è stato corretto e ricontrollato a 320/360 px. L’area grigia vista in uno screenshot precedente non si riproduce dopo il completamento del caricamento: Chromium ha verificato tutti i tile OSM caricati (15 tile desktop), nessuna richiesta fallita e larghezza mappa coerente; il GeoJSON live contiene zero ticket, quindi il test resta limitato alla mappa senza marker.
- Tracking E2E su SQLite effimero: il builder Xot ha creato `/tmp/fixcity-browser.sqlite`; ticket/activity sintetici verificati a 320/768/1440 px. Il guest via code vede titolo, stato tradotto e timeline, ma non il capability code; owner autenticato via ID vede il codice; guest isolato via ID riceve 403. Server e dati condivisi non sono stati alterati.
- Wizard autenticato su SQLite temporaneo: title CMS IT/EN; step attivo e campi senza chiavi di traduzione; 320/768/1440 senza overflow. Browser ha verificato privacy obbligatoria e passaggio al secondo step con barra spaziatrice. Il rifiuto ora marca il checkbox `aria-invalid=true`, associa il testo errore via `aria-describedby` e rifocalizza il controllo; zero errori JS.
- Audit form: i campi autore/contatto del riepilogo non avevano colonne o mapping nel modello e venivano scartati da `Ticket::create()`. Sono stati eliminati dal wizard perché l'accesso richiede un account; codice proprietario e email di conferma continuano a provenire dall'utente autenticato. Test Feature copre schema, stato iniziale e render Livewire.
- Verifica dopo aggiornamento del test Unit legacy e del submit Livewire: suite Fixcity **367 test / 1.544 asserzioni**, PHPStan Modules senza errori, Pint mirato, `view:cache`, quality gate wiki e `git diff --check` verdi. Chromium IT autenticato a 320/768/1440: titolo corretto, nessun overflow, nessuno dei quattro campi rimossi, nessun errore JS. DB SQLite temporaneo/account sintetico rimossi.

- Suite Fixcity `APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity/tests --compact`: **365 test / 1.521 asserzioni passati** dopo le regressioni wizard IT/EN e accessibilità.
- PHPStan completo `php -d memory_limit=2048M vendor/bin/phpstan analyse Modules --no-progress`: `[OK] No errors` dopo la UI preferenze email.
- Quality gate wiki, Pint mirato, Blade cache e `git diff --check` verdi. `pint --test` globale resta non verde: molti file legacy richiedono formattazione e tre file Sixteen hanno parse error preesistenti; nessuno è stato modificato per questa story.
- Pint mirato, `php artisan view:cache`, `node --check Themes/Sixteen/resources/js/app.js` e `git diff --check`: exit 0.
- `bash bashscripts/quality-gates/verify-llm-wiki.sh`: PASS (frontmatter, merge marker, scratch).
- MySQL diretto usa `${FIXCITY_TEST_DB_USERNAME}` non interpolato e fallisce
  con 1045. Nessuna credenziale o grant è stato modificato.
- I test usano SQLite condiviso e `DatabaseTransactions`; `/it`, auth,
  elenco, route protette guest e tracking sono stati aperti in browser il 2026-09-27.
  Le route rispondono 200; le pagine protette terminano sul login, senza errori JS.
- `/it/` visualizza “Elenco segnalazioni”, coerente con `config/local/fixcity/database/content/pages/home.json`:
  griglia pubblica, mappa e CTA. Il browser ha verificato la route locale, non il contenuto
  CMS del deploy; il DB condiviso non è stato modificato.
- Screenshot responsive della lista 320/768/1.440 px, tab Mappa/Elenco, asset e
  limiti: [audit UI/UX](ui-ux-runtime-audit-2026-09-26.md). Restano accessibilità
  completa, focus errori, invio wizard/upload, rating browser e smoke PA/staging.
- MySQL diretto/staging resta bloccato dalle credenziali disponibili (SQLSTATE
  1045); non sono state cambiate credenziali o grant.

## Verifiche funzionali obbligatorie

- il cittadino crea titolo, descrizione, categoria, coordinate, consenso e immagini;
- un cittadino non vede ticket di altri cittadini;
- la PA filtra, apre, assegna, aggiorna, commenta ed esporta;
- le transizioni non autorizzate sono rifiutate;
- il cambio stato registra motivazione, attore e timestamp;
- il cittadino riceve la notifica e vede la timeline aggiornata;
- `resolved/closed` abilita il rating solo al proprietario;
- gli errori Geo/Media/Notify hanno fallback e non perdono il ticket.

## Non fare

Non usare controller HTTP o Services layer, non cambiare `phpstan.neon`, non modificare credenziali in modo ad hoc e non dichiarare completata una story senza evidenza.

## Artefatti non conformi rimossi

Sono stati rimossi due artefatti non referenziati che introducevano un'architettura non ammessa: `app/Http/Controllers/Citizen/CitizenAuthController.php` e `app/Actions/Wizard/FormValidationWizardAction.php`. L'autenticazione cittadino deve usare le pagine Folio/Actions già previste dai moduli User e Cms; la validazione wizard deve appartenere all'Action reale che persiste il ticket.

È stato rimosso anche `app/Http/Controllers/Wizard/WizardController.php` e l'omonima policy duplicata in `app/Policies/`: erano artefatti non referenziati che usavano namespace/classi inesistenti o colonne (`user_id`) non presenti nel modello Ticket. La policy canonica resta `app/Models/Policies/TicketPolicy.php`.

La policy applicativa corretta è invece `app/Policies/TicketPolicy.php` con namespace `Modules\\Fixcity\\Policies`, coerente con il `composer.json` del modulo. Gli artefatti duplicati sotto `App\\Actions`, `App\\Http\\Controllers` e le notifiche che usavano proprietà inesistenti del modello sono stati rimossi.

## UI/UX e convenzioni Filament

- [x] le pagine Resource Fixcity usano `XotBaseCreateRecord`, `XotBaseEditRecord`, `XotBaseViewRecord` e `XotBaseListRecords`;
- [x] la Dashboard Fixcity usa `XotBaseDashboard`;
- [ ] verificare in browser mobile il wizard autenticato completo, errori inline, focus, contrasto e stato di caricamento;
- [x] form tracking: ripristinata la validazione nativa del campo obbligatorio e collegato l’errore al campo; resta lo smoke test browser.
- [ ] verificare in browser desktop la tabella PA, assegnazione, cambio stato e feedback toast;
- [x] registrare screenshot/report responsive lista pubblica; non equivale a QA RC.
- [ ] registrare screenshot wizard/dettaglio/PA e smoke staging prima della promozione a release candidate.

## Migrazioni

Le migration nuove usano `foreignIdFor(Model::class, 'column')`; per User usano `XotData::make()->getUserClass()`. Le migration storiche con `foreignId('ticket_id')` non vengono riscritte: la regola forward-only richiede una migration evolutiva separata, se l'audit dello schema pilota la renderà necessaria.
