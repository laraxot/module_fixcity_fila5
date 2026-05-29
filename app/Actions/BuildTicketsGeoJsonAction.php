<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Fixcity\Models\Ticket;

/**
 * GeoJSON FeatureCollection da ticket geolocalizzati (mappa pubblica).
 */
final class BuildTicketsGeoJsonAction
{
    public function __construct(
        private readonly ResolveTicketTypeMarkerPropertiesAction $resolveTypeMarker,
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
            'generated_at' => now()->toISOString(),
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

        $lat = (float) ($location['lat'] ?? $location['latitude'] ?? $ticket->getAttribute('latitude') ?? 0);
        $lng = (float) ($location['lng'] ?? $location['longitude'] ?? $ticket->getAttribute('longitude') ?? 0);

        if ($lat === 0.0 && $lng === 0.0) {
            return null;
        }

        $rawType = $ticket->getAttribute('type');
        $typeValue = $rawType instanceof \BackedEnum
            ? (string) $rawType->value
            : (is_string($rawType) ? $rawType : 'other');

        $typeProps = $this->resolveTypeMarker->executeFromValue($typeValue);

        $statusValue = $ticket->resolveTicketStatusValue();

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
                'status' => $statusValue,
                'url' => '/it/tests/segnalazione-dettaglio?id='.$ticket->id,
                'detail_url' => '/it/tests/segnalazione-dettaglio?id='.$ticket->id,
            ],
        ];
    }
}
