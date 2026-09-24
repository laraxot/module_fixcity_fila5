<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models\Concerns;

trait NormalizesTicketLocation
{
    /**
     * @param  array<string, mixed>  $value
     * @return array<string, string|array<string, mixed>|null>
     */
    public static function extractAddressComponents(array $value): array
    {
        $details = $value['address_components'] ?? $value['addressdetails'] ?? $value['address_details'] ?? null;
        if (! \is_array($details) || $details === []) {
            return [];
        }

        /** @var array<string, mixed> $filtered */
        $filtered = array_filter([
            'street' => self::normalizeNullableText($value['street'] ?? $details['street'] ?? null),
            'street_number' => self::normalizeNullableText($value['street_number'] ?? $details['house_number'] ?? null),
            'zip' => self::normalizeNullableText($value['zip'] ?? $value['postcode'] ?? $details['postcode'] ?? null),
            'postcode' => self::normalizeNullableText($value['postcode'] ?? $details['postcode'] ?? null),
            'city' => self::normalizeNullableText($value['city'] ?? $details['city'] ?? $details['village'] ?? $details['municipality'] ?? null),
            'province' => self::normalizeNullableText($value['province'] ?? $details['county'] ?? $details['state_district'] ?? null),
            'state' => self::normalizeNullableText($value['state'] ?? $details['state'] ?? $details['region'] ?? null),
            'country' => self::normalizeNullableText($value['country'] ?? $details['country'] ?? null),
            'country_code' => self::normalizeNullableText($value['country_code'] ?? $details['country_code'] ?? null),
            'suburb' => self::normalizeNullableText($value['suburb'] ?? $details['suburb'] ?? $details['neighbourhood'] ?? null),
            'address_details' => $details,
        ], static fn (mixed $item): bool => $item !== null && $item !== '');

        /** @var array<string, string|null> $result */
        $result = [];
        foreach ($filtered as $key => $item) {
            $result[$key] = is_string($item) ? $item : null;
        }

        return $result;
    }

    public static function normalizeCoordinateString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_int($value) || \is_float($value) || (\is_string($value) && is_numeric($value))) {
            return (string) $value;
        }

        return null;
    }

    private static function normalizeText(mixed $value): string
    {
        return \is_string($value) ? trim($value) : '';
    }

    private static function normalizeNullableText(mixed $value): ?string
    {
        $normalized = self::normalizeText($value);

        return $normalized !== '' ? $normalized : null;
    }

    /**
     * @param  array<mixed>  $value
     * @return array<string, mixed>
     */
    public static function stringKeyed(array $value): array
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
