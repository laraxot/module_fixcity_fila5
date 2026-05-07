<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Facades\Bus;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

class GenerateTicketsAction
{
    use QueueableAction;

    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @return void
     */
    public function execute(int $count): void
    {
        $states = ['open', 'urgent', 'resolved'];

        $closures = collect(range(1, $count))
            ->map(fn (int $i): callable => function () use ($states): void {
                $state = $this->faker->randomElement($states);

                /** @var TicketFactory $factory */
                $factory = Ticket::factory();
                $factory->state(['status' => $state])->create();
            })
            ->all();

        Bus::batch($closures)->dispatch();
    }
}
