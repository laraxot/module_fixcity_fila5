<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

final class NormalizeTicketLocationDataAction
{
    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    public function execute(array $state): array
    {
        $location = $state['location'] ?? null;

        if (\is_string($location)) {
            $decoded = \json_decode($location, true);

            if (\is_array($decoded)) {
                $location = $decoded;
            }
        }

        if (! \is_array($location)) {
            return $state;
        }

        $state['location'] = $location;

        $latitude = $location['latitude'] ?? null;
        $longitude = $location['longitude'] ?? null;
        $address = $location['address'] ?? null;

        $normalizedLatitude = $this->normalizeCoordinate($latitude);
        $normalizedLongitude = $this->normalizeCoordinate($longitude);

        if ($normalizedLatitude !== null) {
            $state['latitude'] = $normalizedLatitude;
        }

        if ($normalizedLongitude !== null) {
            $state['longitude'] = $normalizedLongitude;
        }

        if (\is_string($address) && trim($address) !== '') {
            $state['address'] = trim($address);
        }

        return $state;
    }

    private function normalizeCoordinate(mixed $value): ?string
    {
        if ($value === null || $value === '' || ! \is_numeric($value)) {
            return null;
        }

        return (string) $value;
    }
}
