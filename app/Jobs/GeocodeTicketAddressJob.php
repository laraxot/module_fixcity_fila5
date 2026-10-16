<?php

declare(strict_types=1);

namespace Modules\Fixcity\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Models\Ticket;
use Modules\Geo\Support\GeoApiEndpoints;

/**
 * Reverse-geocode delle coordinate di un ticket verso Nominatim.
 *
 * Scopo: riempire `location.address` / `location.display_name` quando il ticket
 * ha lat/lng ma manca l'indirizzo leggibile (FO/mappa). Usa cache per coordinate
 * uguali e un retry su errore HTTP. Non esiste colonna `address`: SSoT e' il JSON
 * `location` (vedi ticket-location-json-architecture).
 */
final class GeocodeTicketAddressJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const int MAX_ATTEMPTS = 2;

    public function __construct(private readonly Ticket $ticket) {}

    public function handle(): void
    {
        $latitude = $this->ticket->latitude;
        $longitude = $this->ticket->longitude;

        if ($latitude === null || $latitude === '' || $longitude === null || $longitude === '') {
            Log::warning('Ticket missing coordinates', [
                'ticket_id' => $this->ticket->getKey(),
            ]);

            return;
        }

        $cacheKey = sprintf('geocode:reverse:%s:%s', $latitude, $longitude);
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            $this->persistAddress($cached);

            return;
        }

        $displayName = $this->fetchDisplayName($latitude, $longitude);
        if ($displayName === null) {
            return;
        }

        Cache::put($cacheKey, $displayName, now()->addDay());
        $this->persistAddress($displayName);
    }

    private function fetchDisplayName(string $latitude, string $longitude): ?string
    {
        $lastStatus = 0;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $response = Http::withHeaders([
                'User-Agent' => 'Fixcity/1.0 (GeocodeTicketAddressJob)',
            ])->get(GeoApiEndpoints::NOMINATIM_REVERSE, [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
            ]);

            $lastStatus = $response->status();

            if ($response->successful()) {
                return $this->extractDisplayName($response);
            }
        }

        Log::error('Nominatim API error', [
            'ticket_id' => $this->ticket->getKey(),
            'status' => $lastStatus,
        ]);

        return null;
    }

    private function extractDisplayName(Response $response): ?string
    {
        $payload = $response->json();
        if (! is_array($payload)) {
            return null;
        }

        $displayName = $payload['display_name'] ?? null;

        return is_string($displayName) && $displayName !== '' ? $displayName : null;
    }

    private function persistAddress(string $displayName): void
    {
        $location = $this->ticket->location;
        if (! is_array($location)) {
            $location = [];
        }

        $location['address'] = $displayName;
        $location['display_name'] = $displayName;
        $location['provider'] = 'nominatim';
        $location['latitude'] = $this->ticket->latitude;
        $location['longitude'] = $this->ticket->longitude;
        $location['lat'] = $this->ticket->latitude;
        $location['lng'] = $this->ticket->longitude;

        $this->ticket->forceFill([
            'location' => $location,
        ])->save();
    }
}
