<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Suggest existing PUBLIC issues near a point, without leaking private reports.
 *
 * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
 */
final class FindNearbyPublicTicketsAction
{
    use QueueableAction;

    /**
     * @return Collection<int, Ticket>
     *
     * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
     */
    public function execute(string $type, float $latitude, float $longitude, int $radiusMeters = 50): Collection
    {
        if (TicketTypeEnum::tryFrom($type) === null
            || ! is_finite($latitude) || ! is_finite($longitude)
            || abs($latitude) > 90 || abs($longitude) > 180
            || $radiusMeters < 1 || $radiusMeters > 500) {
            return new Collection;
        }

        $latitudeDelta = $radiusMeters / 111320;
        $longitudeDelta = $radiusMeters / (111320 * max(0.01, cos(deg2rad($latitude))));

        $candidates = app(BuildPublicTicketsQueryAction::class)->execute(false)
            ->where('type', $type)
            ->whereNotIn('status', [TicketStatusEnum::RESOLVED->value, TicketStatusEnum::CLOSED->value])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$latitude - $latitudeDelta, $latitude + $latitudeDelta])
            ->whereBetween('longitude', [$longitude - $longitudeDelta, $longitude + $longitudeDelta])
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByDesc('created_at')
            ->limit(250)
            ->get();

        return $candidates
            ->filter(static fn (Ticket $ticket): bool => is_numeric($ticket->latitude)
                && is_numeric($ticket->longitude)
                && self::distanceMeters($latitude, $longitude, (float) $ticket->latitude, (float) $ticket->longitude) <= $radiusMeters)
            ->sortBy(static fn (Ticket $ticket): float => self::distanceMeters(
                $latitude, $longitude, (float) $ticket->latitude, (float) $ticket->longitude,
            ))
            ->take(5)
            ->values();
    }

    /**
     * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
     */
    private static function distanceMeters(float $latitudeA, float $longitudeA, float $latitudeB, float $longitudeB): float
    {
        $latDelta = deg2rad($latitudeB - $latitudeA);
        $lngDelta = deg2rad($longitudeB - $longitudeA);
        $angle = sin($latDelta / 2) ** 2
            + cos(deg2rad($latitudeA)) * cos(deg2rad($latitudeB)) * sin($lngDelta / 2) ** 2;

        return 2 * 6371000 * asin(min(1.0, sqrt(max(0.0, $angle))));
    }
}
