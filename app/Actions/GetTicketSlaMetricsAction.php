<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * SLA metrics use the recorded resolution event, not subsequent ticket edits.
 *
 * Tickets imported without an audit event still use updated_at as a documented
 * legacy fallback. Chunks avoid loading the whole tenant's ticket history.
 *
 * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
 */
final class GetTicketSlaMetricsAction
{
    use QueueableAction;

    /**
     * @return array{resolved_count: int, avg_resolution_hours: float|null, resolved_last_30_days: int}
     *
     * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
     */
    public function execute(): array
    {
        $statuses = [TicketStatusEnum::RESOLVED->value, TicketStatusEnum::CLOSED->value];
        $cutoff = Carbon::now()->subDays(30);

        $resolvedCount = 0;
        $totalSeconds = 0.0;
        $timedCount = 0;
        $resolvedLast30 = 0;

        Ticket::query()
            ->whereIn('status', $statuses)
            ->select(['id', 'created_at', 'updated_at'])
            ->chunkById(500, function (Collection $tickets) use (
                &$resolvedCount, &$totalSeconds, &$timedCount, &$resolvedLast30, $cutoff,
            ): void {
                $resolutionDates = [];

                $activities = TicketActivity::query()
                    ->whereIn('ticket_id', $tickets->modelKeys())
                    ->where('event_type', TicketActivityEventTypeEnum::StatusChange->value)
                    ->get(['ticket_id', 'created_at', 'payload']);

                foreach ($activities as $activity) {
                    if (! $activity instanceof TicketActivity
                        || (($activity->payload ?? [])['to'] ?? null) !== TicketStatusEnum::RESOLVED->value
                        || ! $activity->created_at instanceof Carbon) {
                        continue;
                    }

                    $id = SafeStringCastAction::cast($activity->ticket_id);
                    if (! isset($resolutionDates[$id]) || $activity->created_at->greaterThan($resolutionDates[$id])) {
                        $resolutionDates[$id] = $activity->created_at;
                    }
                }

                foreach ($tickets as $ticket) {
                    if (! $ticket instanceof Ticket) {
                        continue;
                    }

                    $resolvedCount++;
                    $resolvedAt = $resolutionDates[SafeStringCastAction::cast($ticket->getKey())] ?? $ticket->updated_at;
                    if (! $resolvedAt instanceof Carbon) {
                        continue;
                    }

                    if ($resolvedAt->greaterThanOrEqualTo($cutoff)) {
                        $resolvedLast30++;
                    }

                    if ($ticket->created_at instanceof Carbon) {
                        $totalSeconds += max(0.0, (float) $ticket->created_at->diffInSeconds($resolvedAt));
                        $timedCount++;
                    }
                }
            });

        return [
            'resolved_count' => $resolvedCount,
            'avg_resolution_hours' => $timedCount > 0 ? round($totalSeconds / $timedCount / 3600, 1) : null,
            'resolved_last_30_days' => $resolvedLast30,
        ];
    }
}
