<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
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
     * Build the chronological timeline allowed for the viewer.
     *
     * @return list<array{occurred_at: string, occurred_date: string, occurred_on: string, from_label: string|null, to_label: string|null, reason: string|null}>
     */
    public function execute(Ticket $ticket, ?Authenticatable $viewer = null): array
    {
        $isOwner = $viewer !== null
            && (string) $ticket->owner_id === (string) $viewer->getAuthIdentifier();
        $isOperator = $viewer !== null
            && method_exists($viewer, 'can')
            && (
                (bool) $viewer->can('changeStatus', $ticket)
                || (bool) $viewer->can('assign', $ticket)
                || (bool) $viewer->can('viewAny', Ticket::class)
            );

        $activities = $ticket->activities()
            ->where(function ($query) use ($isOwner, $isOperator): void {
                $query->where('visibility', TicketActivityVisibilityEnum::Public->value);

                if ($isOwner || $isOperator) {
                    $query->orWhere('visibility', TicketActivityVisibilityEnum::AuthorOnly->value);
                }

                if ($isOperator) {
                    $query->orWhere('visibility', TicketActivityVisibilityEnum::Internal->value);
                }
            })
            ->orderBy('created_at')
            ->get();

        $timeline = [];

        foreach ($activities as $activity) {
            if (! $activity instanceof TicketActivity) {
                continue;
            }

            $payload = $activity->payload ?? [];
            $toValue = is_array($payload) && is_string($payload['to'] ?? null)
                ? $payload['to']
                : null;
            $to = $toValue !== null ? TicketStatusEnum::tryFrom($toValue) : null;

            $fromValue = is_array($payload) && is_string($payload['from'] ?? null)
                ? $payload['from']
                : null;
            $from = $fromValue !== null ? TicketStatusEnum::tryFrom($fromValue) : null;

            $timeline[] = [
                'occurred_at' => $activity->created_at?->translatedFormat('d F Y H:i') ?? '',
                'occurred_date' => $activity->created_at?->translatedFormat('j F Y') ?? '',
                'occurred_on' => $activity->created_at?->toDateString() ?? '',
                'from_label' => $from?->getLabel(),
                'to_label' => $to?->getLabel() ?? $this->eventLabel($activity),
                'reason' => $this->safeReason($activity),
            ];
        }

        return $timeline;
    }

    private function eventLabel(TicketActivity $activity): string
    {
        $key = match ($activity->event_type) {
            TicketActivityEventTypeEnum::Assignment->value => 'fixcity::ticket_detail.timeline.assignment',
            TicketActivityEventTypeEnum::Approved->value => 'fixcity::ticket_detail.timeline.approved',
            TicketActivityEventTypeEnum::Rejected->value => 'fixcity::ticket_detail.timeline.rejected',
            TicketActivityEventTypeEnum::MailSent->value => 'fixcity::ticket_detail.timeline.mail_sent',
            TicketActivityEventTypeEnum::MailFailed->value => 'fixcity::ticket_detail.timeline.mail_failed',
            TicketActivityEventTypeEnum::MailReceived->value => 'fixcity::ticket_detail.timeline.mail_received',
            TicketActivityEventTypeEnum::Followup->value => 'fixcity::ticket_detail.timeline.followup',
            TicketActivityEventTypeEnum::MediaAdded->value => 'fixcity::ticket_detail.timeline.media_added',
            TicketActivityEventTypeEnum::Closed->value => 'fixcity::ticket_detail.timeline.closed',
            default => 'fixcity::ticket_detail.timeline.update',
        };

        return (string) __($key);
    }

    private function safeReason(TicketActivity $activity): ?string
    {
        $payload = $activity->payload ?? [];

        if (in_array($activity->event_type, [
            TicketActivityEventTypeEnum::MailSent->value,
            TicketActivityEventTypeEnum::MailFailed->value,
            TicketActivityEventTypeEnum::Followup->value,
        ], true)) {
            $label = is_array($payload) && is_string($payload['recipient_label'] ?? null)
                ? trim($payload['recipient_label'])
                : '';

            return $label !== ''
                ? (string) __('fixcity::ticket_detail.timeline.recipient', ['recipient' => $label])
                : null;
        }

        if ($activity->event_type === TicketActivityEventTypeEnum::MediaAdded->value) {
            $fileName = is_array($payload) && is_string($payload['file_name'] ?? null)
                ? trim($payload['file_name'])
                : '';

            return $fileName !== ''
                ? (string) __('fixcity::ticket_detail.timeline.attachment', ['file' => $fileName])
                : null;
        }

        return $activity->reason;
    }
}
