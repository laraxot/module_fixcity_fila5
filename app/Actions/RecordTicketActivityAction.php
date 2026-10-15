<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Spatie\QueueableAction\QueueableAction;

final class RecordTicketActivityAction
{
    use QueueableAction;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function execute(
        Ticket $ticket,
        TicketActivityEventTypeEnum $eventType,
        ?TicketStatusEnum $from,
        TicketStatusEnum $to,
        ?string $reason,
        TicketActivityVisibilityEnum $visibility,
        ?int $userId,
        array $payload = [],
    ): TicketActivity {
        return TicketActivity::query()->create([
            'ticket_id' => $ticket->getKey(),
            'old_status_id' => null,
            'new_status_id' => null,
            'user_id' => $userId,
            'event_type' => $eventType->value,
            'payload' => $payload + [
                'v' => 1,
                'from' => $from?->value,
                'to' => $to->value,
            ],
            'visibility' => $visibility->value,
            'reason' => $reason,
        ]);
    }
}
