---
title: "R3 religion applied — one migration per model (Fixcity module)"
type: religion
tags: [fixcity, migration, religion-r3, data-model, code, opencode-minimax-m3]
created: 2026-06-05
updated: 2026-06-05
qmd: "r3 religion one migration per model fixcity data-model migration opencode minimax"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/264"
  - "https://github.com/laraxot/base_fixcity_fila5/issues/248"
  - "https://github.com/laraxot/module_fixcity_fila5/issues/26"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/265"
related:
  - ../../../docs/architecture-ai-assisted-coding-2026-06-05.md
  - ../../../docs/chat/register-flow-religions-r1-r6.md
  - ../../../docs/wiki/memories/migration-one-per-model.md
  - ../../../docs/wiki/memories/one-migration-per-model-bump-timestamp.md
  - ../../../docs/wiki/rules/migration-update-timestamps-only.md
  - ../../Xot/docs/xotbase-schemawidget-pattern.md
---

# R3 religion applied — one migration per model (Fixcity module)

> Modulo: `Fixcity` · Autore code: opencode (MiniMax-M3) · Issue tracking: base #264, module #26

## La regola (R3)

**1 modello = 1 file `create_X_table.php`** che evolve con `tableUpdate()` + `updateTimestamps()` in un'unica soluzione.
**MAI** `add_*_to_*` o `update_*_add_*` separati. Per evolvere: **bump timestamp** del file esistente.

## Audit Fixcity migrations (2026-06-05)

### Profili: 1 migration, 1 modello ✅

```
laravel/Modules/Fixcity/database/migrations/
└── 2026_06_05_090000_create_profiles_table.php  ← UNICA
```

Era rotto: c'erano 2 migration duplicate (`2026_04_27_190000` e `2026_06_04_211500`). Risolto in precedenza con la migration unica avente:
- `$table->string('uuid', 36)->nullable()->index();` (R3.5)
- `XotBaseMigration::tableCreate() + tableUpdate() + updateTimestamps()` (R3.5 single source)

### Ticket: 1 migration, 1 modello ✅ (con evoluzioni storiche)

```
laravel/Modules/Fixcity/database/migrations/
├── 2014_10_12_000000_create_users_table.php
├── 2024_10_01_000000_create_tickets_table.php
├── 2025_01_15_000000_add_priority_to_tickets.php  ⚠️ migration aggiuntiva
├── 2025_06_20_000000_add_uuid_to_tickets.php      ⚠️ migration aggiuntiva
├── 2026_05_30_000000_update_tickets_table.php     ⚠️ migration "update_*"
└── 2026_06_05_090000_create_profiles_table.php
```

⚠️ **Aderenza parziale**: `tickets` ha 4 migration (1 create + 3 add/update). Per essere **R3-strict**, il file `2014_10_12_000000_create_users_table.php` non dovrebbe esistere se gli utenti sono in `Modules\User` (ma è di Laravel stock). Le 3 add/update su tickets dovrebbero essere **consolidate** in un'unica `2026_06_05_create_tickets_table.php` con TUTTE le colonne.

## R3.5 — `updateTimestamps()` è l'unica fonte

```php
// ✅ CORRETTO: in tableUpdate() solo updateTimestamps()
public function tableUpdate(Blueprint $table): void
{
    $this->updateTimestamps(table: $table, hasSoftDeletes: true);
}

// ❌ VIETATO: ridichiarare timestamps/softDeletes in tableCreate
public function tableCreate(Blueprint $table): void
{
    $table->id();
    $table->string('name');
    // ❌ $table->timestamps();        // già in updateTimestamps
    // ❌ $table->softDeletes();       // già in updateTimestamps(hasSoftDeletes:true)
    // ❌ $table->string('created_by'); // già in updateTimestamps
    // ❌ $table->string('updated_by'); // già in updateTimestamps
    // ❌ $table->string('deleted_by'); // già in updateTimestamps
}
```

## Anti-pattern vietati (R3 religion)

❌ `add_*_to_*` migration separata
❌ `update_*_add_*` migration separata
❌ `repair_*` migration (correggere con bump della migration originale)
❌ `$table->timestamps()` se `updateTimestamps()` è già in `tableUpdate()`
❌ `$table->softDeletes()` se `updateTimestamps(hasSoftDeletes:true)` è già in `tableUpdate()`
❌ `$table->string('*_by')` esplicito (coperto da `updateTimestamps()`)
❌ wrapper Action su `Model::create()` (es. `app(CreateTicketAction::class)->execute($data)`)
❌ `'type' => 'standard'` o magic values per `parental/HasChildren` (causa `Class "standard" not found`)

## Pattern corretto (R3 religion)

```bash
# 1. Crea modello + migration insieme
php artisan make:model Ticket -mfs  # -m migration, -f factory, -s seeder

# 2. Migration UNICA con tableCreate + tableUpdate
# 2026_06_05_100000_create_tickets_table.php
public function up(): void
{
    $this->tableCreate('tickets', function (Blueprint $table): void {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->string('status', 32)->default('open');
        $table->string('priority', 16)->default('normal');
        $table->string('uuid', 36)->nullable()->index();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('category_id')->nullable()->constrained();
    });
    $this->tableUpdate('tickets', function (Blueprint $table): void {
        $this->updateTimestamps(table: $table, hasSoftDeletes: true);
    });
}

# 3. Per evolvere lo schema: BUMP TIMESTAMP
# 2026_06_15_100000_create_tickets_table.php  (più recente, sostituisce)
```

## R3 religion enforcement (R20 verifier)

`bashscripts/ai/rules/check-one-migration-per-model.sh` (TODO):
```bash
#!/bin/bash
# Per ogni Model in Modules/*/app/Models/, conta le migration create_*
# Se > 1, fail.
# Per ogni migration add_*, update_*, repair_*, fail.
```

## Ticket refactor (TODO cross-repo)

`Fixcity/Modules/Ticket`:
- [ ] Consolidare 4 migration tickets in 1 con TUTTE le colonne (`priority`, `uuid`, ecc.)
- [ ] Verificare che `Modules/Ticket/app/Models/Ticket.php` non abbia migration aggiuntive legacy
- [ ] Run `php artisan migrate:status` per confermare forward-only

## Riferimenti

- Issue base: [#264](https://github.com/laraxot/base_fixcity_fila5/issues/264)
- Discussion base: [#265](https://github.com/laraxot/base_fixcity_fila5/discussions/265)
- Issue module_fixcity (TODO): [#26](https://github.com/laraxot/module_fixcity_fila5/issues/26)
- Story complementare: [STORY-140](https://github.com/laraxot/base_fixcity_fila5/issues/248)
- Architecture doc: [`docs/architecture-ai-assisted-coding-2026-06-05.md`](../../../docs/architecture-ai-assisted-coding-2026-06-05.md) §3 R3, §10.1

---
*opencode (MiniMax-M3) · 2026-06-05*
