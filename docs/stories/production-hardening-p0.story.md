---
title: "Fixcity — Hardening P0 di produzione"
type: story
tags: [fixcity, bmad, production, security, audit]
created: 2026-11-05
updated: 2026-11-05
status: review
owner: Modules/Fixcity (coordinamento); file toccati anche in Geo, Themes/Sixteen, app/
related:
  - ../audit/README.md
  - ../audit/COMPLETION_ROADMAP.md
  - ../../../Xot/docs/stories/binary-files-gitattributes-png-recovery.story.md
---

# Hardening P0 di produzione

- Fase BMAD: Quick Flow per ogni correzione (problema, causa, fix, test), dentro l'audit multidisciplinare del 2026-11-05.
- Branch: `dev`.

## Contesto

L'audit (vedi [README](../audit/README.md)) ha trovato in produzione su fixcity.eu, con sole GET:

- ogni pannello Filament (`/fixcity/admin`, `/user/admin`, `/cms/admin`, `/geo/admin`, `/notify/admin`, `/tenant/admin`, `/xot/admin`)
  rispondeva 500 per `ViteManifestNotFoundException` su `public_html/assets/geo/manifest.json`;
- la pagina di errore era quella di debug di Laravel (stack trace, header, percorsi `/home/ploi/...`);
- le pagine demo `/it/tests/*` rispondevano 200.

## Modifiche

| Problema | File | Correzione |
| --- | --- | --- |
| Pannelli 500 | `Modules/Geo/app/Providers/Filament/AdminPanelProvider.php` | Asset del coordinate picker registrati solo se esiste il manifest o il file `hot`; altrimenti warning nel log |
| Pannelli 500 | `deploy.sh` | `artisan down` con `trap` che riporta online; `npm install` + `vite build` di Geo; errore se manca il manifest |
| SPID senza firma (SEC-02) | `Themes/Sixteen/app/Providers/ThemeServiceProvider.php` | `routes/auth.php` caricato solo con `sixteen.auth.spid.enabled === true` (`SIXTEEN_SPID_ENABLED`) |
| Pagine demo pubbliche | `app/Http/Middleware/BlockShowcasePages.php`, `bootstrap/app.php`, `config/security.php` | Middleware globale: 404 su `tests`, `prova`, `counter`, `sixteen/test-auth`… se `security.showcase_pages` è falso (default in produzione, `SHOWCASE_PAGES`) |
| Stato modificabile dal browser (SEC-08) | `Modules/Fixcity/.../Pages/EditTicket.php` | `status` e `priority` scartati in `mutateFormDataBeforeSave` |
| Migrazione cancellata (DB-01) | `Modules/Fixcity/database/migrations/2026_11_05_000001_restore_citizen_rating_on_tickets_table.php` | Ripristino idempotente di `citizen_rating`, `citizen_rated_at` |
| Rollback distruttivo (DB-02) | migrazioni `2026_10_22_000002`, `2026_10_22_000003` | `down(): void {}` |
| Test fuori suite (QA-01) | `laravel/phpunit.xml` | Tentativo ritirato dopo la revisione: vedi "Revisione". Nel file resta la spiegazione |
| Componenti Blade ignorati da git | `Themes/Sixteen/.gitignore` | Risolto il conflitto di merge: righe `components`, `layouts`, `laravel`, `.c*` rimosse |
| Pagine vietate nel tema (PRD-05, PRD-10) | `Themes/Sixteen/resources/views/pages/counter.blade.php`, `pages/area-personale/pratiche.blade.php` | Rimosse: erano state tolte di proposito (`ca9971ffd2`, `5d60a0430f`) e reintrodotte dal mirror `becb0d58bd`; il doppione di `pratiche` chiamava `route('segnalazioni')`, inesistente. La pagina resta servita dal modulo User |

Test nuovi: `Modules/Geo/tests/Unit/Filament/AdminPanelAssetsAvailabilityTest.php`,
`Themes/Sixteen/tests/Feature/Infrastructure/DigitalIdentityRoutesDisabledByDefaultTest.php`,
`tests/Feature/Pages/ShowcasePagesHiddenTest.php`, `Modules/Fixcity/tests/Unit/Filament/EditTicketWorkflowFieldsTest.php`.

## Verifica

- `php -l` su tutti i file PHP modificati: nessun errore.
- Build Vite di Geo eseguita in questo ambiente: manifest creato in `public_html/assets/geo/` (cartella esclusa da git).
- `bash -n deploy.sh`: ok. `phpunit.xml` valido.
- `git check-ignore` su un nuovo componente Blade del tema: non più ignorato.
- **Non eseguiti:** PHPStan, Pest, Pint. L'ambiente non ha `vendor/` (packagist bloccato, PHP 8.3). Il prossimo agente con
  `vendor/` deve eseguire: `cd laravel && ./vendor/bin/phpstan analyse Modules/Geo Modules/Fixcity Themes/Sixteen app`,
  `./vendor/bin/pest Modules/Geo/tests/Unit/Filament Modules/Fixcity/tests/Unit/Filament/EditTicketWorkflowFieldsTest.php tests/Feature/Pages Themes/Sixteen/tests/Feature/Infrastructure`.

## Revisione indipendente

Un agente separato ha rivisto le modifiche dopo il primo commit (`04fd40d2d9`). Correzioni nel commit successivo:

- **Test Geo:** `App\Application::publicPath()` ignora `usePublicPath()`, quindi il test scriveva un manifest finto e un file
  `hot` nella vera `public_html`, rompendo gli asset. Ora `assetsAreAvailable(?string $publicRoot)` riceve la radice e il
  test usa solo una cartella temporanea.
- **deploy.sh:** il `trap` su EXIT rimetteva online un deploy fallito a metà (codice nuovo, schema vecchio). Ora la build Geo
  avviene prima di toccare l'app, un errore successivo lascia il sito in manutenzione e `artisan up` è esplicito a fine
  script; `npm install --include=dev` perché `vite` è una devDependency.
- **phpunit.xml:** le testsuite `Modules`/`Themes` causavano "Cannot redeclare runTicketSeeder()" (dichiarata in
  `Modules/Fixcity/tests/Unit/Seeders/TicketDatabaseSeederTest.php:161` e `tests/Unit/Modules/Fixcity/Seeders/TicketDatabaseSeederTest.php:161`)
  e circa 240 file senza `uses()` proprio fallivano. Ritirate; QA-01 resta aperto.
- **BlockShowcasePages:** bypass via `/{lang}/pages/tests.<nome>` (slug CMS delle pagine demo), ora bloccato.
- **SPID:** il `ThemeServiceProvider` di Sixteen non risulta registrato (merge-plugin solo su `Modules/*`), quindi le rotte
  SPID non sono attive oggi; il gate resta come difesa. `config/cie.php:63-64` chiama `route()` come default di `env()`:
  se un giorno il provider viene registrato con SPID attivo, il boot fallisce. Da rendere lazy.
- **Aperti dalla revisione:** test Livewire su `EditTicket` (oggi si prova solo l'helper); `CreateTicket` del back office non
  scarta `status`/`priority`.

## Handoff

- **Mirror dai repository dei moduli:** il commit `becb0d58bd` ha reintrodotto file rimossi di proposito. Prima del prossimo
  mirror le stesse rimozioni vanno fatte in `laraxot/theme_sixteen_fila5`, altrimenti tornano.
- Resta `pages/pages/[slug].blade.php`, anch'esso vietato: definisce la rotta `pages.view` usata da
  `Modules/User/.../RegistrationWidget.php:142` e `EnsureRegistrationEnabled.php:23`, e `pages/tests/pages-view-reference.blade.php`
  dichiara lo stesso nome. Va spostato in un JSON CMS con un redirect, non cancellato alla cieca.

- Per vedere le pagine demo in locale: `SHOWCASE_PAGES=true` nel `.env` (default già vero se `APP_ENV` non è `production`).
- SPID/CIE restano spenti finché `SpidAuthAction::validateSamlResponse` non verifica firma, Issuer, Audience, `InResponseTo` e
  scadenza, e il callback non ha l'eccezione CSRF corretta. Valutare un proxy SPID certificato invece di un SP fatto in casa.
- Azioni umane aperte (rotazione segreti, env di produzione, worker, cron): elenco nel [README dell'audit](../audit/README.md).
