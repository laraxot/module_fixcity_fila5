<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Actions\GenerateTicketsJsonAction;

/**
 * GeoJSON export seeder — regenera public_html/data/tickets.json dopo il seed.
 *
 * La mappa FO/BO legge public_html/data/tickets.json (FeatureCollection GeoJSON).
 * Questo seeder garantisce che il JSON sia sempre aggiornato dopo il seed.
 *
 * Usage:
 *   php artisan db:seed --class="Modules\Fixcity\Database\Seeders\InvestoGeoJsonSeeder"
 */
class InvestoGeoJsonSeeder extends Seeder
{
    public function run(): void
    {
        $path = app(GenerateTicketsJsonAction::class)->execute();

        if ($this->command !== null) {
            $this->command->info('InvestoGeoJsonSeeder: GeoJSON aggiornato → '.$path);
        }
    }
}
