<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Modules\Fixcity\Models\TicketRelation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketRelationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TicketRelation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
