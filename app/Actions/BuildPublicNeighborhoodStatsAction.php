<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Public-only aggregate by suburb or city; no ticket details or personal data are returned.
 *
 * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
 */
final class BuildPublicNeighborhoodStatsAction
{
    use QueueableAction;

    /**
     * @return array{period_days: int, total_reports: int, without_area: int, neighborhoods: list<array{name: string, reports: int, active: int, resolved: int}>}
     *
     * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
     */
    public function execute(int $days = 30): array
    {
        $days = in_array($days, [7, 30, 365], true) ? $days : 30;
        $total = 0;
        $withoutArea = 0;
        $groups = [];

        foreach (app(BuildPublicTicketsQueryAction::class)->execute(false)
            ->where('created_at', '>=', now()->subDays($days))
            ->select(['id', 'status', 'created_at', 'location'])
            ->cursor() as $ticket) {
            if (! $ticket instanceof Ticket) {
                continue;
            }

            $total++;
            $location = is_array($ticket->location) ? $ticket->location : [];
            $rawArea = $location['suburb'] ?? $location['city'] ?? null;
            if (! is_string($rawArea) || trim($rawArea) === '') {
                $withoutArea++;

                continue;
            }

            $area = mb_substr(trim($rawArea), 0, 100);
            $key = mb_strtolower($area);
            if (! isset($groups[$key])) {
                $groups[$key] = ['name' => $area, 'reports' => 0, 'active' => 0, 'resolved' => 0];
            }

            $groups[$key]['reports']++;
            $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue());
            $resolved = in_array($status, [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED], true);
            $groups[$key][$resolved ? 'resolved' : 'active']++;
        }

        $neighborhoods = array_values($groups);
        usort($neighborhoods, static fn (array $a, array $b): int => ($b['reports'] <=> $a['reports'])
            ?: strcmp($a['name'], $b['name']));

        return [
            'period_days' => $days,
            'total_reports' => $total,
            'without_area' => $withoutArea,
            'neighborhoods' => $neighborhoods,
        ];
    }
}
