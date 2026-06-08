---
title: "Regola di congruenza modulo-modello (Fixcity)"
type: concept
module: Fixcity
tags: [fixcity, architecture, migrations, models, seeders, factories, parity]
created: 2026-06-05
updated: 2026-06-05
qmd: "fixcity module congruence models migrations seeders factories parity symmetry"
story: STORY-140
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/248"
  - "https://github.com/laraxot/module_fixcity_fila5/issues/27"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/249"
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/28"
related:
  - ./module-artifact-parity-audit.md
  - ../../../../../../docs/wiki/memories/module-symmetry-law.md
---

# Regola di Congruenza Modulo-Modello

## Principio

Ogni modulo deve avere un numero **identico** di file tra quattro categorie:

- **Models** (`app/Models/*.php`)
- **Migrations owner** (`database/migrations/*_create_*_table.php`)
- **Factories** (`database/factories/*Factory.php`)
- **Seeders model-specific** (`database/seeders/*Seeder.php`)

## Fixcity

Audit periodico: confrontare conteggi e allineare prima di nuove feature.

## Collegamenti

- [Module symmetry law](../../../../../../docs/wiki/memories/module-symmetry-law.md)
- [Parità artefatti audit](./module-artifact-parity-audit.md)
- Issue [#248](https://github.com/laraxot/base_fixcity_fila5/issues/248) · module [#27](https://github.com/laraxot/module_fixcity_fila5/issues/27)
