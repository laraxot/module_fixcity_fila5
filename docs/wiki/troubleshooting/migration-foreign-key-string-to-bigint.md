# Migration Foreign Key Errors

## Context
During module migrations (e.g., `php artisan migrate`), a `QueryException` can be thrown: `General error: 1005 Can't create table ... (errno: 150 "Foreign key constraint is incorrectly formed")`.

## Problem
This error typically occurs when the column definition in the referencing table does not perfectly match the type and attributes of the referenced table's primary key.
For example, in `Fixcity` module, the `categories` table uses `bigint(20) unsigned` for its `id` column, but a legacy `reports` table might have attempted to reference it using a `string('category')` column.

## Solution

### Best Practices
- **Use `foreignId()`:** Always use `$table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();` which automatically ensures the type matches `bigint unsigned` and creates the foreign key constraint.
- **Check Legacy Migrations:** When adopting older modules or refactoring (like moving from String IDs to UUIDs or BigInts), ensure all pivot tables and foreign keys are updated simultaneously.
- **Table Creation Order:** Ensure the referenced table (e.g., `categories`) is migrated *before* the referencing table (e.g., `reports`). Prefix migration filenames with timestamps to control this order.

### Bad Practices
- ❌ Using `$table->string('category_id')` to reference an `id` that is `bigint` or `uuid`.
- ❌ Hardcoding `->unsignedBigInteger('category_id')` when `->foreignIdFor(Category::class)` or `->foreignId('category_id')` is cleaner, more DRY, and robust.

### False Friends
- **String `id` vs BigInt `id`:** Just because a value "looks" like a string (e.g. `'strade'`) does not mean the table's `id` column is a `string`. Always check the source migration of the referenced table (e.g. `XotBaseMigration` might force conversion to BigInt + UUID).

## Related Links
- [Laravel Database: Migrations - Foreign Key Constraints](https://laravel.com/docs/11.x/migrations#foreign-key-constraints)
- [MariaDB Error 150](https://mariadb.com/kb/en/foreign-keys/)
