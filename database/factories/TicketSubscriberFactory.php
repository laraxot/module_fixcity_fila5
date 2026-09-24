<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\TicketSubscriber;

/**
 * @extends Factory<TicketSubscriber>
 */
class TicketSubscriberFactory extends Factory
{
    protected $model = TicketSubscriber::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
