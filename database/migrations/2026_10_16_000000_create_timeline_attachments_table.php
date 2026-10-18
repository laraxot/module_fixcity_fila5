<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Owner Fixcity — timeline_attachments (connessione fixcity).
 * Creata separatamente per rispettare la convenzione una-tabella-per-migrazione.
 * Estende XotBaseMigration come richiesto da regole Laraxot.
 * down() rimosso.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = \Modules\Fixcity\Models\TimelineAttachment::class;

    public function up(): void
    {
        if (! $this->hasTable('timeline_attachments')) {
            $this->tableCreate(
                static function (Blueprint $table): void {
                    $table->id();
                    $table->string('path');
                    $table->string('original_name');
                    $table->string('mime_type');
                    $table->string('uploaded_by');
                    $table->timestamp('uploaded_at');
                    $table->timestamps();
                    $table->softDeletes();
                }
            );
        }
    }
};