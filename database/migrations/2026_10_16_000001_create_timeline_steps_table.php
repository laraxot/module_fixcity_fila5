<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\TimelineStep;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Owner Fixcity — timeline_steps (connessione fixcity).
 * Estende XotBaseMigration come richiesto da regole Laraxot.
 * down() rimosso.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = TimelineStep::class;

    public function up(): void
    {
        if (! $this->hasTable('timeline_steps')) {
            $this->tableCreate(
                static function (Blueprint $table): void {
                    $table->id();
                    $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
                    $table->string('type');
                    $table->string('description');
                    $table->string('responsible')->nullable();
                    $table->timestamp('performed_at')->nullable();
                    $table->text('action_note')->nullable();
                    $table->boolean('requires_followup')->default(false);
                    $table->timestamp('followup_at')->nullable();
                    $table->foreignId('attachment_id')->nullable();
                    $table->foreign('attachment_id')
                        ->references('id')
                        ->on('timeline_attachments')
                        ->nullOnDelete();
                    $table->boolean('private_note')->default(false);
                    $table->json('metadata')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                }
            );

            return;
        }

        $this->tableUpdate(
            function (Blueprint $table): void {
                if ($this->hasColumn('attachment_id')) {
                    $table->foreignId('attachment_id')->nullable()->change();
                }

                if (! $this->getConn()->hasForeignKey($this->getTable(), 'timeline_steps_attachment_id_foreign')) {
                    $table->foreign('attachment_id')
                        ->references('id')
                        ->on('timeline_attachments')
                        ->nullOnDelete();
                }
            }
        );
    }
};
