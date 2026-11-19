<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketHour;
use Modules\User\Models\User;

/**
 * @extends Factory<TicketHour>
 */
class TicketHourFactory extends Factory
{
    protected $model = TicketHour::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'value' => fake()->randomFloat(2, 0.25, 8),
            'comment' => fake()->sentence(),
        ];
    }
}
