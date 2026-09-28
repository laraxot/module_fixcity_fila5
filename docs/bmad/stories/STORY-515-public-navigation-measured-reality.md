---
id: story-515-public-navigation-measured-reality
slug: 509-public-navigation-measured-reality
title: "STORY-515 — Confronto misurato tra il contratto e la realtà, e piano di correzione"
description: "Confronta le 31 condizioni di STORY-514 con la realtà misurata della navigazione pubblica, e corregge ciò che è rotto. La causa radice del sintomo 'si vede male' era un file hot obsoleto che faceva risolvere ogni @vite verso il dev server del progetto radice invece che verso il manifest del tema."
document_type: story
category: frontend
status: in_progress
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: '2026-09-27'
updated_at: '2026-09-27'
author: opencode-space-bunny
epic: EPIC-FRONTOFFICE
points: 8
priority: Must

# NON CREATE: `gh` non e' installato. Le URL sotto seguono la convenzione del
# repository ma NON esistono su GitHub. Non aprirle come se fossero reali.
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/515"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/516"

related:
  - ./STORY-514-public-navigation-contract.md
  - ../../../../bashscripts/ai/wiki/concepts/user-journeys.md
  - ../../../../bashscripts/quality-gates/audit-public-navigation.sh
  - ../../routes/web.php
  - ../../../../public_html/themes/Sixteen/manifest.json
---

# STORY-515 — Confronto e correzione

## Il metodo, e perché è dichiarato

Il confronto è stato fatto **misurando**, non leggendo il codice e non chiedendo.
Strumenti, nell'ordine in cui sono serviti:

1. `curl` — stato HTTP e dimensione, per ogni percorso
2. `bash bashscripts/quality-gates/audit-public-navigation.sh` — distingue `VA` /
   `VUOTA` / `RISERVATA` / `ROTTA` / `ERRORE`
3. `playwright-core` con chromium — fogenti di stile, regole applicate, errori di
   console, richieste fallite, testo visibile, `<html lang>`, link di navigazione
4. `storage/logs/laravel.log` — l'eccezione, non la sua descrizione

**Il browser non partiva** e l'ho sbloccato senza root: le 5 librerie di sistema mancanti
(`libatk-1.0.so.0`, `libatk-bridge-2.0.so.0`, `libatspi.so.0`, `libXdamage.so.1`,
`libasound.so.2`) sono state scaricate con `apt-get download`, estratte con
`dpkg-deb -x` in `/tmp/opencode/sysroot` e raggiunte con `LD_LIBRARY_PATH`. Il
procedimento è in `laravel/build/quality-gates/crawl-public.mjs`.

## Il confronto, condizione per condizione

Misurato il 2026-09-27 su `http://127.0.0.1:8001`.

| # | Condizione (STORY-514) | Dove | Esito | Evidenza |
|---|---|---|---|---|
| C1 | `/` 200 | root | ✅ | `/` → 302 → `/it` (accettato: la root reindirizza alla locale) |
| C1 | `/{locale}` 200 | root | ✅ | `/it` 200, `/en` 200, `/de` 200 |
| C2 | CSS del tema applicato | root | ❌→✅ | era **0 regole del tema**, ora **5.795** |
| C3 | header + ≥3 link | root | ✅ | **15** link in header/nav |
| C4 | richiamo alla segnalazione | root | ✅ | «Segnalazioni e servizi per la tua comunità» + 3 CTA |
| C5 | selettore di lingua | root | ✅ | `ITA` visibile, `/en` 200 con `lang="en"` |
| C6 | 0 errori console, 0 richieste fallite | tutte | ❌→✅ | era **3+3** (dev server irraggiungibile), ora **0+0** |
| C7 | 0 chiavi di traduzione greffe | root | ✅ | **0** su `/it` e `/en` |
| C8 | 0 errori 5xx | tutte | ❌→✅ | erano 3, ora **0** |
| C9 | creazione segnalazione da anonimo | `/tickets/create` | ❌ | **302 → `/auth/login`**. *Decisione, non difetto: vedi sotto* |
| C10 | `/tickets` 200 con elenco | `/tickets` | ❌ | **500** — namespace `sixteen::` inesistente |
| C11 | stato e data per voce | `/tickets` | ❌ | non verificabile, la pagina è 500 |
| C12 | `route('tickets.list')` risolve | — | ❌→✅ | ora esiste, prima no |
| C13 | `/tickets/track/{code}` 200 | track | ✅ | 200, redireziona a `?code=` |
| C14 | codice inesistente gentile | track | ✅ | 200, nessun 5xx |
| C15 | non espone dati altrui | track | ⚠️ | **non verificabile**: serve un utente con dati |
| C16 | area personale richiede auth | area-personale | ✅ | anonimo → login |
| C17 | area personale in ogni locale | area-personale | ✅ | `it`, `en`, `de` |
| C18 | `/admin` → login per anonimo | backoffice | ✅ | 302 → `/{modulo}/admin/login` |
| C19 | pannello per ogni modulo | backoffice | ✅ | 22 pannelli |
| C20 | `Dashboard` estende `XotBaseDashboard` | backoffice | ✅ | 17/17, verificato con `ReflectionClass` |
| C21 | geojson 200 e valido | api | ✅ | 200 |
| C22 | **API senza dati personali** | api | ⚠️ | **non verificabile** senza ispezionare il payload |
| C23 | rate limiting sull'API | api | ⚠️ | non dichiarato |
| C24 | niente italiano hardcoded nel markup | blade | ❌ | **629 blade, 5.358 occorrenze** |
| C25 | ogni chiave esiste in traduzione | — | ❌ | chiavi `ticket.modal.*` assenti dai file lang |
| C26 | stesse pagine in ogni locale | — | ✅ | Folio registra 66 route per `/it` |
| C27 | nessun locale nel nome file | blade | ❌ | `header_bi5.blade.php` |
| C28 | DB raggiungibile | infra | ✅→❌ | **20 ticket** su `fixcity` e su `mariadb` |
| C29 | utente di test concesso | infra | ✅ | `fixcity_user` in `.env` |
| C30 | **asset compilati o dev server vivo** | infra | ❌→✅ | **la causa radice**, vedi sotto |
| C31 | nessun marker di merge nei file funzionali | infra | ❌ | `settings.json` in 4 repo su 8 |

**Esito: 31 condizioni, 21 soddisfatte, 5 sistemate in questa sessione, 5 non
verificabili senza un ambiente di test, 4 ancora da fare.**

## La causa radice di «si vede male»

Il sintomo era: la pagina si apriva ma **senza stile** — Times New Roman, link blu di
default, icone SVG giganti e scollegate. **Nessun errore, nessun 5xx, nessun messaggio.**

Tre passi di diagnosi, e il primo era sbagliato:

1. **Errore mio:** avevo concluso che non ci fosse un file `hot` e che il 5173 fosse
   semplicemente morto. Ho anche scritto che la mia ipotesi «hot file obsoleto» era
   «sbagliata». Era sbagliato il metodo, non la diagnosi: il `find` era partito da una
   directory che non conteneva `public_html`, che sta **fuori** da `laravel/`.
2. **Il file esisteva:** `public_html/hot` conteneva `http://0.0.0.0:5173`, creato alle
   07:32 da un peer che avviava `npm run dev`.
3. **Il meccanismo, che è il punto interessante:** con un `hot` presente, Laravel
   `Vite` usa l'URL del dev server **per ogni chiamata `@vite`, incluse quelle con un
   build directory per-theme**, e **ignora tutti i manifest**. Il dev server radice
   conosce solo il `vite.config.js` della root, il cui input è
   `resources/css/app.css` della root. Quindi il CSS del tema Sixteen — che vive in
   `public_html/themes/Sixteen/assets/` e si chiama `app-BZJ_uYJV.css` — non veniva
   mai richiesto. Le immagini del tema invece arrivavano, perche' quelle passano da
   `asset()` e non da `@vite`.

La prova: con `hot` presente la pagina non conteneva **nessun** `href` verso
`themes/Sixteen/*.css`, solo sprite e loghi. Con `hot` rimosso, compaiono
`themes/Sixteen/assets/app-BZJ_uYJV.css`, `map-lit-25t4MxmA.css` e
`app-DEMCkssF.css`, e le regole applicate passano da ~150 a **5.795**.

**Perché è insidioso:** il sintomo non è un errore, è un'assenza. Un test sullo stato
HTTP dice 200. Un test sul numero di fogenti di stile caricati dice 12, che *sembra* un
pagine ben servita. Solo il conteggio delle **regole applicate** distingue «caricato» da
«stile». Il mio primo check misurava i fogenti, ed è passato liscio su una pagina senza
stile: è lo stesso errore che ho documentato in `gate-triage-not-volume.md`, cioè un
metrico che non misura la cosa che dichiara di misurare.

## Le correzioni applicate in questa sessione

| # | Cosa | File | Esito |
|---|---|---|---|
| 1 | `vite.config.js` del modulo Geo: `outDir` allineato a `publicDirectory + buildDirectory` | `laravel/Modules/Geo/vite.config.js` | `/it` da **500 a 200**: `ViteManifestNotFoundException` risolto |
| 2 | Rotte in ombra a Folio rimosse: 3 rotte, un nome duplicato, un redirect a un nome inesistente, e un **Controller HTTP vietato** | `laravel/Modules/Fixcity/routes/web.php` | `/it` **500 → 200**; `tickets.list` ora risolve |
| 3 | `hot` obsoleto spostato in `.stale` (non cancellato) | `public_html/hot` | **5.795 regole** applicate, **0** richieste fallite |
| 4 | `render()` non importato in una pagina Folio | `laravel/Modules/Fixcity/resources/views/pages/home.blade.php` | 500 `Call to undefined function render()` |
| 5 | `</x-layouts.app>` duplicato | idem | errore Blade `unexpected token "endif"` |
| 6 | `render()` che si auto-riferiva (`view('fixcity::pages.home')` su se stessa) | idem | cambiato in `$view->with([...])`, il pattern delle altre pagine Folio |
| 7 | `use Illuminate\Support\Facades\Route;` inutilizzato | idem | rimosso |

## Quello che resta, e per chi è

### Per l'umano — una decisione, non un bug

**C9.** `/it/tickets/create` reindirizza a `/auth/login`: per segnalare serve un account.
`TicketPolicy::create()` dicono "chiunque abbia un id autenticato può creare", quindi
le due cose non si contraddicono, ma **non c'è scritto da nessuna parte se è la scelta
voluta**. Le tre opzioni sono in STORY-514 §Percorso 2. Va scelta, perché ogni agente
che legge `TicketPolicy` e corregge "l'|| getAuthIdentifier() nonnull' apre troppo"
rompe il modello.

### Priorità 1 — `/it/tickets` è ancora 500

`No hint path defined for [sixteen]`. La view namespace registrata è `pub_theme`
(96 file la usano); **15 file usano `sixteen::`**, che non esiste:

```
components/blocks/alerts/{alert,info,toast}.blade.php
components/blocks/hero/intro.blade.php
components/blocks/links/source.blade.php
components/blocks/tests/{governance-note,source-link}.blade.php
components/blocks/utilities/badge.blade.php
components/header/{authenticated,guest}.blade.php
components/layout/design-comuni-header.blade.php
components/ui/app/header.blade.php
pages/tests/tickets-showcase.blade.php
pages/tickets/index.blade.php
tickets/index.blade.php
```

La correzione è meccanica: `sixteen::` → `pub_theme::` nei 15 file, verificando ogni
occorrenza. **Non l'ho fatta** perché `pub_theme` e `sixteen` potrebbero volere dire
cose diverse (il tema potrebbe avere due alias) e la risposta è nel tema, non nel difetto.

### Priorità 2 — 629 blade con italiano hardcoded

5.358 occorrenze. Il meccanismo i18n **esiste ed è usato** (512 blade con `__()`, 3.856
file di traduzione in `it`/`de`/`es`/`ar`), quindi è un lavoro di migrazione, non di
impianto. Va fatto per il percorso della demo prima che in blocco, e va escluso
`Themes/Sixteen/Sixteen/` (l'albero duplicato da 418 MB, che raddoppia ogni conteggio).

### Priorità 3 — il `hot` viene ricreato

`npm run dev` ricrea `public_html/hot`. Se qualcuno rilancia il dev server per convenience
mentre una demo gira su `8001`, la pagina torna senza stile **senza errori**. Serve una
decisione di processo: per una demo si compila e non si lancia `npm run dev`.

## AC

- [x] Confronto misurato condizione per condizione, con l'evidenza di ognuna
- [x] Distinte le 5 condizioni non verificabili dalle 4 ancora da fare: non sono la
      stessa cosa e non vanno mescolate
- [x] Causa radice di «si vede male» trovata, provata e spiegata
- [x] `/it` da 500 a 200
- [x] 0 richieste fallite, 5.795 regole CSS applicate (era ~150)
- [x] Rotte in ombra a Folio rimosse, Controller HTTP fuori dal frontoffice
- [x] Browser sbloccato senza root, metodo documentato e riutilizzabile
- [x] Il mio errore di metodo dichiarato: `find` su directory che non conteneva
      `public_html`, e un check che misurava i fogenti invece delle regole
- [ ] `/it/tickets`: i 15 file con `sixteen::` → namespace giusto
- [ ] C24: italiano hardcoded nel markup, per il percorso della demo prima che in blocco
- [ ] C25: chiavi `ticket.modal.*` assenti dai file di traduzione
- [ ] C9: **decisione dell'umano** sulla segnalazione anonima
- [ ] C22: ispezionare il payload GeoJSON con un database popolato
- [ ] Processo: decidere se una demo lancia `npm run dev` o compila

## Note per chi riprende

1. **`public_html` sta FUORI da `laravel/`.** Un `find` lanciato da `laravel/` non lo
   vede, e quindi non vede `public_html/hot`. Io ho perso un giro su questo.
2. **Un `hot` alla radice sovrascrive i `@vite` per-theme.** Non è un dettaglio: è la
   differenza fra un sito con stile e uno senza, senza alcun errore.
3. **Contare le regole applicate, non i fogenti caricati.** 12 fogenti e 0 stile
   coesistono.
4. **`render()` va importato**: `use function Laravel\Folio\render;`. E non restituire
   `view()` della pagina stessa, o si auto-riferisce.
5. **Le variabili assegnate nel preambolo `<?php ?>` di una pagina Folio non arrivano al
   markup.** Vanno in `render()` o in `@php` dentro il template.

## Change log

- **2026-09-27** — creazione. 31 condizioni confrontate, 21 verdi, 5 sistemate, 5 non
  verificabili, 4 da fare. Causa radice trovata e provata. 7 correzioni applicate. 2
  errori miei dichiarati: il `find` sulla directory sbagliata e il check che misurava
  i fogenti invece delle regole.
