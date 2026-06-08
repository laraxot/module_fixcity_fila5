---
type: concept
module: Fixcity
updated: 2026-06-05
qmd: "fixcity one migration per model profiles owner bump timestamp"
---

# One migration per model (Fixcity)

## Scopo

Nel modulo Fixcity ogni tabella owner (es. `tickets`, `profiles`) ha **una sola** migrazione `create_{table}_table.php`. Lo schema evolve dentro quel file, non con file aggiuntivi.

## Owner `profiles`

| Elemento | Valore |
|----------|--------|
| Connessione | `fixcity` |
| Modello | `Modules\Fixcity\Models\Profile` |
| Migrazione canonica | `database/migrations/2026_06_05_090000_create_profiles_table.php` |
| Vietato | Secondo `create_profiles_table` nello stesso modulo |
| Vietato | `create_profiles` attivo in User/Blog (archiviati in `_bak/*.merged`) |

## Bump timestamp

Quando aggiungi o modifichi colonne:

1. Edita il file canonico (`tableUpdate` con `hasColumn()`).
2. Rinomina: `2026_06_05_090000_...` → `2026_06_05_HHMMSS_...` (timestamp nuovo).
3. `php artisan migrate` o `php artisan migrate --database=fixcity` — **mai** `--force`, **mai** `--path` su singolo file ([dati sacri](../../../../../../docs/wiki/rules/data-sacred-no-destructive-db.md)).
4. Aggiorna [profiles-uuid-contract](./profiles-uuid-contract.md) e [log](../log.md).

## Collegamenti

- [profiles-uuid-contract](./profiles-uuid-contract.md)
- [architecture-one-migration-per-model](../../../../../docs/wiki/bmad/architecture-one-migration-per-model.md)
- [profiles-ownership-boundary-rule](../../../User/docs/wiki/concepts/profiles-ownership-boundary-rule.md)
