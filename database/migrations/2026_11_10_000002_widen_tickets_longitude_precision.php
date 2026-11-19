<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * `tickets.longitude` era `decimal(20,18)`: 2 cifre intere, quindi su MySQL/MariaDB ogni longitudine oltre 99,99 gradi
 * (in valore assoluto) falliva con "Out of range value for column 'longitude'". La longitudine vale [-180,180]: servono
 * 3 cifre intere, cioe' `decimal(21,18)`. La latitudine resta `decimal(20,18)` ([-90,90]).
 *
 * Le migrazioni di creazione ora creano gia' `decimal(21,18)`; questa allinea i database esistenti. SQLite non applica
 * precisione ai decimali: nessuna modifica. Idempotente (allargare una colonna gia' larga non cambia nulla). Forward-only.
 */
return new class extends XotBaseMigration
{
    /** @var class-string<Ticket> */
    protected ?string $model_class = Ticket::class;

    public function up(): void
    {
        $schema = $this->getConn();
        $table = $this->getTable();
        if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'longitude')) {
            return;
        }
        if ($schema->getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        $schema->table($table, static function (Blueprint $blueprint): void {
            $blueprint->decimal('longitude', 21, 18)->nullable()->change();
        });
    }

    /**
     * Forward-only: il down() ereditato cancellerebbe l'intera tabella `tickets`.
     */
    public function down(): void {}
};
