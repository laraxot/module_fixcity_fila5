<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use InvalidArgumentException;
use Spatie\QueueableAction\QueueableAction;

/**
 * Validates the public ticket export against the RFC 7946 Point contract.
 *
 * GeoJSON coordinates are always [longitude, latitude], never [latitude,
 * longitude]. The additional generated_at/total members are application
 * metadata and do not alter the FeatureCollection contract.
 */
final class ValidateTicketsGeoJsonAction
{
    use QueueableAction;

    /**
     * @param  array<string, mixed>  $geoJson
     * @return array<string, mixed>
     */
    public function execute(array $geoJson): array
    {
        if (($geoJson['type'] ?? null) !== 'FeatureCollection') {
            throw new InvalidArgumentException('Tickets GeoJSON must be a FeatureCollection.');
        }

        $features = $geoJson['features'] ?? null;
        if (! is_array($features)) {
            throw new InvalidArgumentException('Tickets GeoJSON features must be an array.');
        }

        foreach ($features as $index => $feature) {
            if (! is_array($feature) || ($feature['type'] ?? null) !== 'Feature') {
                throw new InvalidArgumentException(sprintf('Invalid GeoJSON feature at index %s.', (string) $index));
            }

            $geometry = $feature['geometry'] ?? null;
            if (! is_array($geometry) || ($geometry['type'] ?? null) !== 'Point') {
                throw new InvalidArgumentException(sprintf('Ticket feature %s must contain a Point geometry.', (string) $index));
            }

            $coordinates = $geometry['coordinates'] ?? null;
            if (! is_array($coordinates) || count($coordinates) < 2) {
                throw new InvalidArgumentException(sprintf('Ticket feature %s has invalid coordinates.', (string) $index));
            }

            $longitude = $coordinates[0] ?? null;
            $latitude = $coordinates[1] ?? null;
            if (! is_int($longitude) && ! is_float($longitude)) {
                throw new InvalidArgumentException(sprintf('Ticket feature %s longitude must be numeric.', (string) $index));
            }
            if (! is_int($latitude) && ! is_float($latitude)) {
                throw new InvalidArgumentException(sprintf('Ticket feature %s latitude must be numeric.', (string) $index));
            }
            if (! is_finite((float) $longitude) || ! is_finite((float) $latitude)
                || $longitude < -180 || $longitude > 180
                || $latitude < -90 || $latitude > 90) {
                throw new InvalidArgumentException(sprintf('Ticket feature %s coordinates are outside WGS84 bounds.', (string) $index));
            }

            if (! is_array($feature['properties'] ?? null)) {
                throw new InvalidArgumentException(sprintf('Ticket feature %s properties must be an object.', (string) $index));
            }
        }

        return $geoJson;
    }
}
