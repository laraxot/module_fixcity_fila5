<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Entry point modulo Fixcity — utenti demo FO/BO poi categorie/profili/ticket.
 *
 * Evita UserDatabaseSeeder completo (DeviceProfile/OAuth) che blocca la demo.
 */
class FixcityDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing', 'demo'])) {
            if ($this->command !== null) {
                $this->command->warn('Fixcity demo data skipped outside local, testing and demo environments.');
            }

            return;
        }

        $this->call([
            DemoUsersSeeder::class,
            DemoOperatorPanelAccessSeeder::class,
            DatabaseSeeder::class,
        ]);
    }
}
