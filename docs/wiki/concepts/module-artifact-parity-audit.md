---
title: "Fixcity — audit parità artefatti modulo"
type: concept
module: Fixcity
tags: [fixcity, audit, migrations, factories, seeders, bmad]
created: 2026-06-05
updated: 2026-06-05
qmd: "fixcity module artifact parity migration factory seeder audit N models github"
story: STORY-140
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/248"
  - "https://github.com/laraxot/module_fixcity_fila5/issues/27"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/249"
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/28"
related:
  - ../../../../../../docs/wiki/bmad/architecture-module-model-artifact-parity.md
  - ./one-migration-per-model-rule.md
  - ./data-sacred-migrations.md
---

# Parità artefatti modulo (Fixcity)

## Regola

**N** modelli persistibili owner ⇒ **N** `create_*`, **N** factory, **N** seeder entità.

## Audit

```bash
bashscripts/tools/audit-module-artifact-parity.sh Fixcity
```

## Snapshot 2026-06-04 (post-fix)

| Conteggio | Valore |
|-----------|--------|
| Modelli owner | 9 |
| `create_*` | 10 (9 owner + 1 Filament infra) |
| Factory | 9 ✅ |
| Seeder entità | 9 ✅ |
| Verdict | quasi-OK — 1 migrazione Filament documentata |

### Eccezione create_exports_table

Filament Actions infrastructure migration (STORY-024).
Model fornito dal package Filament, non da `app/Models/` — escluso dal conteggio N.

### Fix 2026-06-04

Creati stub seeder mancanti per completare parità N=N:
`TicketHourSeeder`, `TicketRelationSeeder`, `TicketSubscriberSeeder`.

## Wiki

Ogni `.md` in `Modules/Fixcity/docs/`: frontmatter YAML con `issues` + `discussions` — [wiki-markdown-frontmatter-mandatory.md](../../../../../../docs/wiki/rules/wiki-markdown-frontmatter-mandatory.md).

## Collegamenti

- [module-artifact-parity-snapshot.md](../../../../../../docs/wiki/concepts/module-artifact-parity-snapshot.md)
