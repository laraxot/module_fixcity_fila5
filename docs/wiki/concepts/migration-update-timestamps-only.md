---
title: "Migrazioni Fixcity — solo updateTimestamps"
type: concept
module: Fixcity
tags: [migrations, timestamps, xotbase]
created: 2026-06-05
updated: 2026-06-05
qmd: "fixcity migration updateTimestamps no redundant timestamps softDeletes"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/270"
  - "https://github.com/laraxot/module_fixcity_fila5/issues/33"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/271"
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/34"
related:
  - ../../../../../../docs/wiki/rules/migration-update-timestamps-only.md
  - ./profiles-uuid-contract.md
---

# Fixcity — audit columns in migrazione

## Regola

Solo `$this->updateTimestamps(table: $table, hasSoftDeletes: true)` in `tableUpdate` — mai `$table->timestamps()` / `softDeletes()` / `string created_by` nello stesso file.

## Pulizia 2026-06

Ridondanza rimossa in: tickets, ticket_comments, categories, ticket_relations, ticket_activities, ticket_hours, ticket_subscribers. `profiles` allineato a `foreignId` audit via helper.

## Audit

```bash
bashscripts/tools/audit-migration-timestamp-redundancy.sh laravel/Modules/Fixcity/database/migrations
```
