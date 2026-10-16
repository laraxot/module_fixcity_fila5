<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timeline_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('type'); // created, moderated_approved, moderated_rejected, assigned, email_sent, email_reminder, response_received, response_negative, response_delegated, response_soon, photo_added, closed_resolved, closed_rejected, closed_delegated, closed_not_resolved
            $table->string('description'); // breve descrizione per il cittadino
            $table->string('responsible')->nullable(); // cittadino, moderatore X, ULSS, Comune, Vigili, Supporto
            $table->timestamp('performed_at')->nullable();
            $table->text('action_note')->nullable(); // nota del passaggio (visibile in base al ruolo)
            $table->boolean('requires_followup')->default(false); // se serve un follow-up (es. sollecito)
            $table->timestamp('followup_at')->nullable(); // quando eseguire il follow-up (es. sollecito dopo X giorni)
            $table->foreignId('attachment_id')->nullable()->constrained('timeline_attachments')->nullOnDelete();
            $table->boolean('private_note')->default(false); // true = visibile solo a moderatore/dipartimento/admin
            $table->json('metadata')->nullable(); // dati extra (es. soggetto email, destinatario, tipo esito)
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('timeline_attachments', function (Blueprint $table): void {
            $table->id();
            $table->string('path'); // storage/app/public/timeline_attachments/...
            $table->string('original_name'); // nome file originale
            $table->string('mime_type');
            $table->string('uploaded_by'); // nome utente
            $table->timestamp('uploaded_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_steps');
        Schema::dropIfExists('timeline_attachments');
    }
};
