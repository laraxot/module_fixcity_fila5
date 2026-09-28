---
title: "homepage guest visual comparison"
type: bmad-comparison
status: done
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, sixteen, homepage, guest, puppeteer, comparison]
qmd: "homepage guest visual comparison expected vs actual puppeteer"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./homepage-guest-expected-visual.md
  - ./homepage-guest-visual-correction-plan.md
  - ./stories/STORY-513-public-guest-visual-demo.md
  - ../../../Themes/Sixteen/docs/bmad/homepage-guest-visual-contract.md
---

# Homepage guest — confronto atteso vs attuale

## Metodo

- Base: `http://127.0.0.1:8001`
- Tool: Puppeteer + Chromium locale (`LD_LIBRARY_PATH` libs estratte)
- Viewport: 320, 390, 768, 1024, 1440 su `/it` e `/en`
- Artefatti sessione: `/tmp/fixcity-visual/bmad-audit/` (non in git)
- Contratto atteso: [homepage-guest-expected-visual.md](./homepage-guest-expected-visual.md)

## Sintesi

| Area | Atteso | Attuale (misurato) | Esito |
|------|--------|--------------------|-------|
| HTTP `/it` `/en` | 200 | 200 | PASS |
| Brand eyebrow | FixCity | FixCity | PASS |
| Hero bg primario | verde ente `#007a52` | `rgb(0, 122, 82)` dopo CSS completo | PASS condizionato |
| Titolo/lead bianchi | contrasto alto | `rgb(255,255,255)` | PASS |
| CTA primaria | bianco / verde | bg bianco, color `#007a52`, h≈48 | PASS |
| CTA secondaria | outline bianco leggibile | bordo 2px bianco, bg trasparente → **bassa presenza** su verde | GAP |
| CTA register | link bianco | ok | PASS |
| Box bianco vuoto | assente | `emptyWhiteInvisible=0` | PASS |
| `main` unico | 1 | 1 | PASS |
| Mappa `map-lit` | marker/cluster | presente, geojson 200, cluster visibili | PASS |
| Come funziona | 3 card | 3 | PASS |
| Overflow X | no | no su tutti i viewport | PASS |
| Chiavi grezze `__()` | no | no | PASS |
| Brand Laravel | no | no | PASS |
| i18n `/en` | copy EN | hero/CTA in inglese | PASS |
| Search modal | nascosta + i18n | CSS `display:none` ok; **copy italiano hardcoded** | GAP |
| First paint / FOUC | chrome Design Comuni subito | CSS head ~1.4 MB: per ~300–600 ms hero senza bg (screenshot unstyled) | GAP |
| Playwright MCP | disponibile | **non esposto** in questa sessione Cursor; usabile solo Playwright locale | GAP tooling |

## Dettaglio gap

### G1 — FOUC / first paint senza Design Comuni

Con `domcontentloaded` / screenshot troppo presto, `#head-section` ha
`background-color` trasparente e il chrome appare “rotto” (icone SVG enormi,
nav verticale). Causa: fogli Vite pesanti; finché non applicati, `bg-primary`
non dipinge. Non è assenza di asset (200 OK), è **ordine/tempo di paint**.

Evidenza: campioni 150/300 ms → `heroBg=none`; ≥600 ms → verde. Screenshot
`_it_1440.png` / `_en_320.png` del batch rapido = falso negativo FOUC.

### G2 — CTA secondaria poco visibile

`btn-hero-secondary`: testo bianco + bordo bianco su verde pieno, sfondo
trasparente. Metriche contrasto testo OK; **presenza visiva** debole (sembra
“bottone verde su verde”). Atteso: outline chiara e percepibile above-the-fold.

### G3 — Search modal non i18n

`search-modal.blade.php` ha stringhe italiane fisse (`Cerca`,
`FORSE STAVI CERCANDO`, suggerimenti CIE/residenza). Su `/en` il modal resta
italiano. Chiavi già presenti: `pub_theme::navigation.*.maybe_searching`,
`pub_theme::ui.search*`.

### G4 — Tooling Playwright MCP

Richiesto dall’utente; namespace MCP non autenticato/assente in sessione.
Mitigazione: Playwright locale (`Modules/Geo/tests/playwright/…`) + Puppeteer
con wait su hero bg.

## Cosa già allineato (non rifare)

- Markup Design Comuni (niente hero Tailwind `bg-blue-950`)
- Classi `btn-hero-*` anti testo invisibile
- Mappa in hero + link elenco localizzato
- Seed `DEMO-*` e geojson pubblici

## Esito post-fix (2026-09-27)

| Gap | Fix | Verifica |
|-----|-----|----------|
| G1 FOUC | critical CSS `#sixteen-critical-fouc` in `main.blade.php` | con CSS Vite ritardato 2.5s hero resta `rgb(0,122,82)` |
| G2 CTA | `btn-hero-secondary` sfondo `rgba(255,255,255,0.14)` | misurato su 320–1440 `/it` `/en` |
| G3 search i18n | `search-modal` → `pub_theme::ui.*` + `navigation.homepage.maybe_searching` + link FixCity | `/en` → “Perhaps you were looking for”; no CIE IT |
| Playwright | locale (MCP assente) | smoke 390/1440 PASS |
| G4 `tickets.list` | nome Illuminate in `Fixcity/routes/web.php` → redirect `/segnalazioni` | `Route::has('tickets.list')=yes`; home Fixcity Folio redirect |

Artefatti: `/tmp/fixcity-visual/bmad-verify/`.

## Verdetto

**PASS** rispetto all’[atteso](./homepage-guest-expected-visual.md) per DoD homepage guest
dopo il [piano correttivo](./homepage-guest-visual-correction-plan.md). Residuo
accettato: mega-nav Design Comuni demo e compressione CSS 1.4 MB (fuori scope).
