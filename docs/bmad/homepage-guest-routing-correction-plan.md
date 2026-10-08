---
title: "homepage guest routing correction plan"
type: bmad-plan
status: done
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, sixteen, homepage, folio, correction-plan]
qmd: "homepage guest routing correction plan folio localizeurl no controller"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./homepage-guest-routing-expected.md
  - ./homepage-guest-routing-comparison.md
  - ../wiki/concepts/no-controllers-folio-volt-filament.md
  - ../../../../docs/second-brain/process/folio-volt-app-pages.md
---

# Homepage guest — piano correttivo routing

## Perché

Chiudere i 500 su `/it` (`HomeController`, `$loginUrl`) senza ricreare Controllers:
solo Folio + Blade autosufficiente + Actions per i dati.

## Interventi (ordine KISS)

### 1. SSoT home Folio (Sixteen)

- `pages/index.blade.php` = unica home `/{locale}` (`name('home')`)
- `pages/home.blade.php` = Folio `render` redirect a `localizeURL('/')` (niente doppio markup)
- URL CTA **inline** con `LaravelLocalization::localizeURL(...)` — zero `$loginUrl` da Controller
- Link elenco → `/segnalazioni`

### 2. Nessun Controller FO

- Confermare assenza `Modules/Fixcity/app/Http/Controllers/HomeController.php`
- `routes/web.php` solo commento ownership (no `it.home`, no view)
- Se riappare: **cancellare**, non “fixare” il Controller (STORY-043 superseded)

### 3. Cache

- `view:clear` dopo edit Blade
- Hit `/it` per refresh FolioRoutes se serve

### 4. Verifica

- HTTP 200, action Folio
- HTML contiene CTA login localizzata, nessun Exception
- Second brain + BMAD comparison → PASS

## Fuori scope

- Mega-nav Design Comuni demo
- Compressione CSS Vite

## Definition of Done

- [x] Trilogy routing aggiornata a PASS
- [x] `/it` Folio 200 senza `$loginUrl` undefined (CTA via `localizeURL` inline)
- [x] Nessun HomeController su disk
- [x] Docs second brain aggiornati
