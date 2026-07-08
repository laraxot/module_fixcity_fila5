---
title: main module e ownership tabella profiles
type: concept
tags:
  - fixcity
  - profile
  - main-module
  - migration
dates:
  created: 2026-06-10
  updated: 2026-06-10
qmd:
  - main_module profiles migration owner
  - Fixcity profiles table
issues: []
discussions: []
---

# Main module e ownership `profiles`

## Business logic

Ogni installazione Laraxot ha un **main_module** (qui: **Fixcity**, da `config/local/fixcity/xra.php`). Il modulo main possiede la tabella `profiles` sulla connessione dominio (`fixcity`), non il modulo generico Profile.

Altri moduli (User, Blog, …) non creano `create_profiles_table`: consumano il profilo via modello/contratti del main.

## Fonte di verità migrazione

`laravel/Modules/Fixcity/database/migrations/2026_06_10_123000_create_profiles_table.php`

Model: `Modules\Fixcity\Models\Profile` — dettaglio: [profiles-uuid-contract](./profiles-uuid-contract.md)

## Regola

1. Una sola migrazione `create_profiles_table` nel modulo **main**
2. Evoluzione = edit file + bump timestamp + `php artisan migrate`
3. Mai `--force`, mai `RefreshDatabase`
4. Vietato duplicare `profiles` in altri moduli

## Collegamenti

- [profiles-uuid-contract](./profiles-uuid-contract.md)
- [one-migration-per-model-rule](./one-migration-per-model-rule.md)
- [activity-log-one-migration](../../../Activity/docs/wiki/concepts/activity-log-one-migration.md)
