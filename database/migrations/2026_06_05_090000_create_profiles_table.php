<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Fixcity\Models\Profile;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Owner Fixcity — UNICA migrazione per `profiles` (connessione fixcity).
 * Evoluzione schema: modificare QUESTO file + bump timestamp nel nome (vietato secondo file create_*).
 * Contratto: docs/wiki/concepts/profiles-uuid-contract.md
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = Profile::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->id();
            $table->string('uuid', 36)->nullable()->index();
            $table->string('user_id', 36)->index()->nullable();
            $table->string('type')->index()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('credits', 12, 2)->nullable();
            $table->string('slug')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('timezone')->nullable();
            $table->string('locale')->nullable();
            $table->json('preferences')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('extra')->nullable();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('uuid')) {
                $table->string('uuid', 36)->nullable()->index();
            }

            if (! $this->hasColumn('user_id')) {
                $table->string('user_id', 36)->index()->nullable();
            } elseif (in_array($this->getColumnType('user_id'), ['integer', 'bigint'], true)) {
                $table->string('user_id', 36)->nullable()->change();
            }

            if (! $this->hasColumn('type')) {
                $table->string('type')->index()->nullable();
            }

            if (! $this->hasColumn('credits')) {
                $table->decimal('credits', 12, 2)->nullable();
            } elseif ($this->hasColumn('credits')) {
                $table->decimal('credits', 12, 2)->nullable()->change();
            }

            if (! $this->hasColumn('slug')) {
                $table->string('slug')->nullable();
            }

            if (! $this->hasColumn('extra')) {
                $table->json('extra')->nullable();
            }

            if (! $this->hasColumn('is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (! $this->hasColumn('preferences')) {
                $table->json('preferences')->nullable();
            }

            if (! $this->hasColumn('status')) {
                $table->string('status')->nullable();
            }

            if (! $this->hasColumn('timezone')) {
                $table->string('timezone')->nullable();
            }

            if (! $this->hasColumn('locale')) {
                $table->string('locale')->nullable();
            }

            if (! $this->hasColumn('avatar')) {
                $table->string('avatar')->nullable();
            }

            if (! $this->hasColumn('bio')) {
                $table->text('bio')->nullable();
            }

            if (! $this->hasColumn('phone')) {
                $table->string('phone')->nullable();
            }

            if (! $this->hasColumn('email')) {
                $table->string('email')->nullable();
            }

            if (! $this->hasColumn('first_name')) {
                $table->string('first_name')->nullable();
            }

            if (! $this->hasColumn('last_name')) {
                $table->string('last_name')->nullable();
            }

            $this->updateTimestamps(table: $table, hasSoftDeletes: true);
        });

        $connection = $this->model->getConnectionName() ?? 'fixcity';

        if ($this->hasColumn('uuid')) {
            DB::connection($connection)
                ->table($this->getTable())
                ->whereNull('uuid')
                ->orderBy('id')
                ->chunkById(100, function ($rows) use ($connection): void {
                    foreach ($rows as $row) {
                        DB::connection($connection)
                            ->table($this->getTable())
                            ->where('id', $row->id)
                            ->update(['uuid' => (string) Str::uuid()]);
                    }
                });
        }
    }
};
