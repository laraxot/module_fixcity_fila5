<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Categorie
        DB::table('categories')->insertOrIgnore([
            [
                'id' => 'strade',
                'name' => 'Strade',
                'description' => 'Problemi relativi a strade e marciapiedi',
                'icon' => 'road',
            ],
            [
                'id' => 'illuminazione',
                'name' => 'Illuminazione',
                'description' => 'Problemi di illuminazione pubblica',
                'icon' => 'lightbulb',
            ],
            [
                'id' => 'arredo_urbano',
                'name' => 'Arredo urbano',
                'description' => 'Problemi di arredo urbano',
                'icon' => 'tree',
            ],
            [
                'id' => 'rifiuti',
                'name' => 'Rifiuti',
                'description' => 'Problemi di rifiuti',
                'icon' => 'trash',
            ],
            [
                'id' => 'verde_pubblico',
                'name' => 'Verde pubblico',
                'description' => 'Problemi di verde pubblico',
                'icon' => 'tree',
            ],
            
            // ... altre categorie
        ]);

        // Reports
        $this->call([
            ReportContentSeeder::class,
            TicketDatabaseSeeder::class,
        ]);
    }
}
