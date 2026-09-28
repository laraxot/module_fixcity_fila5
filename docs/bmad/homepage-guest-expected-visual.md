---
title: "homepage guest expected visual"
type: bmad-ux-spec
status: approved
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, sixteen, homepage, guest, design-comuni, visual-contract]
qmd: "homepage guest expected visual design comuni hero map cta"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./stories/STORY-513-public-guest-visual-demo.md
  - ./stories/STORY-514-public-navigation-contract.md
  - ./homepage-guest-visual-comparison.md
  - ./homepage-guest-visual-correction-plan.md
  - ../../../Themes/Sixteen/docs/bmad/homepage-guest-visual-contract.md
  - ../wiki/concepts/user-journey-map.md
---

# Homepage guest — stato visivo atteso

## Scopo e perché

Il visitatore anonimo apre `http://localhost:8001/it` (o `/en`) e deve capire in
un colpo d’occhio: cos’è FixCity, come segnalare, dove sono le segnalazioni
pubbliche. La home non è un mock Tailwind generico: è frontoffice Design Comuni
(tema Sixteen) + dominio FixCity (ticket pubblici).

## Ownership

| Layer | Owner | Responsabilità |
|-------|-------|----------------|
| Dati pubblici, GeoJSON, policy guest | Fixcity | `BuildPublicTicketsQueryAction`, seed `DEMO-*` |
| Layout, CSS, componenti, i18n Blade | Sixteen | `pages/index.blade.php`, header/footer, `map-lit` |
| Verifica browser | entrambi | Puppeteer/Playwright + report BMAD |

## URL e attori

- URL: `/it`, `/en` (stesso layout; copy da cataloghi lingua)
- Attore: visitatore non autenticato (guest)
- Viewport obbligatori: 320, 390, 768, 1024, 1440 px

## Cosa si deve vedere (ordine above-the-fold → sotto)

### 1. Chrome Design Comuni

- Skip link verso contenuto e footer
- Header slim: brand **FixCity** (mai “Laravel”), selettore lingua, area personale
- Navbar temi Design Comuni (Amministrazione / Novità / Servizi / …) senza chiavi grezze `__()`
- Un solo `<main>` con contenuto focusabile

### 2. Hero civica (`#head-section`)

- Sfondo primario ente (verde Design Comuni / BI)
- Overline: `pub_theme::navigation.site_title` → FixCity
- Titolo bianco contrasto alto: `pub_theme::home.hero.title`
- Sottotitolo bianco leggibile: `pub_theme::home.hero.subtitle`
- CTA primaria piena (sfondo bianco, testo verde): “Invia una segnalazione” →
  percorso create (può redirect a login **nella stessa locale**)
- Link “Vai all’elenco” nella hero, verso la lista canonica `/it/tickets`
  (e `/en/tickets` in inglese), con contrasto AA, focus visibile e target di 44 px
- CTA secondaria outline bianca: “Accedi all’area personale”
- Link testo bianco sottolineato: “Crea un account”
- **Nessun box bianco vuoto**, nessun bottone con testo invisibile
- Colonna destra: card con `map-lit` alimentata da `/api/tickets/geojson`
  (marker/cluster dei `DEMO-*` visibili dopo tile load)
- Footer card mappa: caption + link “Vai all’elenco” → `/it/tickets`

### 3. Come funziona

- Titolo + intro da `pub_theme::home.how.*`
- Tre card uguali (1–2–3) in griglia responsive: descrizione / luogo / aggiornamenti
- Spaziatura uniforme; niente `card-big` che gonfia il vuoto verticale

### 4. Banda CTA finale

- Titolo/corpo `pub_theme::home.list.cta_*`
- Bottone primario “Segnala un problema” → stesso percorso create/login localizzato

### 5. Footer

- Brand FixCity, link segnalazioni/tracking esistenti
- Nessuna chiave di traduzione grezza
- Recapiti ente: se non configurati, messaggio esplicito (non inventare dati)

## Asset e runtime

- CSS/JS dallo **stesso host** della pagina (manifest Vite Sixteen compilato);
  non dipende da `localhost:5173` per la demo
- Sprite Bootstrap Italia e logo risolvono 200
- Console: zero errori JS nel percorso nominale guest
- `documentElement.scrollWidth === innerWidth` su tutti i viewport richiesti

## i18n

- Nessuna parola italiana hardcoded nelle Blade della home
- `/en` mostra copy inglese equivalente (stessa struttura)

## Definition of Done (homepage)

- [x] Screenshot 320/390/768/1024/1440 su `/it` e almeno desktop `/en`
- [x] Contrasti hero CTA misurati (primaria bianco/verde; secondarie bianco)
- [x] Mappa visibile con cluster o marker
- [x] Nessun overflow orizzontale
- [x] Nessuna chiave grezza / brand Laravel
- [x] CTA create preserva locale verso login se auth richiesta
- [x] Markup CTA diretto all’elenco localizzato su `/it/tickets` e `/en/tickets`
- [x] Verificare visibilità, contrasto, focus e destinazione del CTA ai viewport 320/390/768/1024/1440 con Chromium: 5.38:1, outline 3 px, target 44 px, zero overflow
- [x] Confronto e piano correttivo aggiornati con esito PASS o gap residui espliciti
