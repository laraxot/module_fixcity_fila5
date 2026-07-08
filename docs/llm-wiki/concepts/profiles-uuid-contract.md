# Profiles UUID Contract

> Mirror: [wiki/concepts/profiles-uuid-contract.md](../../wiki/concepts/profiles-uuid-contract.md)

## Sintesi

- Owner: modulo **Fixcity**, connessione `fixcity`
- **1 modello = 1 migrazione**: `2026_06_05_090000_create_profiles_table.php` (unico file `create_profiles_table`)
- Contratto: `id` bigint, `uuid` nullable, `credits` nullable
- Evoluzione: edit + **rename timestamp** — no `add_*` / no secondo `create_*`

Migrate: solo `php artisan migrate` — mai `--force`, mai `--path` su singolo file ([dati sacri](../../../../../../docs/wiki/rules/data-sacred-no-destructive-db.md)).
