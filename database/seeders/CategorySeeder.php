<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Models\Category;

/**
 * Class CategorySeeder.
 * 
 * Seeder per popolare la tabella categories con dati di esempio
 * per il sistema di gestione segnalazioni cittadini.
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 'strade',
                'name' => 'Strade e Marciapiedi',
                'description' => 'Problemi relativi a strade, marciapiedi, buche, asfalto danneggiato',
                'icon' => 'road',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 'illuminazione',
                'name' => 'Illuminazione Pubblica',
                'description' => 'Lampioni non funzionanti, illuminazione insufficiente, interruzioni',
                'icon' => 'lightbulb',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 'rifiuti',
                'name' => 'Rifiuti e Pulizia',
                'description' => 'Cassonetti pieni, rifiuti abbandonati, pulizia strade',
                'icon' => 'trash',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'id' => 'verde',
                'name' => 'Verde Pubblico',
                'description' => 'Aiuole, alberi, parchi, manutenzione aree verdi',
                'icon' => 'tree',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'id' => 'segnaletica',
                'name' => 'Segnaletica',
                'description' => 'Cartelli stradali, segnali, indicazioni, semafori',
                'icon' => 'sign',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'id' => 'arredo',
                'name' => 'Arredo Urbano',
                'description' => 'Panchine, cestini, fontane, monumenti, elementi decorativi',
                'icon' => 'bench',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'id' => 'mobilita',
                'name' => 'Mobilità',
                'description' => 'Piste ciclabili, parcheggi, trasporti pubblici, accessibilità',
                'icon' => 'bicycle',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'id' => 'sicurezza',
                'name' => 'Sicurezza',
                'description' => 'Videocamere, illuminazione di sicurezza, aree a rischio',
                'icon' => 'shield',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'id' => 'acqua',
                'name' => 'Acqua e Fognature',
                'description' => 'Perdite d\'acqua, allagamenti, fognature, depurazione',
                'icon' => 'droplet',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'id' => 'energia',
                'name' => 'Energia',
                'description' => 'Cabine elettriche, cavi aerei, interruzioni energia',
                'icon' => 'bolt',
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['id' => $categoryData['id']],
                $categoryData
            );
        }

        // Aggiungi alcune sottocategorie per strade
        $subCategories = [
            [
                'id' => 'strade-buche',
                'name' => 'Buche e Dossi',
                'description' => 'Buche nell\'asfalto, dossi artificiali danneggiati',
                'icon' => 'pothole',
                'parent_id' => 'strade',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 'strade-marciapiedi',
                'name' => 'Marciapiedi',
                'description' => 'Lastricato danneggiato, gradini pericolosi, accessibilità',
                'icon' => 'walking',
                'parent_id' => 'strade',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 'strade-asfalto',
                'name' => 'Asfalto e Pavimentazione',
                'description' => 'Asfalto consumato, pavimentazione danneggiata',
                'icon' => 'road',
                'parent_id' => 'strade',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($subCategories as $subCategoryData) {
            Category::updateOrCreate(
                ['id' => $subCategoryData['id']],
                $subCategoryData
            );
        }
    }
}
