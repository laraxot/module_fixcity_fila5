<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\TicketRelation;

/**
 * @extends Factory<TicketRelation>
 */
class TicketRelationFactory extends Factory
{
    protected $model = TicketRelation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
