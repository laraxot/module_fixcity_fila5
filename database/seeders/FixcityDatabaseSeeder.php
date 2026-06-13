<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

/**
 * Entry point modulo Fixcity — garantisce utenti demo prima dei ticket.
 */
class FixcityDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserDatabaseSeeder::class,
            DatabaseSeeder::class,
        ]);
    }
}
