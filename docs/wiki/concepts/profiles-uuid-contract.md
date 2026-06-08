# Profiles UUID Contract

## Contratto

Nel modulo Fixcity, `profiles` (connessione `fixcity`) deve avere:

- `id` intero auto-increment — chiave relazionale interna
- `uuid` char(36) nullable indexed — identificatore esterno (`BaseProfile` lo genera in `creating`)
- `credits` nullable — insert profilo minimale (`user_id` + `uuid`) non deve fallire

## Fonte di verità (unica)

**Un solo file migrazione owner** — regola [one migration per model](../../../../../../docs/wiki/agents/rules/one-migration-per-model.md):

`laravel/Modules/Fixcity/database/migrations/2026_06_05_090000_create_profiles_table.php`

- Model: `Modules\Fixcity\Models\Profile`
- Pattern: `tableCreate` + `tableUpdate` idempotente + backfill `uuid` null

## Regola operativa

| Azione | Consentito |
|--------|------------|
| Manca colonna su DB legacy | Edit file owner → **bump timestamp** nel nome file → `php artisan migrate` |
| Nuovo campo | Stesso file owner + bump timestamp |
| `add_uuid_to_profiles_table` | **Vietato** |
| Secondo `create_profiles_table` | **Vietato** |
| Migrazione `profiles` in User/Blog | **Vietato** (owner = Fixcity) |

## Bump timestamp (come)

```bash
cd laravel/Modules/Fixcity/database/migrations
mv 2026_06_05_090000_create_profiles_table.php \
   2026_06_05_120000_create_profiles_table.php
cd ../../../..
php artisan migrate
```

**Mai** `--force` — [dati sacri](../../../../../../docs/wiki/rules/data-sacred-no-destructive-db.md).

Aggiornare questo concept e `docs/wiki/log.md` quando si bumpa.

## Runtime

```bash
cd laravel
php artisan migrate
```

Mai `--force`. Mai `migrate --path` su singolo file — [dati sacri](../../../../../../docs/wiki/rules/data-sacred-no-destructive-db.md).

## Collegamenti

- [profiles-ownership-boundary-rule](../../../User/docs/wiki/concepts/profiles-ownership-boundary-rule.md) (User module)
- [one-migration-per-model-bump-timestamp](../../../../../../docs/wiki/memories/one-migration-per-model-bump-timestamp.md)
- [Fixcity wiki log](../log.md)
