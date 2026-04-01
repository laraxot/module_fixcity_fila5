<?php

namespace Modules\Fixcity\Actions;

use Faker\Factory;
use Illuminate\Support\Facades\Bus;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

class GenerateTicketsAction
{
    use QueueableAction;

    protected \Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function execute(int $count): void
    {
        $states = ['open', 'urgent', 'resolved'];

        Bus::batch(
            collect(range(1, $count))
                ->map(fn (): callable => function () use ($states): Ticket {
                    $state = $this->faker->randomElement($states);

                    /** @var \Modules\Fixcity\Database\Factories\TicketFactory $factory */
                    $factory = Ticket::factory();

                    /** @var Ticket $ticket */
                    $ticket = match ($state) {
                        'open' => $factory->open()->create(),  // @phpstan-ignore method.nonObject
                        'urgent' => $factory->urgent()->create(),  // @phpstan-ignore method.nonObject
                        'resolved' => $factory->resolved()->create(),  // @phpstan-ignore method.nonObject
                        default => $factory->create(),  // @phpstan-ignore method.nonObject
                    };

                    return $ticket;
                })
        )->dispatch();
    }
}
