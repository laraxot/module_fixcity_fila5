<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\TicketComment;
use Modules\Fixcity\Models\TicketRelation;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Indici per le query di mappa, elenco, KPI, area personale e timeline (DB-03) e per il codice di tracciamento (DB-04).
 *
 * Solo aggiunte idempotenti (XotBaseMigration::ensureIndexes): un indice gia' presente con lo stesso nome o sulle stesse colonne non
 * viene duplicato, una tabella o colonna mancante viene saltata. Forward-only: togliere indici in produzione e' una decisione dell'utente.
 *
 * `tickets.code`: unique solo se i dati lo permettono. `code` e' nullable (piu' NULL non violano l'unicita'), ma un database esistente
 * puo' avere codici duplicati o piu' righe con stringa vuota: in quel caso un unique farebbe fallire la migrazione. Allora si crea un
 * indice semplice e si registra un warning; i duplicati vanno sistemati a mano, poi l'indice si promuove a unique con una nuova migrazione.
 */
return new class extends XotBaseMigration
{
    /** @var class-string<Ticket> */
    protected ?string $model_class = Ticket::class;

    public function up(): void
    {
        $this->ensureIndexes($this->indexes());
        $this->ensureCodeIndex();
    }

    /**
     * Tabella => nome indice => colonne in ordine (prima l'uguaglianza, poi l'intervallo o l'ordinamento).
     *
     * @return array<string, array<string, list<string>>>
     */
    private function indexes(): array
    {
        return [
            $this->getTable() => [
                // elenco pubblico, KPI, SLA: filtro per stato, ordine per data
                'tickets_status_created_at_index' => ['status', 'created_at'],
                // mappa GeoJSON ed elenco API: filtro per tipo (e stato)
                'tickets_type_status_index' => ['type', 'status'],
                // area personale: le mie segnalazioni, piu' recenti prima
                'tickets_owner_id_created_at_index' => ['owner_id', 'created_at'],
                // filtro "assegnate a" nel back office
                'tickets_responsible_id_index' => ['responsible_id'],
                // ordinamento per data senza altri filtri
                'tickets_created_at_index' => ['created_at'],
            ],
            // dettaglio e timeline: tutte le attivita' di un ticket in ordine
            (new TicketActivity)->getTable() => [
                'ticket_activities_ticket_id_created_at_index' => ['ticket_id', 'created_at'],
            ],
            (new TicketComment)->getTable() => [
                'ticket_comments_ticket_id_index' => ['ticket_id'],
            ],
            (new TicketRelation)->getTable() => [
                'ticket_relations_ticket_id_index' => ['ticket_id'],
            ],
        ];
    }

    private function ensureCodeIndex(): void
    {
        $schema = $this->getConn();
        $table = $this->getTable();
        if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'code') || $schema->hasIndex($table, ['code'])) {
            return;
        }

        // anche le righe cancellate (soft delete): l'indice le comprende
        $hasDuplicates = $schema->getConnection()->table($table)
            ->select('code')
            ->whereNotNull('code')
            ->groupBy('code')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        $schema->table($table, static function (Blueprint $blueprint) use ($hasDuplicates): void {
            if ($hasDuplicates) {
                $blueprint->index(['code'], 'tickets_code_index');

                return;
            }

            $blueprint->unique(['code'], 'tickets_code_unique');
        });

        if ($hasDuplicates) {
            Log::warning('Fixcity migration: tickets.code has duplicate values, created a plain index instead of a unique one.', [
                'table' => $table,
            ]);
        }
    }

    /**
     * Forward-only: il down() ereditato cancellerebbe l'intera tabella `tickets`.
     */
    public function down(): void {}
};
