<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Bus;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

final class GenerateTicketAction
{
    use QueueableAction;

    public function execute(string $state): void
    {
        /** @var TicketFactory $factory */
        $factory = Ticket::factory();
        $attributes = match ($state) {
            'urgent' => ['priority' => TicketPriorityEnum::URGENT],
            'resolved' => ['status' => TicketStatusEnum::RESOLVED],
            default => ['status' => TicketStatusEnum::OPEN],
        };

        $factory->state($attributes)->create();
    }
}
