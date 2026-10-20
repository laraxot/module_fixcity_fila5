<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Database\Seeders\Support\CivicFaqCatalog;
use Modules\Fixcity\Models\Faq;

/**
 * Popola le FAQ pubbliche. È contenuto di prodotto, non dati demo:
 * si può rieseguire, aggiorna la risposta e non duplica la voce.
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $count = 0;

        foreach (CivicFaqCatalog::all() as $locale => $entries) {
            foreach ($entries as $entry) {
                Faq::query()->updateOrCreate(
                    ['id' => $locale.'.'.$entry['slug']],
                    [
                        'locale' => $locale,
                        'question' => $entry['question'],
                        'answer' => $entry['answer'],
                        'sort_order' => $entry['sort_order'],
                        'is_published' => true,
                    ],
                );
                $count++;
            }
        }

        if ($this->command !== null) {
            $this->command->info(sprintf('FaqSeeder: %d domande pubblicate.', $count));
        }
    }
}
