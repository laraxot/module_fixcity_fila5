<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestratore seed Fixcity — ordine: categorie → profili → ticket demo FO.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DemoUsersSeeder::class,
            CategorySeeder::class,
            ProfileSeeder::class,
            TicketDatabaseSeeder::class,
            DemoCitiesSeeder::class,
            TicketCommentSeeder::class,
            TicketActivitySeeder::class,
            TicketHourSeeder::class,
            TicketRelationSeeder::class,
            TicketSubscriberSeeder::class,
            GeoJsonUpdateSeeder::class,
        ]);
    }
}
