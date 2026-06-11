<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * TicketSeeder — parità modulo: delega a TicketDatabaseSeeder (demo FO mappa/elenco).
 */
class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TicketDatabaseSeeder::class);
    }
}
