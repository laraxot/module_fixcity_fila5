<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\TicketActivity;

/**
 * @extends Factory<TicketActivity>
 */
class TicketActivityFactory extends Factory
{
    protected $model = TicketActivity::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
