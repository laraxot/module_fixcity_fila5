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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Ticket>
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
            'slug' => fake()->slug(),
            'content' => fake()->paragraph(),
            'owner_id' => User::factory(),
            'responsible_id' => User::factory(),
            'status_id' => fake()->numberBetween(1, 10),
            'code' => fake()->unique()->numerify('TCK-#####'),
            'ticket_prefix' => 'TCK',
            'order' => fake()->numberBetween(0, 100),
            'priority_id' => fake()->numberBetween(1, 5),
            'project_id' => null,
            'estimation' => fake()->optional()->randomFloat(1, 0, 100),
            'epic_id' => null,
            'sprint_id' => null,
            'type_id' => fake()->optional()->numberBetween(1, 5),
            'latitude' => fake()->optional()->latitude,
            'longitude' => fake()->optional()->longitude,
            'created_by' => fake()->optional()->userName(),
            'updated_by' => fake()->optional()->userName(),
        ];
    }

    /**
     * Indica che il ticket è aperto.
     *
     * @return static
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatusEnum::OPEN,
        ]);
    }

    /**
     * Indica che il ticket è urgente.
     *
     * @return static
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriorityEnum::URGENT,
        ]);
    }

    /**
     * Indica che il ticket è risolto.
     *
     * @return static
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatusEnum::RESOLVED,
        ]);
    }
}