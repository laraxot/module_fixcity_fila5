---
title: "Fixcity — Audit multidisciplinare e stato del progetto"
type: audit
tags: [fixcity, audit, executive-summary, roadmap]
created: 2026-11-05
updated: 2026-11-10
status: active
related:
  - ./COMPLETION_ROADMAP.md
  - ./SECURITY_AUDIT.md
  - ./DATABASE_AUDIT.md
  - ./QUALITY_CI_OPS_AUDIT.md
  - ./PRODUCT_UX_AUDIT.md
  - ../agent-confidence-brief.md
  - ../bmad-remaining-work.md
---

# Audit Fixcity

Fixcity ha un flusso cittadino ben costruito nel codice, ma al 5 novembre 2026 non è pronto per un Comune reale. In produzione
tutti i pannelli di gestione rispondevano 500 e la pagina di errore esponeva il debug. Nel repository ci sono segreti
committati e un callback SPID che non verifica la firma (oggi non attivo, vedi sotto). La CI esegue circa 60 test su oltre 1.500 e nessun controllo può fallire.

Il lavoro recente si è concentrato su parità visiva e PHPStan. I blocchi di esercizio, cioè deploy, debug, segreti, test in CI,
worker e backup, sono rimasti fuori dal radar.

## Come è stato fatto l'audit

- Codice del branch `dev` letto e verificato con grep, `php -l`, `git log`, grafo delle dipendenze fra moduli e `npm audit`.
- Sito di produzione fixcity.eu controllato con sole richieste GET dal browser: pannelli, pagine demo, health check.
- Quattro analisi indipendenti in parallelo (sicurezza, dati e backend, test/CI/operatività, prodotto e UX), più una revisione
  indipendente delle correzioni.
- **Limite principale:** nessun `vendor/`. Packagist è bloccato dall'ambiente, PHP è 8.3 contro `^8.4` e non esiste `composer.lock`.
  PHPStan, Pest e Pint non sono stati eseguiti: i giudizi sul comportamento a runtime sono deduzioni dal codice.

## Stato per area

| Area | Stato | Confidenza | Problemi critici | Priorità |
| --- | --- | --- | --- | --- |
| Produzione (fixcity.eu) | Pannelli admin 500, debug esposto, pagine demo pubbliche | 95 (verificato via HTTP) | Back office inutilizzabile | P0 |
| Sicurezza | Buona base (policy, CSRF, rate limit sul tracking) con falle gravi | 70 | Segreti committati, host header e tenant, SPID senza firma (latente) | P0 |
| Dati e backend | Workflow chiaro, schema fragile | 75 | Migrazione rating cancellata, `down()` distruttivo, nessun indice né FK | P0/P1 |
| Test e CI | Test buoni sui percorsi critici, ma non eseguiti | 85 | Testsuite senza moduli, gate con `|| true`, CI senza dipendenze | P1 |
| Deploy e operatività | Script non sicuro, nessun worker, cron o backup nel repo | 80 | `composer update` in produzione, nessun error tracking | P0/P1 |
| Prodotto e UX | Journey cittadino completo nel codice, back office ricco | 70 | Niente SPID/CIE, email non verificate, segnaposto legali | P1 |
| Multi-comune | Multi-istanza per host, non piattaforma condivisa | 75 | Nessun onboarding, nessuna colonna comune | P2 |
| Documentazione | Molto voluminosa e in parte falsa | 85 | 26.518 `.md`, README sbagliati, rapporti "0 errori" contraddittori | P2 |

## Punteggi provvisori (0-100)

| Dimensione | Punteggio | Motivo |
| --- | --- | --- |
| Completezza funzionale cittadino | 70 | Wizard, tracking, area personale presenti; email non verificate, segnaposto legali |
| Completezza funzionale back office | 60 | Azioni complete nel codice, ma pannelli 500 in produzione e tipi non configurabili |
| Sicurezza | 45 | Segreti committati e SPID senza firma pesano più delle buone policy |
| Affidabilità operativa | 25 | Niente lock, worker, backup, error tracking; deploy distruttivo |
| Qualità e test | 45 | Molti test seri, ma fuori dalla CI e non bloccanti |
| Prontezza per la vendita alla PA | 35 | Mancano SPID/CIE, integrazioni PA, qualificazione cloud, dati dell'ente |

Tutti i punteggi sono provvisori finché PHPStan e Pest non girano su un ambiente con `vendor/`.

## Correzioni applicate in questa sessione

Commit su `dev`, dettagli e verifiche nella story
[production-hardening-p0](../stories/production-hardening-p0.story.md).

| ID | Correzione | Verifica |
| --- | --- | --- |
| PROD-01 | Gli asset Geo si registrano solo se la build esiste; `deploy.sh` compila Geo prima di toccare l'app e, se fallisce dopo, lascia il sito in manutenzione | Build Vite eseguita qui; test Pest scritto, non eseguito |
| SEC-02 | Rotte SPID/CIE registrate solo con `SIXTEEN_SPID_ENABLED=true`. Oggi il `ThemeServiceProvider` di Sixteen non è registrato (il merge-plugin include solo `Modules/*`), quindi le rotte non sono attive: il gate è una difesa per quando lo sarà | Test Pest scritto, non eseguito |
| PRD-03 / SEC-09 | Pagine demo `/tests/*`, `prova`, `counter` nascoste dove `SHOWCASE_PAGES` è falso (default in produzione) | Test Pest scritto, non eseguito |
| SEC-08 | La modifica libera di un ticket non può più cambiare `status` e `priority` | Test Pest scritto, non eseguito |
| DB-01 | Migrazione idempotente che ripristina `citizen_rating` e `citizen_rated_at` | `php -l` |
| DB-02 | `down()` vuoto nelle migrazioni di allineamento di `tickets` e `ticket_hours` | `php -l` |
| QA-01 | Testsuite di moduli e temi: **ritirata** dopo la revisione (helper globale duplicato e `Pest.php` dei moduli non caricati); motivo e piano in `phpunit.xml` | XML valido |
| OPS-17 | Conflitto di merge in `Themes/Sixteen/.gitignore` che faceva ignorare ogni nuovo componente Blade | `git check-ignore` |
| Binari | `.gitattributes` corretti e 675 PNG corrotti ripristinati (sessione precedente) | CRC e decodifica |

## Correzioni del 2026-11-10

| Area | Correzione | Story |
| --- | --- | --- |
| Mirror | 16 file PHP del tema non compilavano dopo il mirror `9fd38b4912` (fusione sbagliata a monte): ripristinati. Riapplicate due correzioni P0 annullate | [Sixteen](../../../../Themes/Sixteen/docs/stories/mirror-merge-corruption-php-recovery.story.md) |
| Ticket | Codice sempre presente, creazione atomica, una ricevuta, lock sui cambi di stato, indici, blocco con dati dell'ente mancanti | [ticket-data-integrity](../stories/ticket-data-integrity.story.md) |
| Account | Account cancellati senza accesso (web, API, token), verifica email reale | [User](../../../User/docs/stories/account-deletion-and-email-verification.story.md) |
| API pubbliche | Allegati di ticket non pubblici non più esposti, disco privato per i nuovi, GeoJSON limitato e in cache | [public-api-media-and-geojson](../stories/public-api-media-and-geojson.story.md) |
| CI e deploy | CI senza `|| true`, testsuite dei moduli, backup DB nel deploy, `LOG_CHANNEL`, runbook | [Xot](../../../Xot/docs/stories/ci-deploy-ops-hardening.story.md) |
| Prodotto | README reali, niente pubblicità di default, 100 traduzioni de/es | [traduzioni](../stories/ticket-translations-de-es.story.md), [pubblicità](../../../../Themes/Sixteen/docs/stories/pa-no-advertising.story.md) |

Nessuna di queste è stata eseguita con Pest o PHPStan (ambiente senza `vendor/`): la prima esecuzione della nuova CI su
GitHub è il primo vero controllo.

## Azioni che richiedono una persona

1. **Ruotare** `APP_KEY` di produzione se è quella di `laravel/.env.fixcity`, il token Matomo e le credenziali dei tenant in `config/it/quaeris*`, `config/net/futurely`, `config/localhost` (SEC-01). Poi togliere quei file da git.
2. **Sul server di produzione** impostare `APP_ENV=production` e `APP_DEBUG=false`, configurare `TRUSTED_HOSTS`, il worker delle code e il cron dello scheduler.
3. **Rilasciare** il branch `dev` corretto ed eseguire la prima build Geo (`deploy.sh` ora la fa).
4. **Decidere** se versionare `composer.lock` e `package-lock.json`, oggi esclusi da `.gitignore` (OPS-07).
5. **Validare con un Comune pilota** i requisiti di vendita: SPID/CIE, protocollo, app IO, qualificazione ACN.
6. **Correggere a monte `laraxot/theme_sixteen_fila5`** (ramo `dev`): ha ancora i 15 file PHP rotti; il prossimo mirror li riporta.
7. **GitHub:** impostare la branch protection su `dev` con i job obbligatori elencati in `Modules/Xot/docs/ci/ci-pipeline.md`; decidere se aggiungere `wikimedia/composer-merge-plugin` a `laravel/composer.json` (oggi installato globalmente in CI).
8. **Spostare gli allegati esistenti** sul disco privato con la procedura della story `public-api-media-and-geojson` e disattivare `Options Indexes` su `/storage`.
9. **Decidere DEC-11**: oggi l'email verificata è obbligatoria per segnalare.
10. **Compilare `config/<tenant>/ente.php`** (titolare, DPO, PEC): finché l'informativa ha segnaposto il wizard rifiuta l'invio.

## Documenti

- [COMPLETION_ROADMAP.md](./COMPLETION_ROADMAP.md): backlog P0-P3 con stato, roadmap in sei fasi, percorso critico.
- [SECURITY_AUDIT.md](./SECURITY_AUDIT.md): 17 rilievi SEC.
- [DATABASE_AUDIT.md](./DATABASE_AUDIT.md): come funzionano dati, workflow, tenant, code e AI; 28 rilievi DB.
- [QUALITY_CI_OPS_AUDIT.md](./QUALITY_CI_OPS_AUDIT.md): 12 rilievi QA e 21 OPS, tabella dei test per percorso critico.
- [PRODUCT_UX_AUDIT.md](./PRODUCT_UX_AUDIT.md): journey, classificazione delle funzionalità, 14 rilievi PRD e 9 UX.

## Cosa non sappiamo ancora

- Se PHPStan e Pest passano davvero: va eseguito su un ambiente con `vendor/` e PHP 8.4.
- La configurazione reale del server: env, worker, cron, web server, versioni installate.
- Lo schema reale del database di produzione, ad esempio se esiste la colonna `fiscal_code`.
- Se in produzione il `ThemeServiceProvider` di Sixteen è registrato in altro modo: `php artisan route:list --name=spid` sul server lo dice.
- I requisiti scritti di un Comune cliente: oggi SPID/CIE, protocollo e app IO sono "da validare".
