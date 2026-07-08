---
title: "Timestamps Consolidation - Single Source of Truth"
type: documentation
tags: [timestamps, dry-kiss, migration, idempotent, xotbasemigration]
created: 2026-06-05
updated: 2026-06-05
qmd: "timestamps consolidation single source of truth"
related:
  - XOTBASEMIGRATION_TIMESTAMPS_RULE.md
  - ../wiki/rules/module-congruence-rule.md
  - ../wiki/patterns/daa-efficient-data-structures.md
---

# Timestamps Consolidation

## 🎯 Objective
Eliminate redundant timestamp declarations across migrations to ensure a **single source of truth** for `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_at`, and `deleted_by`.

## 📜 Why Consolidate?

1. **DRY / KISS** – One place to modify timestamp behavior.
2. **Idempotent** – `updateTimestamps()` checks existence before creating fields.
3. **Consistency** – All modules use the same `updateTimestamps()` signature.
4. **Safety** – Avoids accidental column drops or unintended overwrites.

## ✅ Recommended Pattern (XotBaseMigration)

```php
public function up(): void
{
    // CREATE – add all new columns here
    $this->tableCreate(static function (Blueprint $table): void {
        $table->id();
        $table->string('name')->nullable();
    });

    // UPDATE – idempotently add columns
    $this->tableUpdate(static function (Blueprint $table): void {
        // Example: add a new column only if it doesn't exist yet
        if (! $this->hasColumn('status')) {
            $table->enum('status', ['open', 'closed', 'pending'])->default('open');
        }

        // Timestamps & audit actors (only once)
        $this->updateTimestamps(
            table: $table,
            hasSoftDeletes: true,                // adds softDeletes()
            auditColumns: ['created_by', 'updated_by', 'deleted_by']
        );
    });
}
```

## 🚫 Forbidden Patterns

| Anti‑Pattern | Consequence | Fix |
|--------------|-----------|-----|
| `$table->timestamps()` + `$table->softDeletes()` in multiple files | Duplicate column definitions → migrations fail or produce errors | Remove the duplicated calls; rely on `updateTimestamps()` |
| Manual `created_by`, `updated_by`, `deleted_by` columns outside of `updateTimestamps()` | Scattered logic → hard to audit | Centralize in `updateTimestamps()` (it injects correct foreign‑key types) |
| Multiple migrations targeting the **same** table | Conflicts, circular dependencies | Keep **one migration per table**; use versioned timestamp in filename to sequence changes |

## 🛠️ Migration Lifecycle

1. **Identify** the existing migration (by filename timestamp).  
2. **Edit** the file – add new columns inside `tableCreate()` **or** `tableUpdate()` as appropriate.  
3. **Rename** the filename timestamp to reflect the change (e.g., `2026_03_12_170000_create_users_table.php` → `2026_03_12_171000_create_users_table.php`).  
4. **Run**: `php artisan migrate --path=Modules/.../database/migrations/2026_03_12_171000_create_users_table.php`  
5. **Verify** with `php artisan migrate:status` and functional testing.

## 📋 Checklist for Every New Table Migration

- [ ] Only **one** migration per table (create it once).  
- [ ] Use `$this->tableCreate()` for initial schema, **never** add columns there later.  
- [ ] All future changes go into **`tableUpdate()`**.  
- [ ] Append new columns ONLY if `! $this->hasColumn('new_column')`.  
- [ ] Call `$this->updateTimestamps(table: $table, hasSoftDeletes: true)` **once**.  
- [ ] Update the migration filename timestamp when logic changes.

## 🔄 Example Evolution

| Version | Change |
|---------|--------|
| `2026_03_12_170000_create_users_table.php` | Initial create with `updateTimestamps()` |
| `2026_03_12_171000_create_users_table.php` | Added `status` column inside `tableUpdate()` guard. |
| `2026_03_20_090000_add_two_factor_secret.php` | New table `two_factor_secrets` created, timestamps via `updateTimestamps()`. |

## 📚 Related Documentation

- **[XotBaseMigration Timestamps Rule](XOTBASEMIGRATION_TIMESTAMPS_RULE.md)**
- **[Migration Philosophy](MIGRATION_PHILOSOPHY.md)**
- [Code Review Checklist](REFACTOR_CHECKLIST.md)

---

*Prepared for the **Fixcity** module – version 1.0, 2026‑06‑05.*