---
title: "Homepage audit — 2026-10-07"
type: bmad-audit
status: implementation-complete-pending-runtime
module: Fixcity
created: 2026-10-07
updated: 2026-10-07
tags: [bmad, audit, homepage, fixcity, sixteen, folio, design-comuni]
qmd: "fixcity homepage audit folio sixteeen design comuni map-lit geojson tickets guest"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./homepage-guest-expected-visual.md
  - ./homepage-guest-visual-comparison.md
  - ./homepage-guest-routing-comparison.md
  - ./stories/homepage-audit-followup-2026-10-07.story.md
  - ../wiki/concepts/homepage-audit-2026-10-07.md
  - ../../../Themes/Sixteen/docs/bmad/homepage-guest-visual-contract.md
---

# Homepage audit — 2026-10-07

## Scopo

Audit della homepage pubblica FixCity (`/{locale}`) per capire se il punto di ingresso guest
è coerente con lo scopo del progetto: far capire il servizio, mostrare le segnalazioni pubbliche
e portare il cittadino a creare o tracciare una segnalazione senza controller HTTP.

## Evidenza studiata

| Area | Evidenza |
|---|---|
| Folio homepage tema | `laravel/Themes/Sixteen/resources/views/pages/index.blade.php` |
| Folio path legacy non montato | `laravel/Themes/Sixteen/pages/[locale]/index.blade.php` |
| Alias `/home` tema | `laravel/Themes/Sixteen/resources/views/pages/home.blade.php` |
| Pagina legacy da non usare | `laravel/Themes/Sixteen/resources/views/pages/homepage.blade.php` |
| Alias modulo Fixcity | `laravel/Modules/Fixcity/resources/views/pages/home.blade.php` |
| API mappa live | `laravel/Modules/Fixcity/resources/views/pages/api/tickets/geojson.blade.php` |
| Contratto visuale | `laravel/Themes/Sixteen/docs/bmad/homepage-guest-visual-contract.md` |
| Specifica attesa | `laravel/Modules/Fixcity/docs/bmad/homepage-guest-expected-visual.md` |
| Confronto precedente | `laravel/Modules/Fixcity/docs/bmad/homepage-guest-visual-comparison.md` |
| Test correlati | `laravel/Modules/Fixcity/tests/Feature/Pages/TicketPagesTest.php`, `tests/Feature/Api/TicketsGeoJsonApiTest.php` |

## Stato attuale sintetico

| Check | Esito | Note |
|---|---:|---|
| Homepage canonica Folio `name('home')` | PASS | `resources/views/pages/index.blade.php` è il path montato da `XotData::getPubThemeViewPath('pages')`; non usa controller. |
| No `HomeController` nel flusso auditato | PASS | L'home studiata è Folio; `/home` del tema e del modulo redirectano alla root locale. |
| Laravel 13 / progetto corrente | PASS | `laravel/composer.json` richiede `laravel/framework:^13.0`. |
| Layout Design Comuni | PASS | `x-pub_theme::layouts.app`, header/footer e `body-page="homepage"`. |
| CTA localizzate | PASS | `LaravelLocalization::localizeURL('/tickets/create')`, `/tickets`, `/auth/login`, `/auth/register`. |
| Mappa live | PASS | `map-lit` usa `/api/tickets/geojson`; API Folio chiama `BuildTicketsGeoJsonAction`. |
| Privacy capability code | PASS correlato | Test mappa/lista e dettaglio API verificano che il code non venga serializzato al pubblico/non-owner. |
| I18n hero/list/how | PASS | Copy da `pub_theme::home.*` in `it` e `en`. |
| Search modal i18n | PASS rispetto al gap precedente | `search-modal.blade.php` usa `pub_theme::ui.*` e `pub_theme::navigation.homepage.maybe_searching`. |
| Pagina legacy `homepage.blade.php` | GAP | Esiste ancora con copy hardcoded, link assoluti e componenti duplicati; non è la SSoT, ma può confondere audit/agenti o diventare route raggiungibile. |
| CSS inline in home | RISOLTO | Le regole hero sono ora nel CSS del tema, con fallback `prefers-reduced-motion`. |
| Test diretto homepage | RISOLTO statico | `HomepageDesignContractTest` copre la view attiva; il runtime HTTP/browser resta da verificare. |
| Browser runtime attuale | GAP non verificato in questa sessione | Non ho avviato server/browser; audit è statico + documentale. |

## Findings

### H-AUD-01 — Homepage canonica corretta ma non protetta da test diretto

La home canonica è `Themes/Sixteen/resources/views/pages/index.blade.php` e dichiara `name('home')`.
È coerente con la regola Folio-only: niente controller, CTA inline localizzate, `map-lit` live.
Il test statico `Themes/Sixteen/tests/Unit/HomepageDesignContractTest.php` assicura nel tempo:

- `/it` e `/en` HTTP 200;
- un solo `<main>`;
- presenza `map-lit` con `/api/tickets/geojson`;
- nessun brand `Laravel`;
- nessun vecchio slug CMS `tests` nella homepage attiva;
- CTA create/list/track localizzate;
- nessun `<style>` inline nella view.

**Priorità:** alta, perché la homepage è entry point e in passato è stata rotta da route/controller/cache.

### H-AUD-02 — Pagina legacy `homepage.blade.php` va marcata o rimossa dal routing operativo

`Themes/Sixteen/resources/views/pages/homepage.blade.php` contiene una homepage alternativa con:

- copy italiano hardcoded;
- link assoluti non localizzati (`/servizi`, `/contatti`, `/notizie`);
- componenti duplicati (`a.btn` + `<x-bootstrap-italia.button>` per la stessa azione);
- contenuto generico comunale non FixCity.

Non è la SSoT della home, ma la presenza del file in `resources/views/pages/` può produrre una route Folio `/homepage` o riattivare vecchi flussi demo.

**Priorità:** media/alta. Non va corretta con una nuova homepage parallela: va trasformata in redirect/archivio o spostata in documentazione, rispettando il workflow forward-only.

### H-AUD-03 — CSS inline hero stabilizzato nel tema

La home contiene CSS inline per `#head-section .btn-hero-*`, link elenco e testo bianco. Il contesto BMAD precedente dice che il CSS serve a prevenire testo invisibile/FOUC.
Le classi ora appartengono al foglio CSS del tema, con scope sui selettori hero e fallback `prefers-reduced-motion`.

**Priorità:** chiusa. Il build CSS e il browser smoke restano gate di rilascio.

### H-AUD-05 — CTA duplicate di stessa intenzione ridotte

La homepage mostrava l’azione di creazione sia nell’hero sia nel blocco finale. Il blocco finale ora porta al tracking di una segnalazione esistente: le due azioni hanno intenzioni diverse.

Le etichette dei tre passaggi non contengono più numerazione decorativa; la sequenza è resa dalla posizione e dal contenuto, in linea con il contratto taste-skill per superfici trust-first.

### H-AUD-04 — Runtime browser da riverificare dopo ogni modifica home/header

La documentazione precedente riporta PASS Puppeteer/Chromium. Questa sessione non ha eseguito un browser audit nuovo.
La DoD aggiornata deve richiedere:

- `/it`, `/en`, `/de`, `/es` se supportate;
- viewport 320/390/768/1024/1440;
- console errors = 0;
- `document.documentElement.scrollWidth <= window.innerWidth`;
- focus visibile sulle CTA;
- mappa con richiesta `/api/tickets/geojson` 200;
- nessun testo hardcoded italiano fuori locale italiana.

**Priorità:** alta per chiudere definitivamente l'audit.

## Decisione BMAD

La homepage è sostanzialmente allineata al contratto guest FixCity. Restano i gate runtime:

1. **Fixcity**: verifica HTTP/browser del contratto homepage e collegamento API GeoJSON.
2. **Sixteen**: gestione separata della pagina legacy `homepage.blade.php` senza riusarla come SSoT.

## Feature mancanti trovate

| Feature mancante | Owner | Evidenza | Prova di completamento |
|---|---|---|---|
| Test contratto homepage guest `/it`/`/en` | Sixteen | view attiva con test statico | `HomepageDesignContractTest` verde + smoke HTTP |
| Redirect/archiviazione pagina legacy `/homepage` | Sixteen | `pages/homepage.blade.php` ancora pagina completa | `/it/homepage` non mostra home parallela; docs aggiornate |
| Browser smoke multilingua homepage | Fixcity + Sixteen | audit corrente non runtime | Playwright/Puppeteer report con viewport e console |
| Tokenizzazione CSS hero | Sixteen | CSS inline rimosso | CSS tema + nessuna regressione FOUC/contrasto |
| Monitor regressione capability code in home map | Fixcity | privacy coperta su lista/API, non su home specifica | test homepage assertDontSee code |

## Verifiche consigliate

```bash
cd laravel
php artisan route:list --path=home
php artisan route:list --path=tickets
vendor/bin/pest Modules/Fixcity/tests/Feature/Pages/TicketPagesTest.php Modules/Fixcity/tests/Feature/Api/TicketsGeoJsonApiTest.php
php artisan view:cache
```

Per browser audit: avviare server Laravel + Vite/asset compilati e usare Playwright/Puppeteer sui viewport indicati.
