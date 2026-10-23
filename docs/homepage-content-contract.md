---
title: "homepage content contract"
type: contract
status: active
module: Fixcity
updated: 2026-10-23
tags: [bmad, fixcity, homepage, cms, json, folio]
qmd: "homepage content contract home.json folio no pages home blade"
related:
  - ./bmad/homepage-guest-expected-visual.md
  - ../../Themes/Sixteen/docs/bmad/knowledge/why-home-blade-must-not-exist.md
  - ../../Themes/Sixteen/docs/bmad/homepage-guest-visual-contract.md
  - ../../Themes/Sixteen/docs/bmad/filament-blueprint.md
  - ../../../../docs/second-brain/process/sixteen-pages-folder-religion.md
---

# Contratto contenuti homepage FixCity

## Perché (senso)

La homepage è un **servizio civico composto**, non una landing Blade del tema.
Il cittadino vede blocchi (hero, come funziona, track) che il Comune può
cambiare per lingua senza deploy di markup. Filament/CMS possiede i dati;
Sixteen possiede solo il vestito riutilizzabile dei blocchi.

## Dove vive il contenuto (SSOT runtime tenant)

`laravel/config/local/fixcity/database/content/pages/home.json`

- Chiave: `content_blocks.{it|en|de|es}` — stessa sequenza di blocchi per lingua.
- Ogni blocco: `type` + `data.view` + props (URL via `container0.*`, mai `/it` hardcoded).
- Il DB CMS (`Pages.content_blocks` translatable) si allinea a questo JSON;
  aggiornare il JSON e sincronizzare le traduzioni per locale, non inventare
  una seconda pagina.

## Cosa NON è SSOT

| Path | Ruolo |
|------|--------|
| `Themes/Sixteen/config/local/.../home.json` | Fixture/fallback di distribuzione del tema — **non** sostituisce il tenant |
| `Modules/Fixcity/database/content/pages/home.json` | Seed/modulo — allineare al tenant, non viceversa al buio |
| `Themes/Sixteen/resources/views/pages/home.blade.php` | **Vietato** |
| `Themes/Sixteen/resources/views/pages/homepage.blade.php` | **Vietato** |

## Entry point Folio (guscio)

Solo `Themes/Sixteen/resources/views/pages/index.blade.php`:
titolo/description dal CMS → layout → `<x-page slug="home" />`.
Niente hero/mappa/copy in quella vista.

## Come cambiare la home

1. **Contenuto / ordine / CTA / copy** → `config/local/.../home.json` (+ sync CMS).
2. **Aspetto di un blocco** → `Themes/Sixteen/resources/views/components/blocks/...`
   (es. `home/hero.blade.php`) + CSS tema.
3. **Nuova responsabilità visuale** → nuovo blocco riusabile + voce JSON, non
   una Folio page dedicata.

## Perché `pages/home` / `pages/homepage` sono sbagliati

Crearli bypassa CMS e Filament, duplica `name('home')` con `index.blade.php`,
rompe i18n JSON-driven e viola la religione `pages/` (solo `index`, `auth`,
`[container0]`, `tests`). Dettaglio misurato:
[why-home-blade-must-not-exist.md](../../Themes/Sixteen/docs/bmad/knowledge/why-home-blade-must-not-exist.md).

## Collegamenti

- Visuale guest: [homepage-guest-expected-visual.md](./bmad/homepage-guest-expected-visual.md)
- Tema visual + Folio: [homepage-guest-visual-contract.md](../../Themes/Sixteen/docs/bmad/homepage-guest-visual-contract.md)
- Blueprint: [filament-blueprint.md](../../Themes/Sixteen/docs/bmad/filament-blueprint.md)
