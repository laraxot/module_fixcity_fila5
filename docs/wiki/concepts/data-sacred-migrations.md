---
title: "Dati sacri — migrazioni Fixcity"
type: concept
module: Fixcity
tags: [fixcity, migrations, data, sacred, forward-only]
created: 2026-06-05
updated: 2026-06-05
qmd: "fixcity data sacred migrate no force no path RefreshDatabase forward only"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/266"
  - "https://github.com/laraxot/module_fixcity_fila5/issues/29"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/267"
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/30"
related:
  - ../../../../../../docs/wiki/rules/data-sacred-no-destructive-db.md
  - ../../../../../../docs/wiki/memories/data-sacred-no-destructive-db.md
---

# Dati sacri — migrazioni Fixcity

## Regola

Su DB `fixcity` (e in generale nel progetto) **non** si usano:

- `php artisan migrate --force`
- `php artisan migrate --path=Modules/Fixcity/database/migrations/..._create_*_table.php`
- `migrate:fresh` / `RefreshDatabase`

## Workflow corretto

```bash
cd laravel && php artisan migrate
```

Forward-only: i dati locali e di staging sono sacri come in produzione.

## Collegamenti

- [Rule root](../../../../../../docs/wiki/rules/data-sacred-no-destructive-db.md)
- Issue [#266](https://github.com/laraxot/base_fixcity_fila5/issues/266) · Discussion [#267](https://github.com/laraxot/base_fixcity_fila5/discussions/267)
