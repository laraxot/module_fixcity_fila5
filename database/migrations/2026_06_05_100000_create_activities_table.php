<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\Activity;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Owner Fixcity — tabella activities (connessione fixcity).
 * Evoluzione schema: modificare QUESTO file e bump timestamp (no migrazioni add/repair).
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = Activity::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            
        });

        $this->tableUpdate(function (Blueprint $table): void {
            $this->updateTimestamps(table: $table, hasSoftDeletes: true);
        });
    }
};
