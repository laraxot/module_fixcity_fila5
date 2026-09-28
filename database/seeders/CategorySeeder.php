<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Database\Seeders\Support\DemoCategoryCatalog;
use Modules\Fixcity\Models\Category;

/**
 * Class CategorySeeder.
 *
 * Seeder per popolare la tabella categories con dati di esempio
 * per il sistema di gestione segnalazioni cittadini.
 *
 * Il catalogo completo vive in Support\DemoCategoryCatalog: 12 categorie di primo
 * livello e 10 di secondo livello, ognuna con la `type` (TicketTypeEnum) con cui i
 * ticket corrispondenti vengono etichettati e con l'ufficio comunale competente.
 * Lo schema `tickets` non ha una colonna `category_id`, quindi questo e' il modo
 * canonico in cui una categoria arriva a una segnalazione.
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = DemoCategoryCatalog::all();

        foreach ($categories as $categoryData) {
            Category::query()->updateOrCreate(
                ['id' => $categoryData['id']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'icon' => $categoryData['icon'],
                    'parent_id' => $categoryData['parent_id'],
                    'is_active' => true,
                    'sort_order' => $categoryData['sort_order'],
                ]
            );
        }

        $roots = count(array_filter($categories, static fn (array $category): bool => $category['parent_id'] === null));
        $children = count($categories) - $roots;

        if ($this->command !== null) {
            $this->command->info(sprintf(
                'CategorySeeder: %d categorie (%d di primo livello, %d di secondo livello).',
                count($categories),
                $roots,
                $children,
            ));
        }
    }
}
