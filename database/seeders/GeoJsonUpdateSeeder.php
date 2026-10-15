<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Modules\Fixcity\Actions\GenerateTicketsJsonAction;

/**
 * GeoJSON update seeder — rigenera public_html/data/tickets.json dopo ogni seed.
 *
 * La mappa FO/BO legge public_html/data/tickets.json (FeatureCollection GeoJSON).
 * Questo seeder garantisce che il JSON sia sempre aggiornato dopo il seed.
 */
class GeoJsonUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $path = app(GenerateTicketsJsonAction::class)->execute();

        if ($this->command !== null) {
            $this->command->info('GeoJsonUpdateSeeder: GeoJSON aggiornato → '.$path);
        }
    }
}
