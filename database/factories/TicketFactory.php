<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'owner_id' => User::factory(),
            'responsible_id' => User::factory(),
            'status' => TicketStatusEnum::PENDING,
            'code' => fake()->unique()->numerify('TCK-#####'),
            'ticket_prefix' => 'TCK',
            'order' => fake()->numberBetween(0, 100),
            'priority' => TicketPriorityEnum::MEDIUM,
            'project_id' => null,
            'estimation' => fake()->optional()->randomFloat(1, 0, 100),
            'epic_id' => null,
            'sprint_id' => null,
            'type' => TicketTypeEnum::REPORT,
            // MySQL decimal(20,18) accepts latitude [-90,90]; longitude needs
            // decimal(21,18) so the full geographic range [-180,180] remains valid.
            'latitude' => fake()->optional()->randomFloat(6, -90, 90),
            'longitude' => fake()->optional()->randomFloat(6, -180, 180),
            'created_by' => fake()->optional()->userName(),
            'updated_by' => fake()->optional()->userName(),
        ];
    }

    /**
     * Indica che il ticket è aperto.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TicketStatusEnum::OPEN,
        ]);
    }

    /**
     * Indica che il ticket è urgente.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes): array => [
            'priority' => TicketPriorityEnum::URGENT,
        ]);
    }

    /**
     * Indica che il ticket è risolto.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TicketStatusEnum::RESOLVED,
        ]);
    }
}
