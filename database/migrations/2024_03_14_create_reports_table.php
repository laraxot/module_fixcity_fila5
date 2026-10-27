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

                $table->foreign('category')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('cascade');
            }
        );
    }
};