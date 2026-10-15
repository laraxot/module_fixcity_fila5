<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Notifications\TicketAssignedNotification;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

final class NotifyTicketAssigneeOfAssignmentAction
{
    use QueueableAction;

    public function execute(TicketActivity $activity): void
    {
        if ($activity->event_type !== TicketActivityEventTypeEnum::Assignment->value
            || $activity->visibility !== TicketActivityVisibilityEnum::Internal->value) {
            return;
        }

        $responsibleId = $activity->payload['responsible_id'] ?? null;
        if (! is_string($responsibleId) || $responsibleId === '') {
            return;
        }

        $ticket = $activity->ticket()->first();
        if ($ticket === null || $ticket->responsible_id !== $responsibleId) {
            return;
        }

        $assignee = $ticket->assignee;
        if (! $assignee instanceof UserContract || ! method_exists($assignee, 'notify')) {
            return;
        }

        try {
            Notification::send($assignee, new TicketAssignedNotification($ticket->id));
        } catch (Throwable $exception) {
            Log::error('Fixcity assignment notification delivery failed.', [
                'ticket_id' => $ticket->id,
                'recipient_id' => $responsibleId,
                'exception' => $exception::class,
            ]);
        }
    }
}
