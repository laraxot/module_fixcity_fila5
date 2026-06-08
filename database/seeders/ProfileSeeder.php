<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * ProfileSeeder — stub per parità modulo (N modelli = N seeder).
 * Popola profili di test solo in ambiente non-production.
 */
class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Stub: aggiungi dati di esempio quando necessario.
        // MAI usare RefreshDatabase / migrate:fresh — i dati sono sacri.
    }
}
