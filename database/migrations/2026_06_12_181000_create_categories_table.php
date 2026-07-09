<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Models\Category;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Owner Fixcity — categories (connessione fixcity).
 * Bump 2026_06_12: allinea colonna `name` su DB legacy title/slug.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = Category::class;

    public function up(): void
    {
        // -- CREATE --
        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('name');
                $table->text('description');
// Laraxot — see module docs/wiki for domain contract.
                $table->string('icon');
                $table->index('name', 'categories_name_idx');
            }
        );

        // -- UPDATE --
        $this->tableUpdate(
            function (Blueprint $table): void {
                if (! $this->hasColumn('name')) {
                    $table->string('name')->nullable();
                }

                if (! $this->hasColumn('parent_id')) {
                    $table->string('parent_id')->nullable();
                    $table->index('parent_id', 'categories_parent_id_idx');
                }

                if (! $this->hasColumn('is_active')) {
                    $table->boolean('is_active')->default(true);
                    $table->index('is_active', 'categories_is_active_idx');
                }

                if (! $this->hasColumn('sort_order')) {
                    $table->integer('sort_order')->default(0);
                    $table->index('sort_order', 'categories_sort_order_idx');
                }

                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );

        $connection = $this->model->getConnectionName() ?? 'fixcity';

        if ($this->hasColumn('name') && $this->hasColumn('title')) {
            DB::connection($connection)->statement(
                'UPDATE categories SET name = title WHERE (name IS NULL OR name = \'\') AND title IS NOT NULL'
            );
        }

        $this->reconcileStringPrimaryKeyWhenEmpty($connection);
    }

    private function reconcileStringPrimaryKeyWhenEmpty(string $connection): void
    {
        if (! $this->tableExists()) {
            return;
        }

        $idType = $this->getColumnType('id');
        if (! in_array($idType, ['bigint', 'integer', 'int'], true)) {
            return;
        }

        $count = (int) DB::connection($connection)->table($this->getTable())->count();
        if ($count > 0) {
            return;
        }

        $this->getConn()->drop($this->getTable());

        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('name');
                $table->text('description');
                $table->string('icon');
                $table->index('name', 'categories_name_idx');
            }
        );

        $this->tableUpdate(
            function (Blueprint $table): void {
                if (! $this->hasColumn('parent_id')) {
                    $table->string('parent_id')->nullable();
                    $table->index('parent_id', 'categories_parent_id_idx');
                }

                if (! $this->hasColumn('is_active')) {
                    $table->boolean('is_active')->default(true);
                    $table->index('is_active', 'categories_is_active_idx');
                }

                if (! $this->hasColumn('sort_order')) {
                    $table->integer('sort_order')->default(0);
                    $table->index('sort_order', 'categories_sort_order_idx');
                }

                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }
};
