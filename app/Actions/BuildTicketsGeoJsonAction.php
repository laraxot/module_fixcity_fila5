<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;

/**
 * GeoJSON FeatureCollection da ticket geolocalizzati (mappa pubblica).
 */
final class BuildTicketsGeoJsonAction
{
    public function __construct(
        private readonly ResolveTicketTypeMarkerPropertiesAction $resolveTypeMarker,
        private readonly ResolveTicketStatusMarkerPropertiesAction $resolveStatusMarker,
    ) {
    }

    /**
     * @param  Builder<Ticket>  $query
     * @param  array<int, string>  $types
     * @return array{
     *     type: string,
     *     generated_at: string,
     *     total: int,
     *     features: array<int, array<string, mixed>>
     * }
     */
    public function execute(Builder $query, array $types = []): array
    {
        $scoped = clone $query;
        if ($types !== []) {
            $scoped->whereIn('type', $types);
        }

        /** @var Collection<int, Ticket> $tickets */
        $tickets = $scoped->latest()->get();

        $features = $tickets
            ->map(fn (Ticket $ticket): ?array => $this->ticketToFeature($ticket))
            ->filter()
            ->values()
            ->all();

        return [
            'type' => 'FeatureCollection',
            'generated_at' => (string) now()->toISOString(),
            'total' => \count($features),
            'features' => $features,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function ticketToFeature(Ticket $ticket): ?array
    {
        $location = $ticket->location;
        if (! \is_array($location)) {
            return null;
        }

        $lat = SafeFloatCastAction::cast($location['lat'] ?? $location['latitude'] ?? $ticket->getAttribute('latitude') ?? 0);
        $lng = SafeFloatCastAction::cast($location['lng'] ?? $location['longitude'] ?? $ticket->getAttribute('longitude') ?? 0);

        if ($lat === 0.0 && $lng === 0.0) {
            return null;
        }

        $rawType = $ticket->getAttribute('type');
        $typeValue = $rawType instanceof \BackedEnum
            ? (string) $rawType->value
            : (is_string($rawType) ? $rawType : 'other');

        $typeProps = $this->resolveTypeMarker->executeFromValue($typeValue);

        $statusValue = $ticket->resolveTicketStatusValue();
        $statusProps = $this->resolveStatusMarker->executeFromValue($statusValue);

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$lng, $lat],
            ],
            'properties' => [
                'id' => $ticket->id,
                'title' => $ticket->name,
                'type' => $typeProps,
                'address' => $location['address'] ?? $location['display_name'] ?? '',
                'city' => $location['city'] ?? '',
                'status' => $statusProps,
                'url' => '/it/tickets/'.$ticket->id,
                'detail_url' => '/it/tickets/'.$ticket->id,
            ],
        ];
    }
}
