<?php

declare(strict_types=1);

namespace Modules\Fixcity\Datas;

use Illuminate\Http\Request;

/**
 * Opzioni della mappa pubblica (GET /api/tickets/geojson) oltre ai filtri dell'elenco ({@see PublicTicketListFilters}).
 *
 * - `limit`: numero massimo di feature (1..MAX_FEATURES). Senza parametro vale MAX_FEATURES: i consumatori di oggi
 *   (map-lit, viste mobile, widget admin) chiedono la collezione intera senza parametri e continuano a funzionare.
 * - `bbox=minLng,minLat,maxLng,maxLat` (ordine GeoJSON/RFC 7946): solo i ticket dentro il riquadro.
 *
 * Valori non validi vengono ignorati (mai un errore per un link vecchio), come in PublicTicketListFilters.
 */
final readonly class PublicTicketGeoJsonOptions
{
    /** Tetto rigido di feature per risposta (DB-12, SEC-12). */
    public const int MAX_FEATURES = 2000;

    /** Durata della cache della risposta per gli ospiti, in secondi. */
    public const int CACHE_TTL_SECONDS = 60;

    /**
     * @param  array{0: float, 1: float, 2: float, 3: float}|null  $bbox  [minLng, minLat, maxLng, maxLat]
     */
    public function __construct(
        public int $limit = self::MAX_FEATURES,
        public ?array $bbox = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $rawLimit = $request->query('limit');
        $limit = is_numeric($rawLimit) ? min(self::MAX_FEATURES, max(1, (int) $rawLimit)) : self::MAX_FEATURES;

        return new self($limit, self::parseBbox($request->query('bbox')));
    }

    /**
     * Parte della chiave di cache: due richieste con le stesse opzioni condividono la risposta.
     */
    public function cacheKeyPart(): string
    {
        $bbox = $this->bbox === null
            ? 'all'
            : implode(',', array_map(static fn (float $value): string => number_format($value, 5, '.', ''), $this->bbox));

        return 'l'.$this->limit.'|b'.$bbox;
    }

    /**
     * @return array{0: float, 1: float, 2: float, 3: float}|null
     */
    private static function parseBbox(mixed $raw): ?array
    {
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $parts = explode(',', $raw);
        if (count($parts) !== 4) {
            return null;
        }

        $values = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if (! is_numeric($part)) {
                return null;
            }
            $values[] = (float) $part;
        }

        [$minLng, $minLat, $maxLng, $maxLat] = $values;
        $inRange = $minLng >= -180.0 && $maxLng <= 180.0 && $minLat >= -90.0 && $maxLat <= 90.0;
        if (! $inRange || $minLng > $maxLng || $minLat > $maxLat) {
            return null;
        }

        return [$minLng, $minLat, $maxLng, $maxLat];
    }
}
