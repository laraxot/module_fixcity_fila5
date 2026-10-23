<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Models\Ticket;
use Modules\Geo\Infrastructure\Config\GeoApiEndpoints;
use Spatie\QueueableAction\QueueableAction;

/** Fills the ticket JSON location with a cached Nominatim reverse geocode. */
final class GeocodeTicketAddressAction
{
    use QueueableAction;

    private const int MAX_ATTEMPTS = 2;

    public function execute(Ticket $ticket): void
    {
        $latitude = $ticket->latitude;
        $longitude = $ticket->longitude;
        if ($latitude === null || $latitude === '' || $longitude === null || $longitude === '') {
            Log::warning('Ticket missing coordinates', ['ticket_id' => $ticket->getKey()]);

            return;
        }

        $cacheKey = sprintf('geocode:reverse:%s:%s', $latitude, $longitude);
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            $this->persistAddress($ticket, $cached);

            return;
        }

        $displayName = $this->fetchDisplayName($ticket, $latitude, $longitude);
        if ($displayName === null) {
            return;
        }

        Cache::put($cacheKey, $displayName, now()->addDay());
        $this->persistAddress($ticket, $displayName);
    }

    private function fetchDisplayName(Ticket $ticket, string $latitude, string $longitude): ?string
    {
        $lastStatus = 0;
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $response = Http::withHeaders(['User-Agent' => 'Fixcity/1.0 (GeocodeTicketAddressAction)'])
                ->get(GeoApiEndpoints::NOMINATIM_REVERSE, [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                ]);
            $lastStatus = $response->status();
            if ($response->successful()) {
                return $this->extractDisplayName($response);
            }
        }

        Log::error('Nominatim API error', ['ticket_id' => $ticket->getKey(), 'status' => $lastStatus]);

        return null;
    }

    private function extractDisplayName(Response $response): ?string
    {
        $payload = $response->json();
        $displayName = is_array($payload) ? ($payload['display_name'] ?? null) : null;

        return is_string($displayName) && $displayName !== '' ? $displayName : null;
    }

    private function persistAddress(Ticket $ticket, string $displayName): void
    {
        $location = is_array($ticket->location) ? $ticket->location : [];
        $location['address'] = $displayName;
        $location['display_name'] = $displayName;
        $location['provider'] = 'nominatim';
        $location['latitude'] = $ticket->latitude;
        $location['longitude'] = $ticket->longitude;
        $location['lat'] = $ticket->latitude;
        $location['lng'] = $ticket->longitude;
        $ticket->forceFill(['location' => $location])->save();
    }
}
