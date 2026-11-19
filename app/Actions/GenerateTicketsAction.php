<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Facades\Bus;
use Spatie\QueueableAction\ActionJob;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

class GenerateTicketsAction
{
    use QueueableAction;

    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function execute(int $count): void
    {
        if ($count <= 0) {
            return;
        }

        $states = ['open', 'urgent', 'resolved'];

        $jobs = collect(range(1, $count))
            ->map(fn (int $i): ActionJob => new ActionJob(
                app(GenerateTicketAction::class),
                [SafeStringCastAction::cast($this->faker->randomElement($states))],
            ))
            ->all();

        Bus::batch($jobs)->dispatch();
    }
}
