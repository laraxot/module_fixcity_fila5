<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Modules\Fixcity\Models\TicketSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

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
