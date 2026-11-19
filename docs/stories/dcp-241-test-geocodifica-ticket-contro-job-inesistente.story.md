---
id: DCP-241
title: "DCP-241: test di geocodifica ticket e seeder scritti contro API inesistenti"
type: story
epic: DCP-240
status: in_progress
priority: P1
owner: module:Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "phpstan test inesistenti GeocodeTicketAddressJob TicketDatabaseSeeder 20 ticket DEMO cittadino demo tickets.json mock DatabaseTransactions"
related: [DCP-240, geocode-ticket-address-job-location-json, fix-phpstan-random-swarm, STORY-521]
---

# DCP-241: due test Pest del modulo Fixcity scritti senza leggere le classi

Fase BMAD: correzione alla sorgente e verifica. Epic: `bmad-output/epic-phpstan-modules-test-contro-api-inesistenti.md` (DCP-240).

## Misura di partenza

`analyse Modules` delle 13:34 UTC (epic DCP-240): Fixcity 49 errori, tutti in due file di test: `GeocodeTicketAddressJobTest.php` 47, `TicketDatabaseSeederTest.php` 2 (riga 25 `method.nonObject` su `User::factory()->create()`, riga 95 `cast.string`).

## Scopo ricostruito

### TicketDatabaseSeederTest (comportamento esistente: riscritto)
Proteggere il dataset demo di presentazione del front office pubblico: il seeder `TicketDatabaseSeeder` crea ticket `DEMO-*` a Mogliano Veneto, tutti con coordinate nell'area, con `location` (citta, provincia, `country_code`), con stati pubblicamente visibili, slug stabili, idempotente, senza toccare i ticket preesistenti.

### GeocodeTicketAddressJobTest (7 test)
Un job accoda la geocodifica inversa Nominatim di un ticket con coordinate e salva l'indirizzo leggibile: (1) successo, GET `/reverse` con `lat` e `lon`; (2) cache: due ticket con le stesse coordinate fanno una sola chiamata HTTP; (3) coordinate mancanti: warning `Ticket missing coordinates`, nessun indirizzo; (4) Nominatim 500: errore `Nominatim API error`, nessun indirizzo; (5) corpo non valido: nessun indirizzo; (6) risposta senza `display_name`: nessun indirizzo; (7) un retry dopo un 500.

## Causa radice

1. **Seeder test**: scritto per una versione precedente del seeder (15 ticket) e mai riallineato. Il seeder reale ha 20 ticket (`DEMO-001..DEMO-020`) e, dalla STORY-521, assegna i ticket SOLO all'utente `DemoUsersSeeder::CITIZEN_EMAIL` e salta tutto se manca. Il test creava un utente casuale con `User::factory()`: il seeder non produceva nulla (0 ticket), 5 test su 9 fallivano (conteggio 15, codici, stati non vuoti) e gli altri 4 passavano **a vuoto** (cicli su una collezione vuota). In piu' il test dichiarava `RefreshDatabase` (un `migrate:fresh` sul database configurato se lanciato con `pest` nudo) e lasciava che il seeder riscrivesse il file pubblico `public_html/data/tickets.json` tramite `GenerateTicketsJsonAction`. Il riferimento `@see STORY-135-...` non esiste in nessuna cartella docs.
2. **Job test**: scritto da un rapporto di completamento, non dal codice. `docs/IMPLEMENTATION_SUMMARY_2025.md` dichiara «Task #1 COMPLETATO» per `Modules/Fixcity/app/Jobs/GeocodeTicketAddressJob.php`, una migrazione `address` e «7 test», ma nessuno dei due file di produzione e' mai esistito: `docs/GAP_ANALYSIS_IMPLEMENTATION.md` lo elencava come `[NEW]` (da creare) e `Ticket` non ha ne' colonna ne' attributo `address` (l'indirizzo vive in `location`, e `NormalizeTicketLocationDataAction` fa `unset($state['address'])`). Il test usava anche `Log::fake()` e `Log::assertLogged()` (inesistenti nella facade) e `Ticket::factory()` (restituisce `mixed` con `HasXotFactory`).

## Prove che il job non esisteva

- `git -C laravel/Modules/Fixcity log --all -S'GeocodeTicketAddressJob'` trova un solo commit, `aad41cd` (`refs/remotes/laraxot/dev`, 2026-10-15 14:12 +0200, autore marco76tv, «.»), che **aggiunge soltanto** i due file di test (337 inserzioni, nessun file in `app/`). Il ref non e' contenuto nel `dev` locale. Nessun commit ha mai avuto il file in `app/Jobs/`.
- Fuori dal modulo la classe compare in due copie dello stesso test (`laravel/tests/Unit/Modules/Fixcity/Jobs/GeocodeTicketAddressJobTest.php`, `laravel/tests/Feature/Feature/Jobs/GeocodeTicketAddressJobTest.php`, entrambe del 2026-09-24, fuori dallo scope di `analyse Modules`) e nei documenti di pianificazione `docs/IMPLEMENTATION_SUMMARY_2025.md` e `docs/GAP_ANALYSIS_IMPLEMENTATION.md` (radice) e in `Modules/Notify/docs/`.
- Cio' che esiste di vicino: nel browser `Modules/Geo/resources/js/components/map/reverse-geocode.js` (`fetchNominatimReverse`, usata dal selettore di coordinate) compila l'indirizzo che finisce in `location`; lato server `Modules\Geo\Actions\Nominatim\ReverseGeocodeAction::execute(float $latitude, float $longitude): LocationData` (Guzzle diretto: senza cache, senza retry, senza log, non intercettabile con `Http::fake()`), con **zero chiamanti** in Fixcity. `TicketCreatedListener` imposta solo lo stato `PENDING`. Nessun punto del codice PHP (Modules, app, routes, config) invia `GeocodeTicketAddressJob`.

## Cosa ho fatto

### TicketDatabaseSeederTest.php (scritto da me, con lock)
- Utente cittadino reale: `UserFactory::new()->create(['email' => DemoUsersSeeder::CITIZEN_EMAIL])`.
- 15 diventa 20, codici `DEMO-001..DEMO-020` (il contratto e' `presentationRecords()`, 20 voci tutte nel riquadro 45.540-45.570 / 12.230-12.270).
- Guardia contro i passaggi a vuoto: `seededDemoTickets()` pretende una collezione non vuota prima di iterare.
- `uses(Modules\Fixcity\Tests\TestCase::class)` (DatabaseTransactions) al posto di `Tests\TestCase` + `RefreshDatabase`: il DB serve davvero, ma senza `migrate:fresh`.
- `GenerateTicketsJsonAction` sostituita con un mock (`atLeast()->once()`, prova anche che il mock e' agganciato) e `Storage::fake('public')`: il test non riscrive piu' `public_html/data/tickets.json`.
- Nessuna `assert()`, nessun `@var`, nessun cast per tacere. `@see` ora punta a STORY-521 e a questa story. Alle 13:38:41 UTC un altro attore (non io) aveva gia' ridotto i 2 errori originali (`UserFactory::new()` e `resolveTicketStatusValue()`); li ho mantenuti.

### GeocodeTicketAddressJobTest.php: NON toccato da me
Il test e la nuova classe `app/Jobs/GeocodeTicketAddressJob.php` erano sotto lock `phpstan-swarm-coord` (agenti `fixcity-geocode-test` e `fixcity-geocode-job`, 13:37:13) e sono stati scritti da un'altra sessione (13:39:04 la classe, 13:40:54 il test; story `geocode-ticket-address-job-location-json.story.md`, riga in `docs/wiki/log.md`). L'incarico di questa story diceva di NON creare produzione e di riportare «SENZA SORGENTE»: il fatto e' stato superato dall'altra sessione. Non ho rubato il lock.

## Verifica (output reali)

| Misura | Prima | Dopo |
|---|---|---|
| PHPStan `GeocodeTicketAddressJobTest.php` (13:38 UTC, 2 file) | 47 errori, exit 1 | non mio; 0 errori alle 13:47 con la classe creata dall'altra sessione |
| PHPStan `TicketDatabaseSeederTest.php` | 2 (misura epic 13:34); 0 alle 13:38 dopo il ritocco altrui | 0, exit 0, `totals.file_errors` 0 (13:46) |
| Pest `TicketDatabaseSeederTest.php` (scratch DB) | 5 falliti, 4 passati a vuoto (19 asserzioni) | 9 passati (312 asserzioni) |
| Pest `GeocodeTicketAddressJobTest.php` (stato altrui, 13:47) | non eseguibile (classe assente) | **7 falliti**: `RuntimeException: A facade root has not been set` (il file non dichiara `uses(TestCase::class)`) |

Comandi: `export DB_DATABASE_LIMESURVEY= APP_CONFIG_CACHE=/nonexistent/config.php; bash bashscripts/tools/heavy-slot.sh bash bashscripts/tools/pest-scratch-db.sh <test>`. Database di sviluppo, prima e dopo, interrogato in sola lettura: `users` 11, `tickets` 18, `tickets` con prefisso DEMO 0 (invariati). `public_html/data/tickets.json` md5 `533e5e4d0eb35df629e2d7cfcffa3b01` prima e dopo (il vecchio test, con il cittadino presente, lo avrebbe riscritto). Nessun database `claude_scratch_pest*` residuo.

Limiti: PHPStan su soli due file non prova le soglie `typeCoverage.*` (servono `analyse Modules` interi); la soglia non e' stata misurata.

## Decisioni aperte (proposta)

1. **Il job serve?** Lato server nessuno invia la geocodifica; il browser la fa nel selettore. Proposta: decidere con il prodotto se serve un recupero dell'indirizzo per ticket con coordinate e senza indirizzo (import, API, app). Se no, la classe e i suoi test restano senza chiamanti.
2. **Se si**: (a) riusare `ReverseGeocodeAction` di Geo invece di duplicare la chiamata Nominatim, oppure renderla testabile con `Http`; (b) logica nuova in Spatie QueueableAction, non in un Job con logica (regola del repo); (c) un solo punto di invio (observer/listener di creazione) con test.
3. **Il test del job oggi e' rosso a runtime** pur con PHPStan a zero: serve `uses(Modules\Fixcity\Tests\TestCase::class)`; inoltre usa `Assert::assertInstanceOf()` per restringere i tipi, da verificare contro il divieto di asserzioni usate per far tacere PHPStan. Resta alla sessione che ne ha il lock (o al lead).
4. Le due copie fuori modulo del test (`laravel/tests/...`) referenziano la stessa classe: da allineare o ritirare con approvazione (non si cancellano test senza approvazione).

## Lezioni

- Un rapporto che dichiara «COMPLETATO» non e' una prova: qui descriveva un job e una migrazione mai esistiti.
- PHPStan a zero non prova che un test funzioni: il job test e' verde per PHPStan e rosso 7/7 a runtime.
- Un test che itera su una collezione puo' passare a vuoto: pretendere prima che non sia vuota.
