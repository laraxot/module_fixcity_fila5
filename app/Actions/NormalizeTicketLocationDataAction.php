<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use function Safe\json_decode;

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
            $decoded = json_decode($location, true);

            if (\is_array($decoded)) {
                $location = $decoded;
            }
        }

        if (! \is_array($location)) {
            return $state;
        }

        /** @var array<string, mixed> $locationPayload */
        $locationPayload = $this->stringKeyed($location);

        $latitude = $locationPayload['latitude'] ?? $locationPayload['lat'] ?? null;
        $longitude = $locationPayload['longitude'] ?? $locationPayload['lng'] ?? null;

        $normalizedLatitude = $this->normalizeCoordinate($latitude);
        $normalizedLongitude = $this->normalizeCoordinate($longitude);

        if ($normalizedLatitude !== null) {
            $state['latitude'] = $normalizedLatitude;
        }

        if ($normalizedLongitude !== null) {
            $state['longitude'] = $normalizedLongitude;
        }

        $state['location'] = $this->normalizeLocationPayload($locationPayload);
        unset($state['address']);

        return $state;
    }

    /**
     * @param  array<string, mixed>  $location
     * @return array<string, mixed>
     */
    private function normalizeLocationPayload(array $location): array
    {
        $details = $location['address_details'] ?? $location['addressdetails'] ?? $location['address_components'] ?? null;
        if (! \is_array($details)) {
            $details = [];
        }

        return array_filter([
            'lat' => $this->normalizeCoordinate($location['lat'] ?? $location['latitude'] ?? null),
            'lng' => $this->normalizeCoordinate($location['lng'] ?? $location['longitude'] ?? null),
            'address' => $this->normalizeText($location['address'] ?? $location['display_name'] ?? null),
            'display_name' => $this->normalizeText($location['display_name'] ?? null),
            'provider' => $this->normalizeText($location['provider'] ?? null),
            'street' => $this->normalizeText($location['street'] ?? $details['road'] ?? $details['street'] ?? null),
            'street_number' => $this->normalizeText($location['street_number'] ?? $details['house_number'] ?? null),
            'zip' => $this->normalizeText($location['zip'] ?? $location['postcode'] ?? $details['postcode'] ?? null),
            'postcode' => $this->normalizeText($location['postcode'] ?? $details['postcode'] ?? null),
            'city' => $this->normalizeText($location['city'] ?? $details['city'] ?? $details['town'] ?? $details['village'] ?? $details['municipality'] ?? null),
            'province' => $this->normalizeText($location['province'] ?? $details['county'] ?? $details['state_district'] ?? null),
            'state' => $this->normalizeText($location['state'] ?? $details['state'] ?? $details['region'] ?? null),
            'country' => $this->normalizeText($location['country'] ?? $details['country'] ?? null),
            'country_code' => $this->normalizeText($location['country_code'] ?? $details['country_code'] ?? null),
            'suburb' => $this->normalizeText($location['suburb'] ?? $details['suburb'] ?? $details['neighbourhood'] ?? null),
            'address_details' => $details !== [] ? $details : null,
        ], static fn (mixed $item): bool => $item !== null && $item !== '');
    }

    private function normalizeCoordinate(mixed $value): ?string
    {
        if ($value === null || $value === '' || ! \is_numeric($value)) {
            return null;
        }

        return (string) $value;
    }

    private function normalizeText(mixed $value): ?string
    {
        if (! \is_string($value)) {
            return null;
        }

        $normalized = trim($value);

        return $normalized !== '' ? $normalized : null;
    }

    /**
     * @param  array<mixed>  $value
     * @return array<string, mixed>
     */
    private function stringKeyed(array $value): array
    {
        $normalized = [];

        foreach ($value as $key => $item) {
            if (\is_string($key)) {
                $normalized[$key] = $item;
            }
        }

        return $normalized;
    }
}
