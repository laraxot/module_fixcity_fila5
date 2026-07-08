<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\TicketHour;

/**
 * @extends Factory<TicketHour>
 */
class TicketHourFactory extends Factory
{
    protected $model = TicketHour::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
