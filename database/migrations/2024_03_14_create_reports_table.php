<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Owner Fixcity — reports (connessione fixcity).
 * Bump 2026_07_13: allinea colonna `category` su string per match con categories.id (string primary).
 * Estende XotBaseMigration (regola Laraxot) — down() rimosso.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = \Modules\Fixcity\Models\Report::class;

    public function up(): void
    {
        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->json('location');
                $table->string('address');
                $table->unsignedBigInteger('category');
                $table->string('status');
                $table->json('metadata');
                $table->timestamps();
                $table->index('category');

                // Nessuna FK verso `categories`: la migrazione 2026_06_12_181000 ricrea `categories` con id stringa quando e' vuota,
                // e con questo vincolo su un database nuovo falliva ("Cannot delete or update a parent row"). Vale solo per le
                // installazioni nuove: tableCreate() non tocca una tabella gia' esistente.
            }
        );
    }
};