---
title: "Suite Pest root (Feature, Unit) e Fixcity verdi in CI"
type: story
bmad: true
status: review
created: 2026-11-10
updated: 2026-11-11
owner: Fixcity
tags: [fixcity, ci, pest, mariadb, testcase]
qmd: "suite pest root feature unit fixcity ci mariadb migrate"
---

# Suite Pest root (Feature, Unit) e Fixcity verdi in CI

Contesto: job `Pest (Feature|Unit|Fixcity)` di `.github/workflows/ci.yml` (MariaDB 11, `fixcity_data_test` + `fixcity_user_test`).
Log analizzati: Feature 34 falliti (commit 15e20598a6), Unit 1 fallito, Fixcity 550 falliti (commit 42947da871).
I test non girano in locale (PHP 8.3, niente vendor): ogni correzione e' verificata leggendo codice e storia git, con `php -l`.

## Cause, file, correzioni

### 1. Suite Fixcity su MariaDB mai migrata (~500 test)

- Causa: `XotBaseTestCase::prepareSharedSqliteForTesting()` converte solo le connessioni con driver `sqlite`. In CI `DB_CONNECTION=mariadb`
  e `DB_DATABASE_USER` e' impostato: tutte le connessioni restano MariaDB e puntano a database `*_test` vuoti
  (`xot:build-test-sqlite` popola solo lo SQLite). Errori: 409 `fixcity_user_test.users doesn't exist`, poi tickets, articles,
  profiles, categories, job_batches, ratings; i 45 "Expected 200 but received 500" e i 500 di FAQ, header, footer, SecurityHeaders,
  TicketsGeoJson sono la stessa causa (eccezione PDO `articles`/`tickets` mancante nella richiesta).
- File: `Modules/Fixcity/tests/TestCase.php`.
- Correzione: `beginDatabaseTransaction()` lancia `migrate --force` una volta per processo, prima di aprire le transazioni, solo se
  la connessione di default non e' sqlite. Stesso schema di `Tests\TestCase` e `Modules\User\Tests\TestCase`. Additivo (mai `fresh`).

### 2. `TicketFactory` scriveva uno username in colonne UUID

- Causa: `created_by`/`updated_by` = `fake()->optional()->userName()`; le colonne sono `foreignIdFor(User)` (UUID). SQLite accetta,
  MariaDB no (`Incorrect uuid value 'prosacco.theron' for column tickets.created_by`).
- File: `Modules/Fixcity/database/factories/TicketFactory.php`; test che passavano username:
  `tests/Feature/Actions/BuildAuthenticatedUserTicketsQueryActionTest.php` (`'system'` -> UUID di un operatore),
  `tests/Feature/Pages/TicketPublicDetailPageTest.php` (`'mario.rossi'` -> UUID fisso, stessa asserzione di non esposizione).
- Correzione: la factory lascia `null`.

### 3. Test root Feature senza build Vite (500 su pagine del tema)

- Causa: `Tests\TestCase` non chiamava `withoutVite()` (lo fa `XotBaseTestCase`): i layout del tema usano `@vite` e senza manifest la
  vista lancia `ViewException`. `/it/tests` rispondeva 500 solo nella suite root, 200 nella suite Fixcity.
- File: `tests/TestCase.php`.
- Correzione: `$this->withoutVite()` in `setUp()`. Le asserzioni di stato in `ShowcasePagesHiddenTest` e `ListaCategorieTest` ora
  riportano il messaggio dell'eccezione della richiesta, cosi' un eventuale 500 residuo e' diagnosticabile dal log CI.

### 4. Funzionalita' rimosse (skip con motivo, nessun test cancellato)

- Scaffolding Breeze/Volt (20 test): `tests/Feature/Auth/*Test.php` (6 file), `tests/Feature/ProfileTest.php`. `/login`, `/register`,
  `/profile`, `/dashboard` e i componenti Volt `pages.auth.*` non esistono; `App\Models\User` non ha tabella. Coperto da
  `Modules/User/tests/Feature/Auth` e `Modules/Fixcity/tests/Feature/Auth`.
- `tests/Feature/Feature/Jobs/GeocodeTicketAddressJobTest.php` (6): il job e la colonna `address` non esistono; coperto da
  `Modules/Fixcity/tests/Unit/Jobs/GeocodeTicketAddressJobTest.php` (`GeocodeTicketAddressAction`, `location['address']`).
- `tests/Unit/MinimalProbeTest.php` (1): nessuna migration crea `tenants`/`domains`; la tenancy e' da configurazione.

### 5. Asserzioni superate

- `tests/Feature/ExampleTest.php`: `/` reindirizza a `/{locale}` (LaravelLocalization), come `Cms/.../RootRouteTest`.
- `tests/Feature/Pages/ListaCategorieTest.php`: `assertStatusNot()` non esiste in `TestResponse` -> `assertLessThan(500, ...)`.
- `tests/Feature/Themes/Sixteen/ListingComponentsTest.php`: il JSON e' in `content/pages/tests/` ed e' un solo blocco `list-page`.
- `Modules/Fixcity/tests/Unit/Cookie/CookieInventoryTest.php`: `consent_lifetime` sta in `GdprConfigData`, non in `gdpr.config.cookie`.
- `Modules/Fixcity/tests/Unit/MigrationConventionsTest.php`: `config/database.php` del modulo usa `$userDatabase`/`$applicationDatabase`.

## Rimasti aperti (fuori dal perimetro di questa story)

Regressioni introdotte dai commit di mirror `becb0d58bd` (2026-11-05) e `9fd38b4912` (2026-11-10), che hanno riportato file di moduli
a versioni precedenti a fix gia' fatti:

- Gdpr, `app/Providers/GdprServiceProvider.php`: persi la registrazione delle traduzioni `cookie-consent` in `app->booted()` con
  `addLines`, `View::prependNamespace('cookie-consent', ...)` e la registrazione di `InjectCookieBanner` (oggi il gruppo web riceve di
  nuovo `CookieConsentMiddleware` di statikbe). Fa fallire `CookieTextsParityTest` (4), `CookieInventoryTest` (durate, 2),
  `CookieBannerPagesTest`, `CookieBarObscuringTest` (4). Correzione: ripristinare il blocco rimosso da `9fd38b4912`.
- User, `lang/de/validation.php` e `lang/es/validation.php`: `uploaded` torna a contenere `:attribute`, che Livewire valorizza con
  `data.images.f1`. Fa fallire `TicketFormImagesLangTest` (2). Ripristinare i testi rimossi da `becb0d58bd`.
- Da verificare al prossimo run: `AuthFormErrorsAccessibilityTest` (autofocus su `/it/auth/login`), `UserApiLoginThrottleTest`
  (500 con `email` array: modulo User), `DesignComuniAssetRouteTest` (`Cache-Control` sovrascritto in `no-store, private`, firma di
  `DisableBackButtonCacheMiddleware` di Livewire sul gruppo web).

## Test

- `php -l` su ogni file toccato.
- Da eseguire in CI: `vendor/bin/pest --testsuite=Feature`, `--testsuite=Unit`, `--testsuite=Fixcity`.

## Secondo giro (CI run 42910837863)

Log: run 42910837863 (108 falliti) e 42912501538 (111 falliti, gia' con `BasePolicy::before()` ristretto al solo super-admin).
Molti nomi cambiano fra i due run perche' la causa 1 dipende da un valore casuale della factory.

### 1. `tickets.longitude` decimal(20,18): ~85 test (bug reale dello schema)

- Causa: `decimal(20,18)` ammette solo 2 cifre intere; MariaDB rifiuta ogni |longitudine| > 99,99 ("Out of range value for column
  'longitude'"). La `TicketFactory` genera [-180,180]: circa meta' dei ticket di factory falliva, in qualsiasi test. SQLite non controlla.
- File: `database/migrations/2023_01_01_000005_create_tickets_table.php`, `2026_04_29_110000_create_tickets_table.php`,
  `2026_06_10_140000_create_tickets_table.php` (solo il ramo `if (! hasColumn('longitude'))`: su un database esistente non fanno nulla);
  nuova `2026_11_10_000002_widen_tickets_longitude_precision.php` (`decimal(21,18)` con `change()`, allarga senza perdita, idempotente,
  saltata su SQLite o senza tabella/colonna, `down()` vuoto); commento in `database/factories/TicketFactory.php`.

### 2. Transazioni di test aperte quattro volte sulla stessa connessione: notifiche "dopo il commit" mai inviate (5)

- Causa: `TenantServiceProvider::aliasModuleConnectionsToDefault()` risolve `fixcity`, `comment`, `media`, `blog` sulla stessa istanza
  `Connection` della default. `connectionsToTransact` con cinque nomi apriva i livelli 1-4 su quella connessione; le Action con
  `transaction()` (approva, scarta, chiudi via `LockTicketForStatusChangeAction`) committavano al livello 4 e il
  `DatabaseTransactionsManager` di test esegue i callback solo al livello 1: l'observer `ShouldHandleEventsAfterCommit` di
  `TicketActivity` non partiva. Fuori da una transazione (test che creano l'attivita' a mano) funzionava, da qui il quadro misto.
- File: `tests/TestCase.php`, `connectionsToTransact()` restituisce un nome per ogni istanza di connessione (`spl_object_id`).
- Test: `TicketLifecycleNotificationTest` (approvata, scartata, chiusa), `PersonalAreaLifecycleNotificationsTest` (2).

### 3. Indice unique su `tickets.code` (DB-04): test con due ticket dello stesso codice (4)

- `tests/Unit/Actions/BuildPublicTicketDetailActionTest.php` (secondo ticket `TCK-SEGRETO-0002`),
  `tests/Feature/Pages/TicketOwnerDetailAccessTest.php` (quattro codici distinti nel dataset per stato e posizione).

### 4. `tickets.content` NOT NULL senza default (2)

- `tests/Feature/Actions/CreateTicketAtomicityTest.php`, `tests/Feature/Actions/TicketReceiptNotificationTest.php`: passano `content`,
  come fa il wizard (campo obbligatorio).

### 5. JPEG con firma valida ma non decodificabile: 500 e file con GPS su disco (5, bug reale)

- Causa: `acceptedFileTypes` guarda il MIME; `StripImageMetadataAction` decodifica dopo `Ticket::create` e lanciava
  `ImageDecoderException`. Riga e media tornavano indietro, il file no (il disco non e' transazionale).
- File: `app/Filament/Resources/TicketResource/Schemas/TicketForm.php`, `decodableImageRule()` sul campo `images`: decodifica JPEG/PNG
  con lo stesso decoder (Intervention, GD) prima di scrivere; errore `fixcity::ticket_form.fields.images.error_prepare`.
- Test: `CreateTicketWizardForgedUploadTest` (nessun orfano x2, errore di validazione x2, nessun GPS su disco).

### 6. Asserzioni superate o dipendenti dall'ambiente

- `tests/Feature/Filament/CreateTicketWizardEnteGuardTest.php`: `.env.testing` ha `LOG_LEVEL=error` e `Logger::writeLog()` non emette
  `MessageLogged` per un warning non gestito; il test usa un canale proprio a livello debug (NullHandler).
- `tests/Feature/Pages/CookieBarObscuringTest.php` (2): la riserva in basso esclude `[data-layout="flow"]` e il viewport basso lo decide
  lo script (`matchMedia('(max-height: 30rem)')`), non piu' una media query CSS.
- `tests/Feature/Filament/TicketResourceTest.php` (modifica): `status` e `priority` non passano dal form (SEC-08, `EditTicket::WORKFLOW_FIELDS`).
- `tests/Feature/Pages/LegacyTicketPagesRetiredTest.php`: `*.old` e `*.old2` sono in `.gitignore`, in CI non esistono; skip con motivo
  se mancano (archivio = storia git, ce04aee28e). Il contratto (nessuna pagina `[id]`/`[slug]` attiva) resta coperto.
- `resources/views/filament/forms/components/dc-file-upload.blade.php`: gli input file nascosti hanno `aria-label` con il testo del
  pulsante (Dcp146StepDatiTest, WCAG 4.1.2, it/en).

### Rimasti aperti (fuori da Fixcity)

- Gdpr `app/Actions/SaveGdprConsentsAction.php`: `created_by`/`updated_by` = `'gdpr_register_widget'` in colonne UUID; usare `$user->id`.
  Fa fallire `RegistrationConsent*Test` (2).
- Gdpr: manca la colonna `consents.revoked_at` usata da `RevokeGdprConsentsAction` (500 su cancellazione account, `AccountDeletion*Test`).
- `SeoLinksTest`: `/{lang}/tickets/track` assente dalla sitemap; `tickets/track.json` ha `slug: "track"` invariato dal 2026-10-20,
  da verificare come Cms ricava lo slug dal percorso (regressione probabile dal mirror becb0d58bd).
- `AuthFormErrorsAccessibilityTest`: `[autofocus]` su `/it/auth/login` (componente User/tema; User in modifica da un altro agente).
- `tests/TestCase.php` (radice: `mariadb`, `fixcity`, `blog`) e `Modules/User/tests/TestCase.php` (`mysql`, `mariadb`, `fixcity`) hanno la
  stessa doppia transazione della causa 2: stessa correzione di `connectionsToTransact()`.

### Test

- `php -l` su ogni file PHP toccato. Da eseguire in CI: `vendor/bin/pest --testsuite=Fixcity`, `vendor/bin/phpstan analyse Modules/Fixcity`.
