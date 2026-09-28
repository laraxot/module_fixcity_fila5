<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

final class NotifyTicketSubscribersOfStatusChangeAction
{
    use QueueableAction;

    public function execute(TicketActivity $activity): void
    {
        if ($activity->event_type !== TicketActivityEventTypeEnum::StatusChange->value
            || $activity->visibility !== TicketActivityVisibilityEnum::Public->value) {
            return;
        }

        $targetStatus = $activity->payload['to'] ?? null;
        if (! is_string($targetStatus)) {
            return;
        }

        $status = TicketStatusEnum::tryFrom($targetStatus);
        if (! $status instanceof TicketStatusEnum || ! in_array($status, TicketStatusEnum::canViewByAll(), true)) {
            return;
        }

        $ticket = $activity->ticket()->first();
        if ($ticket === null) {
            return;
        }

        $ticketCode = $ticket->code;
        if (! is_string($ticketCode) || trim($ticketCode) === '') {
            Log::warning('Fixcity status notification skipped: ticket has no public tracking code.', [
                'ticket_id' => $ticket->id,
            ]);

            return;
        }

        $recipients = $ticket->ticketSubscribers()->get();
        $owner = $ticket->owner;
        if ($owner instanceof UserContract) {
            $recipients->push($owner);
        }

        $sentTo = [];
        foreach ($recipients as $recipient) {
            if (! $recipient instanceof UserContract || ! method_exists($recipient, 'notify')) {
                continue;
            }

            $recipientId = $recipient->getAuthIdentifier();
            if (! is_string($recipientId) && ! is_int($recipientId)) {
                continue;
            }

            $recipientKey = (string) $recipientId;
            if (isset($sentTo[$recipientKey])) {
                continue;
            }
            $sentTo[$recipientKey] = true;

            try {
                Notification::send(
                    $recipient,
                    new TicketStatusChangedNotification($ticketCode, $status),
                );
            } catch (Throwable $exception) {
                Log::error('Fixcity status notification delivery failed.', [
                    'ticket_id' => $ticket->id,
                    'recipient_id' => $recipientKey,
                    'exception' => $exception::class,
                ]);
            }
        }
    }
}
