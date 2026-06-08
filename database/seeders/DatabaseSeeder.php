<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Categorie — schema categories: id, title, slug, description, icon, is_active, sort_order
        DB::table('categories')->insertOrIgnore([
            [
                'id' => 1,
                'title' => 'Strade',
                'slug' => 'strade',
                'description' => 'Problemi relativi a strade e marciapiedi',
                'icon' => 'road',
                'is_active' => 1,
                'sort_order' => 10,
            ],
            [
                'id' => 2,
                'title' => 'Illuminazione',
                'slug' => 'illuminazione',
                'description' => 'Problemi di illuminazione pubblica',
                'icon' => 'lightbulb',
                'is_active' => 1,
                'sort_order' => 20,
            ],
            [
                'id' => 3,
                'title' => 'Arredo urbano',
                'slug' => 'arredo-urbano',
                'description' => 'Problemi di arredo urbano',
                'icon' => 'tree',
                'is_active' => 1,
                'sort_order' => 30,
            ],
            [
                'id' => 4,
                'title' => 'Rifiuti',
                'slug' => 'rifiuti',
                'description' => 'Problemi di rifiuti',
                'icon' => 'trash',
                'is_active' => 1,
                'sort_order' => 40,
            ],
            [
                'id' => 5,
                'title' => 'Verde pubblico',
                'slug' => 'verde-pubblico',
                'description' => 'Problemi di verde pubblico',
                'icon' => 'tree',
                'is_active' => 1,
                'sort_order' => 50,
            ],
        ]);

        // Reports / Tickets presentation
        $this->call([
            TicketDatabaseSeeder::class,
        ]);
    }
}
