# Migration Foreign Key Errors

## Context
During module migrations (e.g., `php artisan migrate`), a `QueryException` can be thrown: `General error: 1005 Can't create table ... (errno: 150 "Foreign key constraint is incorrectly formed")`.

## Problem
This error typically occurs when the column definition in the referencing table does not perfectly match the type and attributes of the referenced table's primary key.
For example, in `Fixcity` module, the `categories` table uses `bigint(20) unsigned` for its `id` column, but a legacy `reports` table might have attempted to reference it using a `string('category')` column.

## Solution

### Best Practices
- **Use `foreignIdFor()`:** For an Eloquent relation, use `$table->foreignIdFor(\Modules\Fixcity\Models\Category::class, 'category_id')->constrained()->cascadeOnDelete();`. It derives key type and table/key name from the model. Add `constrained()` only when the referenced model shares the same database connection; for a configurable User model use `XotData::make()->getUserClass()` without a physical cross-connection constraint.
- **Check Legacy Migrations:** When adopting older modules or refactoring (like moving from String IDs to UUIDs or BigInts), ensure all pivot tables and foreign keys are updated simultaneously.
- **Table Creation Order:** Ensure the referenced table (e.g., `categories`) is migrated *before* the referencing table (e.g., `reports`). Prefix migration filenames with timestamps to control this order.

### Bad Practices
- ❌ Using `$table->string('category_id')` to reference an `id` that is `bigint` or `uuid`.
- ❌ Hardcoding `->unsignedBigInteger('category_id')` or using bare `foreignId('category_id')` when an Eloquent model owns the relation. Prefer `foreignIdFor(\Modules\Fixcity\Models\Category::class, 'category_id')`.

### False Friends
- **String `id` vs BigInt `id`:** Just because a value "looks" like a string (e.g. `'strade'`) does not mean the table's `id` column is a `string`. Always check the source migration of the referenced table (e.g. `XotBaseMigration` might force conversion to BigInt + UUID).

## Related Links
- [Laravel Database: Migrations - Foreign Key Constraints](https://laravel.com/docs/11.x/migrations#foreign-key-constraints)
- [MariaDB Error 150](https://mariadb.com/kb/en/foreign-keys/)
