---
title: "Fixcity — Audit test, CI e operatività"
type: audit
tags: [fixcity, audit, quality]
created: 2026-11-05
updated: 2026-11-12
status: active
related:
  - ./README.md
  - ./COMPLETION_ROADMAP.md
---

> Analisi statica del branch `dev` del 2026-11-05: niente vendor/, PHPStan e Pest non eseguiti in questo ambiente.
> Lo stato aggiornato di ogni rilievo (risolto/aperto) è in [COMPLETION_ROADMAP.md](./COMPLETION_ROADMAP.md).

# Audit qualità, test, CI/CD e operatività — Fixcity

## Aggiornamento operativo — 12 novembre 2026

L'audit front-office ha rilevato che `deploy.sh` eliminava tutte le migration con
`rm -rf database/migrations/*` prima di eseguire `artisan migrate`. La riga è stata
rimossa: le migration versionate devono restare nel checkout per mantenere deploy,
audit dello schema e correzioni forward-only riproducibili. Il controllo resta da
verificare su un deploy staging/live, perché questa sessione non ha accesso SSH al
server di produzione.

## Sintesi

Il codice di test locale è ricco e in parte di buona qualità: 1.477 file `*Test.php` nei moduli, con test seri su wizard, upload e policy di Fixcity. **La pipeline però non li esegue e non blocca mai nulla.**

- `laravel/phpunit.xml` include solo `laravel/tests/{Unit,Feature}`, circa 60 file. I test dei moduli non fanno parte di nessuna testsuite.
- Tutti i gate di CI che girano su `dev` terminano con `|| true`.
- Con alta probabilità la CI non installa nemmeno le dipendenze dei moduli: manca `wikimedia/composer-merge-plugin` in `require` e non c'è un lockfile.
- Il deploy esegue `composer update -W` in produzione, senza backup, senza manutenzione e senza rollback.
- Non c'è error tracking e non c'è una configurazione di worker o di cron.
- I rapporti "PHPStan 0 errori" si contraddicono fra loro e citano una baseline che è un file vuoto.

Legenda gravità: Critica / Alta / Media / Bassa. Tipo: **BD** = bug dimostrato, **AP** = altamente probabile, **DT** = debito tecnico, **IP** = ipotesi.

---

## 1. Test

### Inventario per modulo

Conteggio dei file `*Test.php` sotto `tests/`, con i `.spec.js` Playwright a parte.

| Modulo/Tema | Totale | Feature | Unit | Browser (Dusk) | Playwright |
|---|---|---|---|---|---|
| Fixcity | 196 | 115 | 81 | 0 | 14 (8 in `tests/Playwright`) |
| Xot | 205 | 14 | 190 | 0 | 0 |
| User | 177 | 62 | 115 | 0 | 2 |
| Cms | 150 | 61 | 89 | 0 | 0 |
| Notify | 134 | 16 | 118 | 0 | 0 |
| Geo | 108 | 5 | 103 | 0 | 34 |
| Activity | 100 | 20 | 79 | 0 | 0 |
| Sixteen (tema) | 58 | 19 | 39 | 0 | 0 |
| UI | 52 | 20 | 32 | 0 | 0 |
| Job / Media | 46 / 46 | 10 / 1 | 36 / 39 | 0 | 0 |
| Tenant | 41 | 5 | 32 | 0 | 0 |
| Gdpr / Rating / Lang / Seo / Comment | 38 / 30 / 26 / 21 / 20 | 13 / 1 / 3 / 3 / 5 | 25 / 29 / 23 / 18 / 15 | 0 | 0 |
| AI / Blog / Mobile / TwentyOne | 12 / 8 / 4 / 2 | 0 / 6 / 0 / 0 | 12 / 2 / 4 / 2 | 0 | 0 |
| Event, Barthelemy, Meetup | nessuna cartella `tests` | | | | |
| `laravel/tests` (radice app) | 77 file PHP | Auth 6, Pages 1, Themes 1 … | Modules/* (Geo, Notify, Tenant, Chart …) | — | — |

Altri numeri: in tutto ci sono 135 `markTestSkipped` in 65 file, 56 `->skip(` in 25 file, 74 `->todo()` in 36 file (52 in Cms) e 6 `markTestIncomplete` (Xot). Non esistono test Dusk né `tests/Browser`.

### Tabella "test per percorso critico"

| Percorso critico | Test esistenti (path) | Giudizio |
|---|---|---|
| Creazione ticket dal wizard | `Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardWidgetTest.php`, `CreateTicketWizardIdempotentSubmitTest.php` (verifica 1 solo ticket dopo doppio submit, righe 92-101), `CreateTicketWizardInputValidationTest.php`, `CreateTicketWizardSessionExpiredTest.php`, `CreateTicketWizardTamperingTest.php`, `CreateTicketWizardDataStepTest.php`, `Feature/Wizard/*`, `Unit/Actions/CreateTicketConfirmationFlowTest.php`; E2E `tests/Playwright/ticket-create-e2e.spec.js`, `ticket-create-wizard.spec.js` | **Buono come contenuto**, ma non gira in CI (QA-01) e dipende da un DB SQLite condiviso pre-costruito (QA-03). L'E2E è manuale, con baseURL su un IP di LAN (`dcp146-step-dati-http.spec.js:8-9`). |
| Permessi / policy | `Fixcity/tests/Unit/Policies/TicketPolicyTest.php` (6 casi, con `Ticket` anonimo), `Feature/Filament/AssignTicketOperatorsOnlyTest.php`, `TicketDeletionAuthorizationTest.php`, `Feature/Policies/PermissionLookupWritesOnceTest.php`, `User/tests/Unit/Policies/UserPolicyMatrixTest.php`, `UserActionsPolicyVerdictTest.php`, `Media/tests/Unit/MediaPolicyTest.php`, `Rating/tests/Unit/RatingPolicyRolesTest.php` | **Discreto.** Copre la matrice ruoli di Fixcity. Mancano test HTTP end-to-end sul pannello Filament per ruolo (403 su URL diretti). |
| API GeoJSON | `Fixcity/tests/Feature/Api/TicketsGeoJsonApiTest.php` (5 casi), `Unit/Actions/ValidateTicketsGeoJsonActionTest.php`, `Geo/tests/Playwright/map-lit-markers-tickets-json.spec.js` e altri 8 spec | **Parziale.** Il primo caso (righe 15-27) controlla solo la struttura su dati "live". Nessun test dimostra che ticket non pubblici o dati personali (nome/email dell'autore) restino fuori dalla FeatureCollection (QA-06). |
| Notifiche | `Fixcity/tests/Feature/Actions/TicketReceivedNotificationTest.php`, `TicketLifecycleNotificationTest.php`, `TicketAssignmentNotificationTest.php`, `TicketStatusNotificationTest.php`, `TicketReceiptNotificationTest.php`, `FollowerNotificationHidesTrackingCodeTest.php`, `Unit/Notifications/NotificationRetryPolicyTest.php`; 134 test in Notify | **Buono** per Fixcity. In Notify molti file sono "coverage boost" (`NotifyRemainingCoverageTest`, `NotifyHighestMissCoverageTest`) che saltano se il DB non c'è (`NotifyHighestMissCoverageTest.php:155`). |
| Isolamento tenant | `Tenant/tests/Feature/MultiTenantScopeTest.php`, `TenantConfigIsolationTest.php` (path traversal) | **Debole, quasi tautologico.** `MultiTenantScopeTest.php:20-60` filtra con `where('tenant_id', …)` scritto nel test stesso e il caso a riga 41 verifica che **non** ci sia uno scope globale. Tutta la suite salta se il DB non è scrivibile (righe 14-17) (QA-05). |
| Upload | `Fixcity/tests/Feature/Filament/CreateTicketWizardForgedUploadTest.php` (12 casi: MIME falsificato, JPEG corrotto, rimozione GPS), `WizardUploadRestrictionTest.php`, `TicketFormImagesUploadTypesTest.php`, `Privacy/TicketPhotoMetadataTest.php`, `Media/tests/Unit/Actions/SaveAttachmentsActionTest.php`, `Image/StripImageMetadataActionTest.php`, `Rules/FileExtensionRuleTest.php` | **Buono**: è la parte più solida. Non gira in CI. |
| Login / autenticazione | `User/tests/Feature/Filament/Widgets/Auth/LoginWidgetTest.php`: 4 casi `->skip` (righe 60, 81, 94, 115) e 1 skip condizionale (riga 41); `laravel/tests/Feature/Auth/*` (6) | **Insufficiente**: il widget di login è di fatto non testato. |
| Retention GDPR (scheduler) | `Fixcity/tests/Feature/Privacy/RetentionScheduleTest.php`, `PurgeDeletedAccountsActionTest.php` | Buono come logica. In produzione dipende da un cron che non esiste nel repository (OPS-14). |

### Rilievi sui test

#### QA-01 — I test dei moduli non fanno parte di nessuna testsuite: la CI esegue circa 60 file su oltre 1.500
- **Gravità**: Critica. **Tipo**: BD (configurazione).
- **Evidenza**: `laravel/phpunit.xml:7-17` dichiara solo `tests/Unit` (escluso `tests/Unit/Modules/Chart`) e `tests/Feature`. `laravel/phpunit-parallel.xml:27-59` aggiunge solo `tests/Integration` e altre sottocartelle di `laravel/tests`. Nessun `Modules/*/tests` e nessun `Themes/*/tests`. La CI lancia `php artisan test` senza percorsi (`.github/workflows/ci.yml:145`, `comprehensive-quality.yml:159`, `tests.yml:88`). L'unico workflow che lancia Pest su un modulo è `module-quality-check.yml:45` (Notify), che ha un filtro sui path sbagliato (OPS-05). Inoltre `tests/Integration` non è nella suite principale.
- **Impatto**: circa 1.477 test dei moduli e del tema, compresi tutti quelli di Fixcity, non vengono mai eseguiti in automatico. Le regressioni arrivano su `dev` senza segnale.
- **Correzione**: in `phpunit.xml` aggiungere le testsuite `Modules` (`<directory>Modules/*/tests/Unit</directory>`, `…/Feature`) e `Themes` (`Themes/*/tests`). In alternativa, in CI, una matrice con `vendor/bin/pest Modules/<M>/tests` per modulo. Rimuovere i test orfani di `tests/Unit/Modules/Chart`.
- **Criterio di accettazione**: `vendor/bin/pest --list-tests | wc -l` include i test di `Modules/Fixcity`, e il job CI riporta più di 1.400 test eseguiti.
- **Confidenza**: 90. La configurazione è letta direttamente. Pest esegue un percorso passato a mano anche fuori dalle suite, ma la CI non passa percorsi.

#### QA-02 — Le suite si auto-saltano quando il DB non c'è o non è migrato (verde falso)
- **Gravità**: Alta. **Tipo**: BD.
- **Evidenza**:
  - `Modules/Tenant/tests/TestCase.php:101-109`: `markTestSkipped` su "database is locked" e "no column named".
  - `MultiTenantScopeTest.php:14-17`.
  - `User/tests/Feature/Filament/Widgets/Auth/LoginWidgetTest.php:41`: "users table missing … run migrations".
  - `RegisterWidgetTest.php:22`, `TeamChangeWidgetTest.php:51`, `SuperAdminWidgetTest.php:101`, `PassportDashboardNewCredentialsTest.php:74,85`, `Notify/tests/Unit/NotifyHighestMissCoverageTest.php:155`.
  - In tutto 70 file di test fanno controlli tipo "table missing / hasTable".
  - Il DB di test è un SQLite condiviso, scelto da `Modules/Xot/tests/XotBaseTestCase.php:273-306` e costruito da `xot:build-test-sqlite`. Nessun `*.sqlite` è tracciato in `laravel/database/` (c'è `database/.gitignore`) e nessun workflow lancia quel comando.
  - `ci.yml` non esegue migrazioni e usa il DB `laravelpizza_test` (righe 91, 142), copiato da un altro progetto.
- **Impatto**: in CI, o su una macchina nuova, buona parte dei test risulta "skipped" invece che "failed". Il verde non dice niente.
- **Correzione**: in CI, prima dei test, costruire il DB (`php artisan xot:build-test-sqlite` o `migrate --env=testing`). Trasformare gli skip "tabella mancante" in fallimenti quando `CI=true`. Far fallire la pipeline se gli skip superano una soglia (`--fail-on-skipped` su un sottoinsieme critico).
- **Criterio di accettazione**: sui moduli Fixcity, User e Tenant il job CI mostra 0 skip dovuti a DB o tabelle mancanti.
- **Confidenza**: 85.

#### QA-03 — Skip obsoleti e test tautologici
- **Gravità**: Media. **Tipo**: BD.
- **Evidenza**:
  - `Themes/Sixteen/tests/Feature/ComunePagesTest.php:15,33,53,143` salta con "Modulo Fixcity assente in questa base", ma `Modules/Fixcity` esiste.
  - `laravel/tests/Unit/ExampleTest.php:14` contiene `assertTrue(true)`.
  - `laravel/tests/Feature/Pages/ListaCategorieTest.php:21` contiene `expect(true)->toBeTrue(); // Always pass`.
  - `Modules/Cms/tests/Feature/Auth/PasswordUpdateTest.php:8,12` è un file intero di sole `assertTrue(true)`.
  - `laravel/tests/Feature/Themes/Sixteen/ListingComponentsTest.php:24,68,102,121` salta per un problema di DatabaseTransactions.
  - `User/tests/Feature/Filament/Pages/ListUsersTest.php:130-142` ha 7 skip del tipo "not implemented".
  - 21 commenti "assertTrue(true) removed — tautology" in User (`AdditionalDatasTest.php`, `AdditionalActionsTest.php`) lasciano test con il solo `class_exists`.
  - 52 `->todo()` in Cms.
- **Impatto**: copertura apparente gonfiata. Test di pagine del Comune spenti per un motivo che non è più vero.
- **Correzione**: rimuovere i test tautologici. Riattivare `ComunePagesTest`. Convertire i `todo` in story BMAD oppure cancellarli.
- **Criterio di accettazione**: `rg "assertTrue\(true\)|expect\(true\)->toBeTrue" Modules Themes tests` non trova niente, e nessuno skip cita la mancanza di un modulo presente.
- **Confidenza**: 95.

#### QA-04 — Cartelle di test duplicate per maiuscole e file randagi
- **Gravità**: Bassa. **Tipo**: BD.
- **Evidenza**:
  - `Modules/Media/tests/` contiene sia `Feature`/`feature`, sia `Unit`/`unit`, sia `Filament`/`filament`. `tests/Feature/MediaBusinessLogicTest.php` e `tests/feature/MediaBusinessLogicTest.php` hanno contenuto diverso.
  - `Modules/Cms/tests/` contiene sia `Pest.php` sia `pest.php`.
  - C'è una cartella randagia `laravel/laravel/Modules/Rating/tests/Fixtures/*` (10 file).
- **Impatto**: il checkout su macOS o Windows (filesystem case-insensitive) produce collisioni e modifiche fantasma. Possibili test duplicati.
- **Correzione**: unificare in PascalCase e cancellare `laravel/laravel/`.
- **Criterio di accettazione**: `git ls-files | tr A-Z a-z | sort | uniq -d` restituisce un output vuoto.
- **Confidenza**: 95.

#### QA-05 — Isolamento tenant non verificato sul codice applicativo
- **Gravità**: Media. **Tipo**: AP.
- **Evidenza**: `Modules/Tenant/tests/Feature/MultiTenantScopeTest.php:20-60`. I filtri `where('tenant_id', …)` sono scritti nel test e il caso "keeps rows separable without global scope" (riga 41) verifica il **contrario** dell'isolamento automatico. Non esiste un test che, con il tenant A attivo, cerchi di leggere un ticket o un media del tenant B tramite le route o le query dell'app.
- **Impatto**: un'eventuale fuga di dati fra Comuni non verrebbe rilevata.
- **Correzione**: test feature che risolve il tenant da host o config, crea dati su due tenant, chiama `/api/tickets/geojson`, `/api/ticket-details/{id}` e il pannello, e verifica che l'altro tenant non sia visibile.
- **Criterio di accettazione**: il test fallisce se si rimuove il meccanismo di separazione (connessione per tenant o scope).
- **Confidenza**: 70. L'architettura per tenant (connessione e config per dominio) potrebbe rendere l'isolamento strutturale, ma oggi nessun test lo dimostra.

#### QA-06 — L'API GeoJSON non ha test di non-esposizione
- **Gravità**: Media. **Tipo**: AP.
- **Evidenza**: `Modules/Fixcity/tests/Feature/Api/TicketsGeoJsonApiTest.php`. Il caso a righe 15-27 controlla solo la struttura. I casi successivi riguardano `/api/ticket-details`, cioè il codice di accesso. Nessuna verifica che la FeatureCollection escluda gli stati non pubblici e i campi personali.
- **Impatto**: una regressione che espone segnalazioni non moderate o dati personali sulla mappa pubblica passerebbe inosservata.
- **Correzione**: dataset con ticket PENDING, privati e pubblici; asserzioni sugli id presenti e sull'elenco esatto delle `properties` ammesse.
- **Criterio di accettazione**: il test fallisce se `properties` contiene `owner`, `email` o `code`, o se compare un ticket non pubblico.
- **Confidenza**: 80.

#### QA-07 — I test Playwright (50 spec) sono fuori CI e legati alla rete locale
- **Gravità**: Bassa. **Tipo**: DT.
- **Evidenza**: le configurazioni sono `Modules/Fixcity/playwright.config.js`, `Modules/Geo/playwright.config.js` e `Modules/User/playwright.config.js`. Nessun workflow nomina playwright. `dcp146-step-dati-http.spec.js:8-9,161` richiede `PLAYWRIGHT_BASE_URL=http://192.168.x.x:8000`.
- **Correzione**: un job E2E notturno con `php artisan serve` su un DB seed, limitato agli spec funzionali e senza quelli di confronto visivo.
- **Criterio di accettazione**: `ticket-create-e2e.spec.js` gira verde in CI su `dev`.
- **Confidenza**: 90.

#### QA-08 — `.env.testing` usa credenziali di sviluppo ed è generato da uno script assente
- **Gravità**: Bassa. **Tipo**: BD.
- **Evidenza**: `laravel/.env.testing:1-3` dice "Generato da sync-env-testing.sh", ma `bashscripts/` non esiste (OPS-20). Righe 16-27: `DB_CONNECTION=mariadb`, `marco`/`marco`, `fixcity_data_test`. `APP_KEY` è fittizia (riga 7), il che va bene.
- **Correzione**: un `.env.testing` neutro (sqlite) e documentato, e lo script versionato o rimosso.
- **Confidenza**: 90.

---

## 2. Static analysis

#### QA-09 — I rapporti "PHPStan 0 errori" sono contraddittori e citano una baseline vuota
- **Gravità**: Alta, perché sono i rapporti su cui si decide. **Tipo**: BD (documentazione non vera).
- **Evidenza**:
  - `PHPSTAN_SUCCESS_REPORT.md:11` e `laravel/PHPSTAN_COMPLETE_SUCCESS.md:5,13` dicono "ZERO ERRORS".
  - `laravel/PHPSTAN_COMPLETE_SUCCESS.md:19` dice che le segnalazioni Pest sono "gestite tramite baseline".
  - `laravel/PHPSTAN_IMPLEMENTATION_COMPLETE.md:21-22` dice "Final Errors 26, Errors Baselined 19,135".
  - `laravel/PHPSTAN_KNOWN_ISSUES.md:5` dice "127 errors remaining".
  - `PHPSTAN_FINAL_REPORT.md:30` dice "Errori Test 13,982 (esclusi da config)".
  - In realtà `laravel/phpstan-baseline.neon`, `laravel/phpstan-modules-baseline.neon` e `phpstan-baseline.neon` alla radice sono **0 byte**, e l'include è commentato (`laravel/phpstan.neon:2`). L'esclusione dei test è commentata (`phpstan.neon:38`), quindi i test sono analizzati.
  - L'ultimo output reale, `laravel/.phpstan-r5-live.json` del 2026-10-20, riporta 2 errori di sintassi in `Notify/tests/Unit/NotifyRemainingCoverageTest.php:147-148`. Oggi `php -l` su quel file è pulito.
  - `laravel/phpstan_run.json` riporta 170 `file_errors`, ma con path di un'altra installazione (`base_fixcity_fila4_mono`).
- **Impatto**: lo stato reale di PHPStan non è noto. Il punto 2 del CLAUDE.md ("PHPStan a zero") non è verificabile con quanto c'è nel repository.
- **Correzione**: cancellare o archiviare i 7 rapporti `PHPSTAN_*`. Pubblicare come artifact CI l'output di `phpstan analyse --error-format=json` e citare solo quello.
- **Criterio di accettazione**: un job CI bloccante (senza `|| true`) con `phpstan analyse` verde, oppure una baseline versionata con il numero reale di errori.
- **Confidenza**: 95.

#### QA-10 — Perimetro di PHPStan: esclusi `app/` e i temi; ignore inline
- **Gravità**: Media. **Tipo**: DT.
- **Evidenza**: `laravel/phpstan.neon:16-19` analizza solo `./Modules/`. Restano fuori `./Themes/` (91 file PHP, di cui 87 in `Themes/Sixteen/app`) e `./app` (16 file). Nel codice ci sono 69 `@phpstan-ignore`, di cui 59 senza identificatore (`-next-line` / `-line`), concentrati nei test UI (`UI/tests/Unit/Models/ThemeModelTest.php`: 24). Nella cartella `laravel/` ci sono 10 varianti di configurazione (`phpstan1.neon`, `phpstan-temp.neon`, `phpstan-tmp-subagent.neon`, `phpstan-tests-fixed.tmp.neon`, …).
- **Correzione**: aggiungere `./Themes/*/app` e `./app` ai `paths`. Convertire gli ignore in `@phpstan-ignore <identifier>`. Lasciare una sola configurazione.
- **Criterio di accettazione**: `paths` include temi e app, e `rg -c "@phpstan-ignore-(next-)?line"` restituisce 0.
- **Confidenza**: 95.

#### QA-11 — Pint, Rector, PHPMD, Psalm e GrumPHP: configurati ma inefficaci
- **Gravità**: Bassa. **Tipo**: DT.
- **Evidenza**:
  - Pint: `laravel/pint.json` c'è. In CI gira con `|| true` (`ci.yml:81`, `comprehensive-quality.yml:68`), mentre `quality.yml:55` lo esegue **senza** `--test`, quindi corregge e poi scarta le modifiche.
  - Rector: `laravel/rector.php` ha tutti i livelli a 0 (`withTypeCoverageLevel(0)`, `withDeadCodeLevel(0)`, `withCodeQualityLevel(0)`), quindi non fa nulla. Include anche `__DIR__.'/themes'`, che non esiste.
  - Psalm: `psalm.xml` non esiste, ma `ci-cd.yml:76` lo usa.
  - GrumPHP: `grumphp.yml` c'è, ma `phpro/grumphp` non è fra le dipendenze.
  - PHPMD: `phpmd.xml` e `phpmd.ruleset.xml` ci sono, ma in CI si cerca un `phpmd.phar` che non esiste (`comprehensive-quality.yml:71`).
- **Correzione**: tenere Pint (bloccante con `--test`) e PHPStan. Rector con almeno `withPhpSets()` in dry-run nella CI. Rimuovere Psalm, GrumPHP e PHPInsights oppure configurarli davvero.
- **Confidenza**: 95.

#### QA-12 — Lint PHP: un errore fatale reale e uso di sintassi 8.4
- **Gravità**: Bassa. **Tipo**: BD.
- **Evidenza**: `php -l` (PHP 8.3) su 13.388 file PHP tracciati.
  - `laravel/compare-html.php:30` contiene `return $html;` in una funzione `void`: errore fatale anche su 8.4.
  - 5 file usano `new X()->m()` senza parentesi, sintassi valida **solo** da 8.4 e quindi non un bug: `Fixcity/app/Actions/CreateTicketAction.php:29`, `User/app/Models/Traits/HasTeams.php:205`, `Activity/tests/Unit/Listeners/LoginLogoutListenerBehaviorTest.php:31`, `Cms/tests/Unit/Http/Middleware/PageSlugMiddlewareTest.php:50`, `User/tests/Unit/UserModelTest.php:89`.
- **Impatto**: i workflow che usano PHP 8.3 o inferiore (`tests.yml:44`, `phpstan.yml:19`, `pint.yml:19`, `module-quality-check.yml:17`) non possono comunque compilare il codice.
- **Correzione**: rimuovere `compare-html.php`, che è uno script temporaneo, e allineare tutta la CI a PHP 8.4.
- **Confidenza**: 95.

---

## 3. CI/CD

Workflow che girano davvero su un push a `dev` (unico branch, `origin/HEAD -> origin/dev`):

| Workflow | Esito atteso |
|---|---|
| `ci.yml` | Verde sempre, per i `|| true`, se `composer install` riesce. Vedi OPS-01. |
| `comprehensive-quality.yml` | Verde sempre (`|| true`). Produce solo un artifact. |
| `tests.yml` | Rosso: PHP 8.3 contro `^8.4`, e `artisan` lanciato prima di `composer install`. |
| `semantic-release-advanced.yml` | Rosso: `npm ci` senza lockfile, script `bashscripts/` assente. |
| `psalm.yml`, `rector.yaml`, `phpinsights.yml` (`on: [push]`) | Rossi: PHP 8.1 sul `composer.json` di radice incoerente. Se funzionassero, farebbero auto-commit. |
| `phpstan.yml`, `pint.yml` (push su `**.php`) | Rossi: `composer install` alla radice, PHP 8.3, variabile `$mod` non definita. |
| `module-quality-check.yml` | Non parte mai: filtro `Modules/Notify/**` invece di `laravel/Modules/...`. |

Tutti gli altri workflow puntano a `main`, `master` o `develop`, che non esistono, oppure sono `.old`/`_test`, schedulati o manuali.

#### OPS-01 — `composer install` in CI non installa le dipendenze dei moduli
- **Gravità**: Critica. **Tipo**: AP.
- **Evidenza**: `laravel/composer.json:11-15` richiede solo `php`, `laravel/framework` e `nwidart/laravel-modules`. Il merge dei `Modules/*/composer.json` si affida a `extra.merge-plugin` (righe 65-69) e ad `allow-plugins` (riga 86), ma `wikimedia/composer-merge-plugin` **non è in `require` né in `require-dev`**, né qui né in un modulo (lo citano solo come `allow-plugins` Xot, Activity, Notify, Rating, UI, Gdpr, Fixcity e i temi). Non esiste nessun `composer.lock`: è ignorato da `.gitignore:10,119` (`*.lock`).
- **Impatto**: in CI mancano Filament, Livewire, Spatie e il resto. `artisan` e i test non partono, e i `|| true` nascondono l'errore.
- **Correzione**: aggiungere `"wikimedia/composer-merge-plugin": "^2.1"` in `require` di `laravel/composer.json` e versionare `laravel/composer.lock`.
- **Criterio di accettazione**: il job CI esegue `composer install` da lockfile e poi `php artisan about` senza errori.
- **Confidenza**: 80. Sulle macchine di sviluppo il plugin potrebbe essere installato globalmente, cosa che non posso vedere.

#### OPS-02 — Nessun gate blocca: `|| true` su PHPStan, Pint e test
- **Gravità**: Alta. **Tipo**: BD.
- **Evidenza**: `ci.yml:45,81,134,145`; `comprehensive-quality.yml:52,65,68,79,159`; `ci-cd.yml` con `|| true` più `continue-on-error: true` (righe 52-77); `quality.yml:41,55`.
- **Impatto**: la CI non può fallire, quindi dependabot auto-merge (OPS-06) e le PR passano sempre.
- **Correzione**: togliere `|| true` almeno da test e PHPStan in `ci.yml` e rendere il job richiesto nella branch protection di `dev`.
- **Criterio di accettazione**: un test volutamente rotto fa diventare rosso il check su una PR verso `dev`.
- **Confidenza**: 100.

#### OPS-03 — `tests.yml` è rotto per costruzione
- **Gravità**: Alta. **Tipo**: BD.
- **Evidenza**: `.github/workflows/tests.yml:44` usa `php-version: '8.3'` contro `"php": "^8.4"`. `php artisan key:generate` (riga 52) viene prima di `composer install` (riga 57). Usa Postgres (righe 14-25) mentre il progetto usa mariadb e sqlite. `phpstan --level=9` alla riga 74 e `--min=60` alla riga 88 non hanno `|| true`.
- **Correzione**: eliminarlo e farlo confluire in `ci.yml`.
- **Confidenza**: 95.

#### OPS-04 — Workflow che fanno auto-commit sul branch a ogni push
- **Gravità**: Alta. **Tipo**: AP.
- **Evidenza**: `psalm.yml:17-20` esegue `psalm --alter`, `rector.yaml:17-20` esegue `rector process` e `phpinsights.yml:16-19` esegue `phpinsights --fix`. Tutti e tre chiudono con `stefanzweifel/git-auto-commit-action@v7`, su `on: [push]` per qualsiasi branch, con PHP 8.1 e `composer install` alla radice. Il `composer.json` di radice si chiama `fixcity/user-module`, richiede `laravel/framework ^10` insieme a `filament/forms ^5` e `spatie/laravel-permission ^5`, e non è risolvibile.
- **Impatto**: oggi falliscono a ogni push, generando rumore rosso. Se venissero "riparati", farebbero modifiche automatiche al codice su `dev` senza revisione.
- **Correzione**: eliminare i tre workflow e il `composer.json` di radice, oppure renderlo un vero meta-progetto.
- **Confidenza**: 85.

#### OPS-05 — Circa 45 workflow: obsoleti, duplicati, trigger sbagliati
- **Gravità**: Media. **Tipo**: DT.
- **Evidenza**:
  - 9 file morti: `release-drafter.yml.old`, `release.yml.old`, `semantic-release.yml.old`, `semantic-versioning.yml.old`, `tag-version.yml.old`, `update-changelog.yml.old`, `module-release.yml_test`, `sync-remote-repo.yml_test`, `sync-subtrees.yml_test`.
  - Il rilascio è implementato 7 volte: `release-please.yml`, `semantic-release.yml`, `semantic-release-advanced.yml`, `semantic-release-module.yml`, `semantic-release-monorepo.yml`, `reusable-semantic-release.yml`, `module-release.yml`.
  - PHPStan gira in 6 workflow diversi.
  - `ci-cd.yml:12-15` usa PHP 8.2, Laravel 12.x e Filament 4.x, con `upload-artifact@v3` (righe 80, 136, 208, 272, 354, 389, 429), deprecato e non più funzionante. In più ha uno schedule notturno (riga 8).
  - `module-quality-check.yml:6` filtra su `Modules/Notify/**`, path che non esiste alla radice.
  - `quality.yml` copre solo Activity; `publish.yml` pubblica la documentazione di `module_geo`.
  - Workflow di marketing (`social-monetization.yml`, `virality-and-monetization.yml`, `stargazer-welcome.yml`) richiedono i segreti `TWITTER_*`, `MAILCHIMP_*`, `SALESFORCE_WEBHOOK`, `MARKETPLACE_API_KEY` e script `scripts/marketing/*` che non esistono.
  - Ci sono anche 23 `.github/workflows` annidati in `laravel/Modules/*` e `laravel/Themes/*`, che GitHub non esegue mai: in tutto 2.328 file sotto `.github` annidati, di cui 1.674 sono "skills" in Notify.
- **Segreti richiesti**: `FLUX_USERNAME`, `FLUX_LICENSE_KEY` (11 usi) più quelli di marketing; `GITHUB_TOKEN` con `contents: write` in `semantic-release-advanced.yml:11-13`.
- **Correzione**: tenere 3 workflow (`ci.yml` bloccante, `release` unico, `dependency-review`) ed eliminare il resto, compresi i `.github` annidati.
- **Criterio di accettazione**: `ls .github/workflows` restituisce al massimo 5 file, tutti con trigger su `dev`.
- **Confidenza**: 95.

#### OPS-06 — Dependabot sul manifest sbagliato e auto-merge senza gate
- **Gravità**: Media. **Tipo**: BD.
- **Evidenza**: `.github/dependabot.yml:3-4` usa `composer` con `directory: "/"`, cioè il `composer.json` di radice incoerente e non `laravel/`. Non c'è nessun ecosistema npm. `dependabot-auto-merge.yml:2` gira su `pull_request_target` con `contents: write` e fa auto-merge di minor e patch (righe 20-32), mentre i gate non falliscono mai (OPS-02).
- **Correzione**: `directory: "/laravel"`, aggiungere `npm` per `/laravel/Themes/Sixteen`, e auto-merge solo con check richiesti.
- **Confidenza**: 90.

---

## 4. Riproducibilità della build

#### OPS-07 — Nessun lockfile, `minimum-stability: dev`, 79 vincoli su 149 senza versione
- **Gravità**: Alta. **Tipo**: BD.
- **Evidenza**:
  - `.gitignore:10,119` ignora `*.lock` e `.gitignore:31` ignora `package-lock.json`.
  - `laravel/composer.json:89` ha `"minimum-stability": "dev"`.
  - Analisi dei `composer.json` di app, moduli e temi: 79 vincoli su 149 sono `*` o `dev-*`, per esempio `Modules/Notify/composer.json` (`aws/aws-sdk-php`, `illuminate/support`, `irazasyed/telegram-bot-sdk` …), `Modules/Media` (`intervention/image`), `Modules/Lang` (`mcamara/laravel-localization`).
  - `php` è dichiarato in quattro modi diversi fra i moduli (`^8.1`, `^8.2`, `^8.3`, `^8.4`).
  - Gli unici lock tracciati sono incidentali: `Modules/Geo/package-lock.json` e i `pnpm-lock.yaml` in `Themes/TwentyOne/Main_files`.
- **Impatto**: ogni install o deploy risolve versioni diverse, i bug non sono riproducibili e un rilascio maggiore di una dipendenza (`*`) entra senza controllo.
- **Correzione**: versionare `laravel/composer.lock` e `laravel/Themes/Sixteen/package-lock.json`, togliere `*.lock` dal `.gitignore` e mettere vincoli `^x.y` in tutti i moduli.
- **Criterio di accettazione**: `composer validate --strict` è pulito, `composer install` va a buon fine da lock in CI e `npm ci` funziona nel tema.
- **Confidenza**: 95.

#### OPS-08 — Dipendenze npm vulnerabili, non riproducibili e divergenti
- **Gravità**: Media. **Tipo**: BD (misurato).
- **Evidenza**: con `npm install --package-lock-only` più `npm audit` su copie in scratchpad:
  - `laravel/package.json`: 28 vulnerabilità (16 alte, 0 critiche), via `puppeteer`/`@puppeteer/browsers` (extract-zip con path traversal), `markdownlint-cli` (glob, js-yaml, markdown-it), `minimatch`, `zod`.
  - `package.json` di radice: 37 vulnerabilità (24 alte), anche `sharp` (libvips/libheif), `@opencode-ai/plugin`, `@kilocode/plugin` e `webdriver`. Inoltre dichiara 178 `dependencies` che sono dipendenze transitive appiattite (`acorn`, `ansi-styles`, `balanced-match` …).
  - `laravel/Themes/Sixteen/package.json` non si risolve: `mauve@^1.1.2` dipende da `x256: https://github.com/itsjoesullivan/node-x256/archive/master.tar.gz`, un tarball GitHub di `master` non fissato (qui bloccato con 403). `mauve` non risulta usato in `resources/js`, e anche `supermemory` è fra le `dependencies` del tema.
  - Versioni divergenti: `laravel/package.json` ha `vite ^6.4.2` e `tailwindcss ^3.4.13`; il tema Sixteen ha `vite ^8.3.4`, `tailwindcss ^4.3.3` e `laravel-vite-plugin ^3.2.0`; la radice ha `tailwindcss ^4.2.2`.
- **Correzione**: rimuovere `mauve`, `supermemory` e le dipendenze transitive dalla radice; spostare puppeteer e markdownlint in un tool separato; allineare le versioni di vite e tailwind.
- **Criterio di accettazione**: `npm ci && npm audit --audit-level=high` restituisce 0 nel tema Sixteen e in `laravel/`.
- **Confidenza**: 90 per laravel e radice; per Sixteen l'audit non è stato possibile.

#### OPS-09 — Asset compilati committati: manifest coerente, ma 21 bundle orfani e una cronologia pesante
- **Gravità**: Bassa. **Tipo**: DT.
- **Evidenza**:
  - `laravel/Themes/Sixteen/public/` ha 148 file tracciati per 27 MB; `Themes/TwentyOne/public` ne ha 25 per 14 MB.
  - `public/manifest.json`, aggiornato il 2026-11-05: 60 voci, 118 riferimenti, **0 file mancanti**, quindi coerente.
  - 21 file in `public/assets` non sono referenziati (11,5 MB: `app-test-*.css` ×6, `theme-components-*.js` ×4, `map-lit-*.js`, `app2.js` …), per via di `emptyOutDir: false` (`vite.config.js:70`).
  - `public/.vite/manifest.json` è residuo, del 2026-01-20.
  - Ci sono 4 configurazioni vite (`vite.config.js`, `.simple`, `.optimized`, `.css-only`).
  - Nella cronologia git ci sono 816 blob di asset compilati (25 MB).
- **Correzione**: build in CI e deploy; se si devono committare, ripulire `public/assets` prima di ogni build.
- **Criterio di accettazione**: lo script di verifica del manifest dà 0 orfani.
- **Confidenza**: 95.

---

## 5. Deploy e operatività

#### OPS-10 — `deploy.sh` non è sicuro per la produzione
- **Gravità**: Critica. **Tipo**: BD.
- **Evidenza** (`deploy.sh`):
  - Il `git pull` è commentato (righe 17-23): lo script non porta codice nuovo.
  - L'installer di composer è scaricato ed eseguito senza verificare lo SHA-384 (righe 28-29).
  - `composer update -W` in produzione, con l'output scartato (riga 35).
  - `vendor:publish --all` sovrascrive config e viste (riga 37).
  - `rm -rf database/migrations/*` (riga 38).
  - `migrate --force` senza backup e senza `artisan down` (riga 41).
  - Nessun `queue:restart`, nessuno smoke test o health check, nessun rollback.
  - `sudo chown -R www-data .` sull'intero repository (righe 54-55).
- **Impatto**: un deploy può portare in produzione versioni mai testate, cancellare personalizzazioni, lasciare worker con codice vecchio e non è reversibile.
- **Correzione**: rilascio atomico (cartelle `releases/<sha>` più symlink, o Deployer/Envoy), `composer install --no-dev` da lock, `artisan down`, backup del DB, `migrate --force`, `optimize`, `queue:restart`, `up`, `curl /up`, e rollback automatico se `/up` fallisce.
- **Criterio di accettazione**: il deploy di una release rotta torna da solo alla precedente entro 2 minuti.
- **Confidenza**: 95.

#### OPS-11 — Backup del DB inadeguato; nessun backup dei media né procedura di restore
- **Gravità**: Alta. **Tipo**: BD.
- **Evidenza**: `laravel/Modules/Xot/app/Console/Commands/DatabaseBackUpCommand.php:37-63`.
  - Fa il dump della **sola** connessione `mysql`, mentre l'app usa `mariadb` e più database (`DB_DATABASE`, `DB_DATABASE_USER`, connessioni `fixcity`, `user`, `media`, `comment` …).
  - La password passa sulla riga di comando (visibile con `ps`) e senza escaping.
  - Il codice di uscita non viene controllato (riga 63).
  - Il file ha nome giornaliero, quindi un secondo dump nello stesso giorno sovrascrive il primo, e resta in `storage/app/backup` sulla stessa macchina.
  - Non è schedulato (`routes/console.php:17-22`).
  - Non c'è backup di `storage/app` e dei media, né `spatie/laravel-backup`, né una procedura di restore.
- **Impatto**: perdita di segnalazioni e foto dei cittadini in caso di guasto o errore di migrazione.
- **Correzione**: `spatie/laravel-backup` con tutte le connessioni più il disco media, destinazione esterna (S3 o un'altra macchina), schedulazione giornaliera, monitoraggio (`backup:monitor`) e una prova di restore trimestrale documentata.
- **Criterio di accettazione**: un restore in staging da un backup notturno è riuscito e documentato.
- **Confidenza**: 90.

#### OPS-12 — Nessun error tracking; logging configurato male
- **Gravità**: Alta. **Tipo**: BD.
- **Evidenza**:
  - Nessun `sentry/*`, `spatie/laravel-ignition`/Flare, Bugsnag o Rollbar in nessun `composer.json`.
  - `laravel/config/logging.php:22` contiene `'default' => config('logging.default', 'stack')`: si autoreferenzia e ignora `LOG_CHANNEL`, quindi è sempre `stack`.
  - `stack` usa `LOG_STACK`, che di default vale `single` (riga 57): un unico `laravel.log` che cresce senza rotazione.
  - `.env.production.example:8-11` non imposta `LOG_STACK` e lascia `LOG_SLACK_WEBHOOK_URL` vuoto.
- **Impatto**: in produzione gli errori dei cittadini non arrivano a nessuno, e il disco può riempirsi.
- **Correzione**: `'default' => env('LOG_CHANNEL', 'stack')`, `LOG_STACK=daily` (14 giorni), e Sentry o Flare con DSN in `.env`.
- **Criterio di accettazione**: un'eccezione provocata in staging compare in Sentry entro un minuto.
- **Confidenza**: 90.

#### OPS-13 — Worker delle code e scheduler non configurati; health check minimo
- **Gravità**: Alta. **Tipo**: AP.
- **Evidenza**:
  - `.env.production.example:27` ha `QUEUE_CONNECTION=redis`.
  - Le notifiche Fixcity sono in coda (`ShouldQueue` in `Modules/Fixcity/app/Notifications/TicketLifecycleNotification.php`, `TicketAssignedNotification.php`).
  - Lo scheduler definisce la retention GDPR (`routes/console.php:17-22`: `activitylog:clean`, `model:prune`, `purge-deleted-accounts`), più un `inspire` orario (riga 13) che non serve.
  - Nel repository non ci sono file supervisor, systemd o crontab, e `deploy.sh` non li gestisce.
  - L'health check è solo `/up` (`bootstrap/app.php:28`), che verifica unicamente l'avvio dell'app.
  - `spatie/laravel-health` registra i check solo quando si apre la pagina Filament (`Modules/Xot/app/Filament/Pages/HealthPage.php:92`) e `health:check` non è schedulato.
- **Impatto**: se il worker non gira, le notifiche ai cittadini restano in coda. Se manca il cron, i dati personali non vengono cancellati come promette l'informativa privacy.
- **Correzione**: versionare `deploy/supervisor/fixcity-worker.conf` e il cron (`* * * * * php artisan schedule:run`); registrare i check di salute in un service provider (DB, Redis, coda, spazio disco, ultimo backup) e schedulare `health:check`.
- **Criterio di accettazione**: `php artisan schedule:list` in produzione mostra i job con l'ultima esecuzione e `/health` risponde con lo stato di DB e coda.
- **Confidenza**: 75. Il server potrebbe avere una configurazione fuori dal repository.

#### OPS-14 — APP_KEY reale e credenziali committate
- **Gravità**: Alta. **Tipo**: BD.
- **Evidenza**: `laravel/.env.fixcity:3` e `laravel/.env.latest:3` contengono la stessa `APP_KEY` reale (valore omesso), con `APP_DEBUG=true` (riga 4) e `DB_PASSWORD` valorizzata (valore omesso) (riga 19). `.env.fixcity:89` ha `IMAP_PASSWORD` valorizzata (valore omesso).
- **Impatto**: se quella chiave è usata in un ambiente pubblico, chiunque abbia accesso al repository può falsificare cookie e sessioni firmati e decifrare dati cifrati.
- **Correzione**: ruotare la chiave dove è in uso, rimuovere i file dal tracking (`.env.*` tranne `*.example`) e valutare se ripulire la cronologia.
- **Criterio di accettazione**: `git ls-files 'laravel/.env*'` elenca solo `*.example` e `.env.testing` con chiave fittizia.
- **Confidenza**: 95 sulla presenza; non so se la chiave sia usata in produzione.

#### OPS-15 — Gestione delle release incoerente: 0 tag
- **Gravità**: Media. **Tipo**: BD.
- **Evidenza**:
  - `git tag` restituisce 0 tag. `version.txt` e `.release-please-manifest.json` dicono `1.0.0`.
  - `.releaserc.json:31` usa `./bashscripts/release/changelog-advanced.js`, che non esiste.
  - `.releaserc.json:41-42` mette come asset `CHANGELOG.md` e `VERSION`, ma il file si chiama `version.txt` e il changelog è in `docs/changelogs/CHANGELOG.md` (riga 27).
  - `semantic-release-advanced.yml:35` usa `npm ci`, che fallisce senza lockfile.
  - release-please e semantic-release convivono.
- **Correzione**: scegliere un solo strumento (release-please, configurazione già pronta) e taggare `dev` e `main` in modo coerente.
- **Criterio di accettazione**: un merge su `main` produce un tag `vX.Y.Z` e il CHANGELOG.
- **Confidenza**: 85. Il `ls-remote` dei tag non ha restituito dati, quindi non è verificato sul remoto.

---

## 6. Igiene del repository

#### OPS-16 — File di report, script temporanei e backup tracciati
- **Gravità**: Media. **Tipo**: DT.
- **Evidenza**: alla radice ci sono 92 file tracciati, fra cui:
  - rapporti: `AUDIT-SESSION-REPORT.md`, `BUGFIX_REPORT.md`, `FINAL-DELIVERY-SUMMARY.md`, `FINAL-SESSION-WRAP-UP.md`, `SESSION-CLOSURE.md`, `PROJECT-STATUS-DASHBOARD.md`, `QUICK-START-NEXT-SESSION.md`, `PHPSTAN_*` ×3, `PEST_COVERAGE_REPORT.md`, `STATUS_RISULTATI_RICERCA.md`, `COMMIT_MESSAGE.md`, `audit_findings.txt`, `phpmd_violations_report.md`;
  - output: `report.txt` (271 KB, output di PHPStan), `phpstan_initial_report.json` (175 KB), `phpstan_errors.json`, `triage_report.json`, `media_report*.json`, `header-snapshot.yml`, `login-snapshot.yml`;
  - script: `fix_merge_conflicts.py` (tiene **sempre** il lato HEAD alla cieca, righe 8-30), `comprehensive-audit.sh`, `convert_phpunit_to_pest.php`, `evaluate_goals.sh`, `validation-metrics.sh`, `phpstan-fix-script.sh`;
  - varie: `x` (contenuto `T17=-`), `opencode.json.tui-migration.bak`, `ticket-list-before.png` (puntatore Git LFS senza LFS, quindi immagine rotta; lo stesso vale per `Modules/Geo/docs/img/debug-*.png`).
  
  In `laravel/` ci sono 80 file alla radice, fra cui `server.pid`, `error.html`, `compare-html.php/.py`, `fix_*.php/.sh`, 10 `phpstan*.neon`, 8 `phpstan_*.json`, `themes_phpstan.json` e la cartella randagia `laravel/laravel/`. Sono tracciati 442 file `.backup*`, `.bak` e `.old` (Xot 177, Geo 62, Cms 46, Tenant 42). Sono tracciati anche 6 database SQLite: `Themes/Sixteen/database/test_data.sqlite`, `Themes/Sixteen/ruvector.db`, `Modules/Fixcity/config/orbit.sqlite`, `config/local/fixcity/orbit.sqlite`, …
- **Correzione**: rimuovere tutto quanto sopra, con `.gitignore` per `*.backup*`, `phpstan_*.json`, `*.pid` e `report.txt`.
- **Criterio di accettazione**: la radice contiene solo README, CLAUDE/AGENTS, configurazioni e `laravel/`; `git ls-files | grep -cE '\.backup|\.bak'` restituisce 0.
- **Confidenza**: 95.

#### OPS-17 — Marcatori di conflitto di merge in 57 file tracciati
- **Gravità**: Media. **Tipo**: BD.
- **Evidenza**: `git grep -I -E '^(<<<<<<<|>>>>>>>)'` dà 140 righe in 57 file: 56 `.md` (48 in `laravel/Themes/Sixteen`, 8 in `Modules/Xot/docs`, per esempio `Xot/docs/00-index.md:29,37` e `Xot/docs/bmad/phpstan-status.md:13,54,78`) più **`laravel/Themes/Sixteen/.gitignore:75-128`**, dove i marcatori diventano pattern e valgono entrambi i lati del conflitto. Nessun marcatore in PHP, JSON, YAML o JS.
- **Correzione**: risolvere a mano, non con `fix_merge_conflicts.py`, e aggiungere un controllo in CI (`git grep -nE '^(<<<<<<<|>>>>>>>) '` deve restituire exit 1).
- **Criterio di accettazione**: il grep restituisce 0 righe.
- **Confidenza**: 100.

#### OPS-18 — `.git` di 730 MB: la causa principale sono gli screenshot nelle docs
- **Gravità**: Media. **Tipo**: DT.
- **Evidenza**: pack di 708 MB e 556 MB di blob compressi in cronologia.
  - Immagini e PDF: 398 MB, di cui `Themes/Sixteen/docs/visual-parity` 131 MB, `docs/pages` 52 MB, `docs/screenshots` 44 MB, `docs/visual-comparison` 27,5 MB, `docs/prompts` 18,5 MB, `docs/comparisons` 12 MB, `Modules/Fixcity/resources` 24 MB.
  - Markdown: 71 MB in 113.724 versioni di blob.
  - Asset compilati: 25 MB.
  - Binari di vendor in cronologia: `laravel/Modules/vendor/phpstan` 25,8 MB, `bin/quality-tools/phpstan` 22,9 MB.
  - `graphify-out/graph.json` fino a 17 MB.
  - Oggi `Themes/Sixteen/docs` occupa 228 MB su disco.
- **Correzione**: spostare gli screenshot in LFS o in un artifact esterno; `.gitignore` per `graphify-out`, `visual-parity/screenshots` e `vendor`; valutare `git filter-repo` coordinato con il team, dato che la regola è git forward-only.
- **Criterio di accettazione**: un nuovo clone pesa meno di 150 MB, oppure una crescita mensile del pack inferiore a 10 MB.
- **Confidenza**: 90.

#### OPS-19 — `bashscripts/` assente ma referenziato da 70 file tracciati
- **Gravità**: Media. **Tipo**: BD.
- **Evidenza**:
  - `bashscripts/` è ignorato (`.gitignore:150`) e `gitmodules.ini` lo dichiara come submodule, ma `.gitmodules` non esiste.
  - Lo usano il `CLAUDE.md` (hook `bashscripts/tools/docs/md-filenames.py`), `laravel/.env.testing:2`, `laravel/composer.json:57-59` (`guard:fixcity-wizard`, con un path relativo sbagliato da `laravel/`), `.releaserc.json:31`, `semantic-release-advanced.yml:49`, `contributor-lines-report.yml:40` e `.gitattributes` dei moduli.
  - Il symlink `.omp -> bashscripts/ai/.agents` è rotto.
- **Impatto**: un clone pulito non ha gli script di qualità, release e documentazione su cui si basano regole e workflow.
- **Correzione**: dichiarare un vero submodule (`.gitmodules`) oppure versionare gli script necessari, e documentarlo nel README.
- **Criterio di accettazione**: dopo `git clone --recurse-submodules`, `composer guard:fixcity-wizard` funziona.
- **Confidenza**: 90.

---

## 7. Documentazione per sviluppatori

#### OPS-20 — README sbagliati e non allineati alla realtà
- **Gravità**: Media. **Tipo**: BD.
- **Evidenza**:
  - `README.md:1-3` alla radice è il README del **modulo Notify**.
  - `laravel/README.md:5-10` dichiara "Laravel 11.x", "Filament 3.x", "PHPStan Level 3", "Coverage 85%" e "Quality 94.5%", con link a `project_docs/`, che non esiste.
  - Il quick start (righe 80-95) chiede PHP 8.2+ e MySQL 8 e usa `git clone https://github.com/your-org/fixcity.git`. Il progetto reale richiede PHP `^8.4` e Laravel `^13` (`laravel/composer.json:12-13`), più connessioni multiple, config del tenant in `config/local/fixcity`, un DB di test SQLite costruito da `xot:build-test-sqlite` e il submodule `bashscripts`.
- **Correzione**: un README di radice corto e vero: prerequisiti, clone con submodule, `.env`, DB, `composer install` da lock, build del tema, comandi di test, link alla wiki.
- **Criterio di accettazione**: uno sviluppatore nuovo avvia l'app e i test seguendo solo il README.
- **Confidenza**: 95.

#### OPS-21 — Volume di documentazione ingestibile e documenti di stato falsi
- **Gravità**: Media. **Tipo**: DT, più BD per i rapporti falsi.
- **Evidenza**:
  - 26.518 `.md` tracciati su 52.205 file.
  - 1.379 copie esatte in 806 gruppi, 12 `.md` vuoti, circa 4.249 `.md` di agenti, BMAD o skill.
  - Notify ha 5.568 `.md`, di cui 1.674 in `.github/skills`.
  - Ci sono configurazioni di un altro progetto (`laravel/config/it/quaeris/...`).
  - `PEST_COVERAGE_REPORT.md:22` dice "Tenant 0% / No tests", ma i file di test sono 41.
  - Sui rapporti PHPStan vedi QA-09.
- **Correzione**: un piano di consolidamento via story BMAD: deduplicare per hash, spostare le skill di agenti fuori dai moduli, archiviare i rapporti di sessione e rimuovere le configurazioni di altri tenant.
- **Criterio di accettazione**: 0 duplicati esatti e 0 rapporti di stato alla radice.
- **Confidenza**: 90.

---

## Priorità consigliata

1. OPS-01, QA-01, OPS-02: rendere la CI reale (merge-plugin più lock, suite dei moduli, gate bloccanti).
2. OPS-14, OPS-10, OPS-11, OPS-12, OPS-13: sicurezza e operatività di produzione (chiave, deploy, backup, error tracking, worker e cron).
3. QA-02, QA-05, QA-06: test affidabili sui percorsi critici.
4. OPS-04, OPS-05, OPS-06, OPS-15: pulizia dei workflow e delle release.
5. OPS-16 … OPS-21, QA-09: igiene del repository e documentazione.

## Cosa non ho potuto verificare

### Aggiornamento audit front office — 12 novembre 2026

Il controllo browser guest ha rilevato un difetto axe condiviso nel footer: il link alla home aveva un `aria-label` sovrascritto non coerente con il contenuto visibile. La correzione è stata applicata nel tema Sixteen (`components/sections/footer/v1.blade.php`); resta da verificare dopo deploy sugli URL pubblici e con le viewport 375, 768 e 1440 px.

La verifica completa per utente autenticato resta aperta perché manca una fixture browser isolata. Non sono state eseguite migration o modifiche distruttive sul database di produzione.

- **Esecuzione di PHPStan, Pest, Pint e Rector**: niente `vendor/`, packagist bloccato e PHP locale 8.3. Lo stato reale di PHPStan e quanti test passano o falliscono restano ignoti.
- **Esito reale dei workflow su GitHub**: non ho controllato la cronologia di Actions con `gh`. I giudizi "rosso/verde" sono dedotti dalla configurazione.
- **Merge-plugin installato globalmente** sulle macchine di sviluppo, che spiegherebbe perché in locale funziona (OPS-01).
- **Configurazione reale del server di produzione**: supervisor, cron, backup esterni, Sentry configurato fuori dal repository, uso effettivo dell'`APP_KEY` di `.env.fixcity`.
- **Tag sul remoto**: `git ls-remote --tags` non ha restituito output.
- **`npm audit` del tema Sixteen**: la risoluzione si blocca sul tarball GitHub di `x256` (403 dal proxy).
- **Pest e i `Pest.php` dei moduli**: se Pest carichi `Modules/*/tests/Pest.php` quando si passa un percorso; i file di test comunque dichiarano `uses(TestCase::class)` per file.
- **Coerenza visiva degli asset compilati** rispetto ai sorgenti: ho verificato solo l'integrità del manifest, non ho rifatto la build.
