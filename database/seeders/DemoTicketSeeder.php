<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;

class DemoTicketSeeder extends Seeder
{
    /**
     * Seed demo tickets for presentation.
     */
    public function run(): void
    {
        TicketFactory::new()
            ->count(15)
            ->create();
    }
}
