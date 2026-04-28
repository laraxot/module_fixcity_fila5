<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\TicketSubscriber;

class TicketSubscriberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TicketSubscriber::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
