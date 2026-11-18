---
id: una-codebase-otto-superpoteri
title: "Una codebase per tanti Comuni: gli otto superpoteri applicati a Fixcity"
type: story
epic: go-live-pa
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-11-18
updated: 2026-11-18
qmd: "una codebase tanti comuni tenant sicurezza spid feature flag tdd rilasci permessi metriche costi onboarding infrastruttura"
related: [dcp-200, "#721", "#722", "#723", "#724", "#726", "#728", "#729", "#730", "#731"]
tags: [multi-tenant, go-live, pa, roadmap, bmad]
---

# Una codebase per tanti Comuni: gli otto superpoteri applicati a Fixcity

## Fase BMAD
Analyze → Plan. Questa story prende la slide «Una codebase, otto superpoteri» (laravelday 2026) e la cala su Fixcity: per ogni punto
dice **cosa c'e' gia' nel codice** (con il percorso), **cosa manca** e **dove si lavora** (issue). Non e' un elenco di desideri:
ogni riga «c'e'» e' verificabile aprendo il file citato.

## Premessa: come Fixcity e' gia' «una codebase»
- Il tenant si sceglie dall'host: `Modules/Tenant/app/Actions/GetTenantNameAction.php` legge `SERVER_NAME` e carica
  `config/local/<tenant>/` (per noi `config/local/fixcity/`), che sovrascrive la configurazione dei moduli.
- I contenuti del front office sono JSON per tenant (`config/local/<tenant>/database/content/pages/`).
- I moduli condivisi (Geo, Comment, Gdpr, AI, Xot, …) restano agnostici: i test di Xot falliscono se citano un progetto.
- Un nuovo Comune e' quindi «una cartella di configurazione + un database», non un fork.

## Gli otto punti

| # | Superpotere | C'e' gia' | Manca | Dove |
|---|---|---|---|---|
| 1 | **Sicurezza** (tenant isolati, WAF, segreti gestiti) | Database per tenant (`config/local/fixcity/database.php`: `DB_DATABASE`, `DB_DATABASE_USER` separati); CSP in `config/security.php` (report-only, 0 violazioni su 18 pagine); nessuna richiesta a terzi prima del consenso (`Themes/Sixteen/tests/Feature/NoThirdPartyBeforeConsentTest.php`); OSM passa dal nostro server (ADR Geo) | WAF davanti all'app; segreti fuori dal `.env` (vault del provider qualificato); CSP in enforce; 2FA nei pannelli | #726 (2FA/CSP), #721 (infrastruttura qualificata) |
| 2 | **SPID o OAuth per tenant** | Login Filament; codice SPID in `Themes/Sixteen/routes/auth.php`, spento da `sixteen.auth.spid.enabled` | La validazione SAML fatta a mano (`SpidAuthAction::validateSamlResponse()`) non va accesa: va sostituita da un pacchetto mantenuto (SPID/CIE OIDC); configurazione per tenant (ogni Comune e' un soggetto aggregato o ha il suo metadata) | #724 |
| 3 | **Feature flag per tenant** | `laravel/pennant` installato (`config/pennant.php`, store `database`); gli interruttori di oggi sono chiavi di config per tenant (es. `comments.gravatar.enabled`, `geo.config.osm_proxy.nominatim.enabled`) | Nessun `Feature::define`: le funzioni che un Comune puo' volere o no (commenti pubblici, assistente AI, valutazioni) non hanno un interruttore unico e documentato | #728 |
| 4 | **TDD** | Pest per modulo e tema (solo nei moduli, mai in `laravel/tests/`); CI con PHPStan a livello max e Pest (`.github/workflows/ci.yml`); contratti di comportamento verificati (`Modules/Xot/tests/Unit/Docs/ActionContractsAreCompleteTest.php`) | 269 file Pest senza `uses(TestCase)`; ~70 test Gdpr che descrivono un flusso che non esiste; PHPStan bloccante solo su Fixcity, User, Geo | story Gdpr, debito in `PackagesHaveContractsAndAdrTest` |
| 5 | **Rilasci lampo** | `deploy.sh` unico (migrazioni, `filament:upgrade`, `optimize`, `route:trans:cache`, `queue:restart`); release-please e semantic-release nei workflow | Rilascio atomico (cartella di release + symlink) e ritorno alla release precedente senza toccare git; oggi `deploy.sh` lavora sulla cartella viva | #731 |
| 6 | **Permessi e metriche** | `spatie/laravel-permission` (config per tenant `config/local/fixcity/permission.php`); log attivita' (`spatie/laravel-activitylog`); pagina Salute in Filament (`Modules/Xot/app/Filament/Pages/HealthPage.php`, `spatie/laravel-health`) | `laravel/pulse` configurato (`config/pulse.php`) ma senza cruscotto raggiungibile; nessuna metrica di servizio per il Comune (tempi di presa in carico e risoluzione) | #730 |
| 7 | **Costi e onboarding** | Il costo marginale di un Comune e' una cartella `config/local/<tenant>/` + un database | Nessun comando che crei un tenant: oggi si copia a mano la cartella di Fixcity, con il rischio di portarsi dietro testi e nomi di Fixcity | #729 |
| 8 | **Infrastruttura** | `deploy.sh`, `public_html` come document root con asset per symlink | Fornitore qualificato ACN (QC1 almeno), dati e backup nell'UE, RTO/RPO scritti, backup schedulati (NFR-003 non implementato) | #721, #722, #723 |

## Decisioni prese qui
- Gli interruttori per Comune restano **configurazione per tenant** finche' non serve cambiarli senza deploy; Pennant entra solo
  quando un amministratore del Comune deve poter accendere o spegnere una funzione dal pannello. La #728 lo decide.
- L'onboarding parte da un **tenant modello neutro** (non dalla cartella di Fixcity), cosi' un nuovo Comune non eredita i testi di
  un altro.

## Criteri di accettazione della story
- [x] Ogni riga «Manca» ha una issue aperta e collegata qui (#721–#731).
- [x] Le issue nuove hanno criteri di accettazione verificabili da un test o da un comando.
- [ ] Il registro DCP-200 rimanda a questa story per le decisioni di prodotto multi-tenant.

## Verifica
I percorsi della colonna «C'e' gia'» sono stati controllati il 2026-11-18 sul ramo `dev`.
