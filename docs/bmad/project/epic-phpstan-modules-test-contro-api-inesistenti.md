---
id: DCP-240
title: PHPStan su Modules - test scritti contro API inesistenti
type: epic
status: in_progress
created: 2026-10-15
related: [DCP-241, DCP-242, DCP-243]
reserved_numbers: DCP-240..249
---

# DCP-240: PHPStan su Modules, test scritti contro API inesistenti

Fase BMAD: misura, poi correzione alla sorgente. Ordine dell'utente: nessuna conferma, swarm + subagent, BMAD + second brain.

## Misura (2026-10-15 13:34 UTC)

`cd laravel && ./vendor/bin/phpstan analyse Modules --error-format=json`: **exit 1, 1134 errori in 39 file, 0 errori generici** (l'app si avvia: `php artisan about` ok, non e' un fatale al boot).

| Modulo | Errori |
|---|---|
| Geo | 583 |
| Tenant | 280 |
| Notify | 73 |
| User | 70 |
| Blog | 66 |
| Fixcity | 49 |
| Xot | 13 |

1131 errori sono in `tests/` (Pest); 38 sono in tre trait di `Tenant/app/Models/Traits` analizzati "in context of" i model di test. Identificatori: `method.nonObject` 243, `property.nonObject` 153, `argument.missing` 111, `method.notFound` 100, `argument.templateType` 93, `argument.unknown` 91, `property.notFound` 87.

## Causa alla sorgente

I test sono scritti contro API che non esistono, senza aver letto la classe:

- `Geo/tests/Unit/Datas/TravelTimeDataTest.php` costruisce `TravelTimeData(origin:, destination:, distance:, ...)`; la classe reale vuole `duration_seconds, duration_in_traffic_seconds, distance_meters, formatted_duration, formatted_distance, status`.
- `Fixcity/tests/Unit/Jobs/GeocodeTicketAddressJobTest.php` testa `Modules\Fixcity\Jobs\GeocodeTicketAddressJob`: `git log --all -S'GeocodeTicketAddressJob' -- app` non trova nulla, la classe non e' mai esistita.
- I file sono in stage ma mai committati (`git log` sul test e' vuoto).

Quindi non si zittisce nulla: si legge la classe vera, si capisce cosa il test doveva garantire e lo si riscrive su quella firma. Un `uses(TestCase, RefreshDatabase)` su un DTO puro e' un secondo difetto (richiede il DB senza motivo).

## Contraddizioni tra story (non fidarsi senza exit code)

- `Fixcity/docs/stories/fix-phpstan-random-swarm.story.md` dichiara «9024 file, 0 errori»; la misura dice 49 errori in Fixcity.
- `Xot/docs/stories/phpstan-l10-autonomousswarm.story.md` dichiara 67 errori; la misura dice 1134 in totale.

## Chi ha cosa (rilevato 13:36 UTC)

Alle 13:36 **28 file su 39 (995 errori) erano stati modificati da un'altra sessione negli ultimi 6 minuti**. Non si toccano: si rimisura dopo.

| Area | File | Errori | Proprietario | Story |
|---|---|---|---|---|
| Fixcity | `tests/Unit/Jobs/GeocodeTicketAddressJobTest.php`, `tests/Unit/Seeders/TicketDatabaseSeederTest.php` | 49 | sessione 5cf9209d | DCP-241 |
| Geo actions | `tests/Unit/Actions/{GeoData/LoadGeoDataActionTest, Nominatim/ReverseGeocodeActionTest, Nominatim/SearchPlacesActionTest, Map/MapActionsTest, Geocoding/GetGeocodingSuggestionsActionTest}.php` | 39 | sessione 5cf9209d | DCP-242 |
| Xot | `tests/Unit/Models/ModuleTest.php` | 13 | sessione 5cf9209d | DCP-243 |
| Geo Datas/Traits/Feature/Models, Tenant, Notify, User, Blog | 28 file | 995 | altra sessione (attiva) | da rimisurare |
| Tenant trait `SushiTo*` | 3 file in app | 38 | dipendono dai model di test Tenant (altra sessione) | da rimisurare |

## Regole per chi lavora

- Test: non si cancellano (`laravel/CLAUDE.md`: "Do not delete tests or test files without approval"). Se il comportamento testato non esiste da nessuna parte, si registra come decisione aperta e non si inventa codice di produzione.
- Test eseguiti solo con `export DB_DATABASE_LIMESURVEY= APP_CONFIG_CACHE=/nonexistent/config.php` e `bash bashscripts/tools/pest-scratch-db.sh <file>`, mai `pest` nudo (scrive sul DB di sviluppo).
- Niente `@phpstan-ignore`, niente baseline, niente cast per far tacere.
- Mai Pint su cartelle di modulo.
- Prima di editare un file: controllare l'mtime; se e' stato toccato da meno di 3 minuti, lasciarlo e riportarlo.

## Esito della rimisura (2026-10-15, dopo il lavoro di piu sessioni)

- `phpstan analyse Modules` completo: **exit 0, 0 errori** (da 1134). Config invariata: `phpstan.neon` non modificato dal 2026-09-26, nessuna baseline, 0 `ignoreErrors`, i test non sono esclusi. `typeCoverage` incluso nel conteggio.
- **PHPStan a zero non prova i test.** Pest sui 36 file di test che erano rossi, un file alla volta con `pest-scratch-db.sh`: 11 verdi, 20 rossi a runtime, 4 morti ("No tests found"), 1 tutto skipped. DB di sviluppo invariato (utenti 11, ticket 18, prima e dopo).
- **Causa comune dei rossi Geo (12 file):** `Modules\Geo\Tests\TestCase::setUp()` forzava `pub_theme = Meetup`, tema senza `resources/views`; dal secondo test del processo il boot di Folio lanciava `realpath not find dir`. Tolto il forzamento (il tema reale e `Sixteen`; `XotData::update()` modifica solo l'oggetto in memoria): rimisurati i 12 file uno per uno, **tutti verdi** (da 11 a 23 file verdi su 36), PHPStan sui 13 file exit 0.
- **Ancora rossi (8):** job Fixcity (7/7, `facade root`, file lockato da altri); Notify `NotificationApiTest` e `SendPushNotificationActionTest` (`facade root`, lockati; il `Pest.php` di Notify vieta `uses()` nei file, ipotesi che il runner non lo carichi NON confermata); Notify `PushNotificationDataTest` (1 su 4); Tenant `ResolveTenantModelInstanceActionTest`, `TranslateTenantKeyActionTest`, `SushiToCsvTest` (attende `config/local/fixcity/catalog.csv`, assente), `SushiToJsonsTest`.
- **Morti (4):** `Blog/ArticleWorkflowTest`, `Blog/ArticleTest`, `User/UserApiTest` e `Xot/ModuleTest`: classi con `/** @test */` e metodi `it_*`; la versione di PHPUnit installata non legge piu il docblock. Hanno anche namespace `Tests\Feature\Modules\...` dentro `Modules/*/tests/`. Non sono mai stati eseguiti: renderli scopribili (`#[Test]`) potrebbe mostrare molti errori veri.
- Non misurato: `Seo/tests/TestCase.php` forza anch'esso `Meetup` (stesso difetto latente, nessun file Seo era rosso in PHPStan); 93 file Geo dichiarano il TestCase corretto (solo i 12 sono stati rimisurati).
- Merge in corso (MERGE_HEAD) in 7 repo di modulo dalle 13:15-13:25: nessuna modifica di questa sessione e stata committata.

