<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * GeoJSON FeatureCollection da ticket geolocalizzati (mappa pubblica).
 *
 * Legge solo le colonne che la feature usa (mai `content`, `owner_id`, `code`) e nessun media: la feature non ha immagini.
 * `limit` e `bbox` sono opzionali (DB-12): senza, il comportamento resta quello dell'export statico (tutti i ticket della query).
 */
final class BuildTicketsGeoJsonAction
{
    use QueueableAction;

    public function __construct(
        private readonly ResolveTicketTypeMarkerPropertiesAction $resolveTypeMarker,
        private readonly ResolveTicketStatusMarkerPropertiesAction $resolveStatusMarker,
    ) {}

    /** Colonne lette per costruire una feature: nessun campo personale o testo libero lungo. */
    public const array FEATURE_COLUMNS = ['id', 'name', 'type', 'status', 'location', 'latitude', 'longitude', 'created_at'];

    /**
     * @param  Builder<Ticket>  $query
     * @param  array<int, string>  $types
     * @param  int|null  $limit  numero massimo di ticket letti; null = nessun limite (export statico, aggregati)
     * @param  array{0: float, 1: float, 2: float, 3: float}|null  $bbox  [minLng, minLat, maxLng, maxLat]
     * @return array{
     *     type: string,
     *     generated_at: string,
     *     total: int,
     *     limit: int|null,
     *     truncated: bool,
     *     features: array<int, array<string, mixed>>
     * }
     */
    public function execute(Builder $query, array $types = [], ?int $limit = null, ?array $bbox = null): array
    {
        $scoped = clone $query;
        $scoped->select(array_map(
            static fn (string $column): string => $scoped->getModel()->qualifyColumn($column),
            self::FEATURE_COLUMNS,
        ));
        if ($types !== []) {
            $scoped->whereIn('type', $types);
        }
        if ($bbox !== null) {
            [$minLng, $minLat, $maxLng, $maxLat] = $bbox;
            $scoped->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLng, $maxLng]);
        }

        $scoped->latest();
        if ($limit !== null) {
            // un elemento in piu' solo per sapere se la risposta e' troncata
            $scoped->take(max(1, $limit) + 1);
        }

        /** @var Collection<int, Ticket> $tickets */
        $tickets = $scoped->get();
        $truncated = $limit !== null && $tickets->count() > max(1, $limit);
        if ($truncated) {
            $tickets = $tickets->take(max(1, $limit));
        }

        $features = $tickets
            ->map(fn (Ticket $ticket): ?array => $this->ticketToFeature($ticket))
            ->filter()
            ->values()
            ->all();

        return [
            'type' => 'FeatureCollection',
            'generated_at' => (string) now()->toISOString(),
            'total' => \count($features),
            'limit' => $limit,
            'truncated' => $truncated,
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

        $typeProps = $this->resolveTypeMarker->execute($typeValue);

        $statusValue = $ticket->resolveTicketStatusValue();
        $statusProps = $this->resolveStatusMarker->execute($statusValue);

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
                'detail_url' => '/'.app()->getLocale().'/tickets/'.rawurlencode((string) $ticket->id),
            ],
        ];
    }
}
