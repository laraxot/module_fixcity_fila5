---
id: serp-primo-risultato
title: "Primo risultato su Google, Bing e motori AI: brainstorming e piano per cluster di ricerca"
type: story
epic: go-live-pa
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-11-19
updated: 2026-11-19
qmd: "seo serp primo risultato google bing chatgpt perplexity indexnow search console sitemap parole chiave segnalazioni comune buche"
related: [una-codebase-otto-superpoteri, "#102", "#735", "#736", "#737", "#738"]
tags: [seo, serp, marketing, go-live, bmad]
---

# Primo risultato su Google, Bing e motori AI

## Fase BMAD
Analyze → Plan → (Do in corso). Decisioni tecniche nel modulo Seo:
[ADR presenza nei motori](../../../Seo/docs/adr/search-engine-presence.md).

## Da dove partiamo (misurato il 2026-11-19 su https://fixcity.eu)

| Segnale | Stato | Peso |
|---|---|---|
| Indicizzazione | **Nessun risultato** cercando `site:fixcity.eu` né il nome «FixCity»: oggi il sito non esiste per i motori | bloccante |
| `robots.txt` | statico, senza riga `Sitemap:` | alto (Bing e altri) |
| `sitemap.xml` | 60 URL (14 pagine × 4 lingue + home), hreflang corretti, **nessuna segnalazione né articolo**, nessun `lastmod` | alto |
| Produzione vs `dev` | la produzione è indietro: `site.webmanifest` 404, nessun link alle segnalazioni in `/it/tickets` (in locale ci sono, 27 pagine di dettaglio con titolo e descrizione proprie) | alto |
| Tempo di risposta | TTFB 1,3-1,6 s su ogni pagina; `Cache-Control: no-store, private` anche per gli ospiti | medio (Core Web Vitals, crawl budget) |
| CSS | `app-*.css` 3,3 MB (≈260 KB gzip) bloccante nel `<head>`; asset Filament caricati sulle pagine pubbliche | medio (LCP) — theme #102 |
| Head | title e description per pagina, canonical, hreflang it/en/de/es + x-default, OG, robots, JSON-LD `Organization`+`WebSite` in home, `WebPage`+`BreadcrumbList` all'interno | buono |
| Contenuto | home ≈400 parole compresa la cornice; h1 generico «Segnalazioni e servizi per la tua comunità» | medio |
| Verifica motori | nessun meta di Search Console / Bing Webmaster | bloccante per misurare |
| Identità | nessun `sameAs`, `metatag` con valori segnaposto (`facebook_href = 'aa'`, sottotitolo di un altro progetto) | medio |

## Brainstorming: per quali ricerche essere primi

«Primo su Google» non è un traguardo unico: ogni ricerca ha la sua pagina dei risultati e i suoi concorrenti. Quattro
gruppi di ricerche, dal più raggiungibile al più difficile.

### 1. Brand — «fixcity», «fix city segnalazioni», «fixcity comune»
- Raggiungibile in settimane: basta essere indicizzati e riconosciuti come entità.
- Rischio omonimia: «Fix My City» (Fraunhofer FOKUS, app greche), «FixIt», «Fixity». Serve un'entità chiara:
  nome, descrizione, logo, profili ufficiali (`sameAs`), stesso nome ovunque.
- Leve: Search Console + Bing Webmaster, sitemap inviata, profili ufficiali (LinkedIn azienda, GitHub
  dell'organizzazione, scheda Developers Italia), menzioni su siti di settore.

### 2. Chi compra: il Comune (B2G) — «software segnalazioni cittadini», «piattaforma segnalazioni comune»,
«gestione segnalazioni urbane», «segnalazione disservizio Design Comuni», «app segnalazioni MePA»
- Pagina dei risultati di oggi: agendadigitale.eu, Comuni-Chiamo, Municipium (Maggioli), cataloghi (Capterra, G2),
  documenti di singoli Comuni, OpenCity Italia nel catalogo europeo del software libero.
- È il gruppo che porta clienti. Volumi bassi, intenzione altissima, concorrenza poca e datata: **è dove il primo posto è
  realistico e vale di più**.
- Contenuti da costruire (pagine vere, non pagine-esca):
  - «FixCity per i Comuni»: cosa fa, per chi, come si attiva, conformità (Design Comuni, accessibilità, GDPR, cloud
    qualificato ACN), integrazioni (SPID/CIE quando attivo, Open311 GeoReport v2, OpenAPI).
  - Il servizio «Segnalazione disservizio» del modello Design Comuni e il pacchetto PNRR 1.4.1 «Cittadino attivo»: come
    FixCity lo copre, campo per campo.
  - Confronto onesto con le alternative (Comuni-Chiamo, Municipium, OpenCity, sviluppo in casa): tabella di funzioni,
    nessuna affermazione non verificabile.
  - Casi d'uso per tipo di ente (piccolo Comune, Unione di Comuni, Città metropolitana).
- Leve fuori sito: scheda nel catalogo Developers Italia (riuso, se il codice è pubblicabile), MePA, Capterra/G2
  (categoria citizen engagement), articolo su agendadigitale.eu o ForumPA, webinar con ANCI regionali.

### 3. Il cittadino con un problema — «segnalare buca [comune]», «lampione spento chi chiamare», «rifiuti abbandonati
segnalazione», «tombino rotto comune»
- Oggi: Altroconsumo, testate motori, lentepubblica, siti dei singoli Comuni, app comunali.
- Si vince **per Comune cliente**: la pagina pubblica di ogni segnalazione (titolo, descrizione, luogo, stato) e le
  pagine per categoria del Comune rispondono proprio a «buca in via X a Y». Servono Comuni attivi e segnalazioni
  pubbliche: senza clienti questo gruppo non si conquista.
- Attenzione: niente pagine generate in serie per Comuni che non sono clienti (Google le tratta come abuso di contenuti in
  scala). Ogni pagina deve avere dati veri.

### 4. Informativo — «come segnalare una buca al comune», «risarcimento danni buca stradale», «chi ripara le strade
provinciali»
- Oggi dominato da Altroconsumo e testate. Utile per farsi conoscere e per le citazioni nei motori AI.
- Guide pratiche nel blog, con fonti (Codice della strada, art. 2051 c.c.), scritte da chi ha esperienza reale di
  gestione segnalazioni. Il tema risarcimento è legale: va riletto da un esperto prima di pubblicarlo.

### Motori AI (ChatGPT, Perplexity, Copilot, Gemini, AI Overviews)
- Google: stesso indice e stessi sistemi della ricerca; niente file speciali (`llms.txt` non usato).
- ChatGPT e Copilot si appoggiano a Bing: IndexNow + Bing Webmaster Tools.
- Vengono citate le pagine che rispondono in modo chiaro e verificabile a una domanda precisa, con dati propri
  (numeri delle segnalazioni risolte, tempi medi): le metriche di servizio (#730) diventano anche contenuto.
- `robots.txt` oggi non blocca nessun crawler AI: scelta confermata (visibilità prima di tutto).

## Piano

### Fatto in questa story (codice, branch `dev`)
- [x] `robots.txt`: riga `Sitemap: https://fixcity.eu/sitemap.xml`.
- [x] IndexNow nel modulo Seo: chiave del tenant, file `/{chiave}.txt`, `php artisan seo:indexnow` (solo URL nuove),
  chiamato da `deploy.sh` a sito online. ADR + contratto + 9 test.
- [x] JSON-LD della home: `Organization` con `description` e `sameAs` (solo profili https veri).
- [x] Meta di verifica di Google, Bing, Yandex dal `metatag` del tenant (vuoti finché non ci sono i token).
- [x] `metatag` del tenant ripulito dai segnaposto (`facebook_href = 'aa'`, sottotitolo di un altro progetto).

### Serve Marco (account e decisioni) — #738
- [ ] **Deploy di `dev` in produzione**: la produzione non ha le pagine di dettaglio delle segnalazioni né i link
  dall'elenco; finché non va online, i motori vedono 60 pagine quasi vuote.
- [ ] **Search Console** (proprietà di dominio `fixcity.eu`, verifica DNS) → inviare `https://fixcity.eu/sitemap.xml`.
- [ ] **Bing Webmaster Tools** (si può importare da Search Console) → la sitemap e IndexNow si vedono nei report.
- [ ] Profili ufficiali da dichiarare in `metatag.same_as` (LinkedIn azienda, GitHub, eventuale Wikidata).
- [ ] Dati veri per le pagine B2G: clienti (o piloti), prezzi o modalità di acquisto, certificazioni, contatti.

### Prossimi passi tecnici (issue)
- [ ] Velocità: cache delle pagine pubbliche per gli ospiti e intestazioni cacheabili; TTFB < 0,8 s (#735).
- [ ] CSS e JS pubblici: niente asset Filament sulle pagine del sito, CSS critico (theme #102).
- [x] `lastmod` in sitemap con date vere (`SitemapLastModifiedContract`: segnalazioni e articoli) e IndexNow anche per
  le URL cambiate (#736).
- [ ] Pagine di categoria con contenuto proprio e segnalazioni del Comune; `Place`/`GeoCoordinates` sulle segnalazioni.
- [ ] Pagina «FixCity per i Comuni» e confronto, con i dati di Marco (#737).

## Come si misura
- Search Console: pagine indicizzate, impressioni e posizione per gruppo di ricerca (filtri per query), report
  «Generative AI performance».
- Bing Webmaster: URL inviate via IndexNow, pagine indicizzate.
- Traguardi: brand primo entro 4-6 settimane dal deploy; prima pagina B2G con la pagina «per i Comuni» entro 3 mesi;
  gruppi 3 e 4 dopo i primi Comuni attivi.

## Verifica
- Audit di produzione: `curl` di home, robots, sitemap, pagine principali (2026-11-19).
- `vendor/bin/pest Modules/Seo/tests/Feature/IndexNow Modules/Cms/tests/Unit/Actions/Head/BuildHeadSchemaActionTest.php
  Modules/Xot/tests/Unit/Datas/MetatagDataSearchIdentityTest.php`.

## Fonti
- Google Search Central: *Optimizing your website for generative AI features on Google Search*; *AI features and your
  website*; *Top ways to ensure your content performs well in Google's AI experiences on Search* (maggio 2025).
- IndexNow: indexnow.org/documentation.
- Risultati di ricerca osservati per le query dei gruppi 2 e 3 (2026-11-19).
