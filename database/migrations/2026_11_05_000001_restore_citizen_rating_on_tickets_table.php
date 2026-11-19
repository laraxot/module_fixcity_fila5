<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Ripristina `citizen_rating` e `citizen_rated_at` su `tickets`.
 *
 * La migrazione originale (2026_05_29_120000_add_citizen_rating_to_tickets_table) è stata cancellata nel commit
 * 2744926b59: sui database creati dopo, valutazione del cittadino, widget, ordinamento ed export andavano in
 * "Unknown column". Idempotente: sui database che hanno già le colonne non fa nulla.
 */
return new class extends XotBaseMigration
{
    /** @var class-string<Ticket> */
    protected ?string $model_class = Ticket::class;

    public function up(): void
    {
        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('citizen_rating')) {
                $table->unsignedTinyInteger('citizen_rating')->nullable();
            }
            if (! $this->hasColumn('citizen_rated_at')) {
                $table->timestamp('citizen_rated_at')->nullable();
            }
        });
    }

    /**
     * Forward-only: il down() ereditato cancellerebbe l'intera tabella `tickets`.
     */
    public function down(): void {}
};
