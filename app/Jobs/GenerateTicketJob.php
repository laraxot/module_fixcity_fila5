<?php

declare(strict_types=1);

namespace Modules\Fixcity\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;

final class GenerateTicketJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly string $state) {}

    public function handle(): void
    {
        /** @var TicketFactory $factory */
        $factory = Ticket::factory();
        $attributes = match ($this->state) {
            'urgent' => ['priority' => TicketPriorityEnum::URGENT],
            'resolved' => ['status' => TicketStatusEnum::RESOLVED],
            default => ['status' => TicketStatusEnum::OPEN],
        };

        $factory->state($attributes)->create();
    }
}
