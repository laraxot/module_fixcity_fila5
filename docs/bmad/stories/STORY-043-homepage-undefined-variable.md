---
title: "STORY-043 — Homepage Undefined Variable (superseded)"
type: story
status: superseded
module: Fixcity
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, homepage, superseded, no-controllers]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./STORY-513-public-guest-visual-demo.md
  - ../../wiki/concepts/no-controllers-folio-volt-filament.md
---

# STORY-043 — superseded

Il problema originale (`$loginUrl` undefined perché `HomeController@homepage`
passava solo stats) **non si risolve aggiornando il Controller**.

Religione: FO = Folio + Volt + Filament; BO = Filament. **Mai** `HomeController`.

Stato attuale: home guest = Folio tema Sixteen; home modulo Fixcity = redirect Folio.
Seguire [STORY-513](./STORY-513-public-guest-visual-demo.md) e
[no-controllers-folio-volt-filament](../../wiki/concepts/no-controllers-folio-volt-filament.md).
