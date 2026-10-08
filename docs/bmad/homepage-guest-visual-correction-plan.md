---
title: "homepage guest visual correction plan"
type: bmad-plan
status: done
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, sixteen, homepage, guest, correction-plan]
qmd: "homepage guest visual correction plan fouc cta search i18n"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./homepage-guest-expected-visual.md
  - ./homepage-guest-visual-comparison.md
  - ./stories/STORY-513-public-guest-visual-demo.md
  - ../../../Themes/Sixteen/docs/bmad/homepage-guest-visual-contract.md
  - ../../../Themes/Sixteen/docs/wiki/concepts/homepage-guest-visual-fix.md
---

# Homepage guest — piano correttivo

## Perché

Il confronto mostra tre gap che fanno “vedere male” `/it` in demo reale:
flash unstyled (FOUC), CTA secondaria debole, search modal non tradotta.
La business logic della home (capire FixCity → segnalare → vedere mappa) resta
valida; si sistema solo presentazione Sixteen senza toccare Controllers/Services.

## Ownership

| Gap | Owner | File |
|-----|-------|------|
| G1 FOUC | Sixteen | `components/layouts/main.blade.php` (+ eventuale CSS critico) |
| G2 CTA secondaria | Sixteen | `pages/index.blade.php` (`btn-hero-secondary`) |
| G3 Search i18n | Sixteen | `components/sections/search-modal.blade.php` + lang esistenti |
| Verifica | Fixcity+Sixteen | Puppeteer wait-on-hero + Playwright locale |

## Interventi (KISS, ordine)

### 1. Anti-FOUC critico (G1)

Nel `<head>` del layout `main`, dopo `@vite` CSS, aggiungere **critical CSS
inline minimo** (DRY: stessi token `#007a52`):

- `#head-section.bg-primary { background-color: #007a52 !important; }`
- testo hero bianco di fallback
- nascondere `.modal.search-modal` finché CSS pieno non arriva
  (`display:none` / `visibility:hidden`)

Non rebuild Vite obbligatorio: evita paint “pagina rotta” nei primi 300–600 ms.

### 2. CTA secondaria più presente (G2)

Su `#head-section .btn-hero-secondary` default:

- `background-color: rgba(255,255,255,0.14)`
- bordo bianco 2px, testo bianco
- hover già definito

Obiettivo: bottone riconoscibile senza confondersi con la primaria piena.

### 3. Search modal i18n (G3)

Sostituire hardcode IT con:

- `__('pub_theme::ui.search')` / `search_site_aria` / `close`
- `__('pub_theme::navigation.header.maybe_searching')` (o path reale del file)
- Suggerimenti: o chiavi dedicate FixCity (segnalazioni / tracking) **oppure**
  lista vuota/minima civica già in lang — **niente CIE hardcoded**.

### 4. Verifica

1. Puppeteer: wait `getComputedStyle(#head-section).backgroundColor !== transparent`
   **prima** dello screenshot; campionare a 150 ms che critical CSS già verde.
2. Playwright locale su `:8001` (MCP assente): smoke homepage.
3. Aggiornare comparison → PASS o gap residui espliciti; second brain + chat.

## Fuori scope (non in questo piano)

- Ridisegno mega-nav Design Comuni (Amministrazione / Estate in città…)
- Compressione bundle CSS 1.4 MB (debito Vite separato)
- Playwright MCP cloud (ripristinare auth MCP in sessione successiva)

## Definition of Done

- [x] G1: con CSS Vite ritardato hero già verde grazie a critical CSS
- [x] G2: secondaria con sfondo `rgba(255,255,255,0.14)`
- [x] G3: `/en` search modal senza italiano hardcoded
- [x] Screenshot 320–1440 `/it` + `/en` PASS (`/tmp/fixcity-visual/bmad-verify/`)
- [x] Comparison aggiornato a esito PASS
- [x] Wiki Sixteen + second brain / chat aggiornati
