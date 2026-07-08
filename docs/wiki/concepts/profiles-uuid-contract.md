---
title: "profiles — contratto uuid (owner Fixcity)"
type: concept
tags: [fixcity, profiles, migration, uuid, main-module]
created: 2026-06-10
updated: 2026-06-10
qmd: profiles uuid migration owner Fixcity main_module bump timestamp
issues: []
discussions: []
---

# Profiles UUID Contract

## Contratto

Nel modulo Fixcity, `profiles` (connessione `fixcity`) deve avere:

- `id` intero auto-increment — chiave relazionale interna
- `uuid` char(36) nullable indexed — identificatore esterno (`BaseProfile` lo genera in `creating`)
- `credits` nullable — insert profilo minimale (`user_id` + `uuid`) non deve fallire
- `user_id` string(36) — allineato a `users.id` UUID/ULID

## Fonte di verità (unica)

`laravel/Modules/Fixcity/database/migrations/2026_06_10_123000_create_profiles_table.php`

- Model: `Modules\Fixcity\Models\Profile`
- Pattern: `tableCreate` + `tableUpdate` idempotente + backfill `uuid` null

## Regola operativa

| Azione | Consentito |
|--------|------------|
| Manca colonna su DB legacy | Edit owner → **bump timestamp** → `php artisan migrate` |
| Secondo `create_profiles_table` | **Vietato** |
| Migrazione `profiles` in User/Blog | **Vietato** — owner = main_module Fixcity |

## Boundary User

Runtime profilo può essere referenziato da User; **schema** resta in Fixcity.

Vedi [profile-migration-uuid-contract](../../../User/docs/wiki/concepts/profile-migration-uuid-contract.md).

## Collegamenti

- Memoria root: [main-module-profiles-migration-owner.md](../../../../docs/wiki/memories/main-module-profiles-migration-owner.md)
