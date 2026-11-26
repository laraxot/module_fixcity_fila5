---
title: "homepage guest routing expected"
type: bmad-ux-spec
status: approved
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, sixteen, homepage, folio, no-controllers, routing]
qmd: "homepage guest routing expected folio volt no homecontroller"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./homepage-guest-expected-visual.md
  - ./homepage-guest-routing-comparison.md
  - ./homepage-guest-routing-correction-plan.md
  - ../wiki/concepts/no-controllers-folio-volt-filament.md
  - ./stories/STORY-513-public-guest-visual-demo.md
---

# Homepage guest `/it` — routing e ownership attesi

## Perché

Il cittadino apre `http://localhost:8001/it` e deve vedere la home Design Comuni
senza 500. La religione FO/BO vieta Controller: la home è una **pagina Folio**
del tema Sixteen, non `HomeController@homepage`.

## Ownership

| Concern | Owner | Artefatto |
|---------|-------|-----------|
| URL `/{locale}` (home) | Sixteen Folio | `resources/views/pages/index.blade.php` (`name('home')`) |
| URL `/{locale}/home` | Sixteen Folio | redirect a `/{locale}` **oppure** stesso markup autosufficiente |
| Chrome / CTA / mappa | Sixteen | Blade + `localizeURL` + `map-lit` |
| Dati mappa | Fixcity | Folio `pages/api/tickets/geojson` + Actions |
| BO | Fixcity Filament | `XotBase*` — fuori scope home guest |

## Contratto tecnico (Definition of Done routing)

1. `GET /it` → **200**, action = `Laravel\Folio\FolioManager@handle` (mai `HomeController`)
2. Nessuna rotta `it.home` verso Controller
3. `Modules/Fixcity/app/Http/Controllers/` vuota (o assente)
4. `Modules/Fixcity/routes/web.php` senza closure FO / view
5. Blade home **autosufficiente**: URL CTA con `LaravelLocalization::localizeURL(...)`
   inline (niente dipendenza da variabili iniettate da un Controller)
6. Elenco pubblico CTA → `/segnalazioni` (SSoT lista), non loop con `/tickets`
7. Zero `Undefined variable $loginUrl` / `$createUrl` / ecc.

## Cosa si deve vedere (funzionale)

Come da [homepage-guest-expected-visual.md](./homepage-guest-expected-visual.md):
hero FixCity, CTA create/login/register, mappa, come funziona, banda CTA, footer.
