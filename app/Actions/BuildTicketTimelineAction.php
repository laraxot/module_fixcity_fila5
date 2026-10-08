<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Spatie\QueueableAction\QueueableAction;

final class BuildTicketTimelineAction
{
    use QueueableAction;

    /**
     * @return list<array{occurred_at: string, from_label: string|null, to_label: string, reason: string|null}>
     */
    public function execute(Ticket $ticket): array
    {
        $activities = $ticket->activities()
            ->where('event_type', TicketActivityEventTypeEnum::StatusChange->value)
            ->where('visibility', TicketActivityVisibilityEnum::Public->value)
            ->orderBy('created_at')
            ->get();

        $timeline = [];

        foreach ($activities as $activity) {
            if (! $activity instanceof TicketActivity) {
                continue;
            }

            $payload = $activity->payload;
            $toValue = is_array($payload) && is_string($payload['to'] ?? null)
                ? $payload['to']
                : null;
            $to = $toValue !== null ? TicketStatusEnum::tryFrom($toValue) : null;

            if (! $to instanceof TicketStatusEnum) {
                continue;
            }

            $fromValue = is_array($payload) && is_string($payload['from'] ?? null)
                ? $payload['from']
                : null;
            $from = $fromValue !== null ? TicketStatusEnum::tryFrom($fromValue) : null;

            $timeline[] = [
                'occurred_at' => $activity->created_at?->translatedFormat('d F Y H:i') ?? '',
                'from_label' => $from?->getLabel(),
                'to_label' => $to->getLabel(),
                'reason' => $activity->reason,
            ];
        }

        return $timeline;
    }
}
