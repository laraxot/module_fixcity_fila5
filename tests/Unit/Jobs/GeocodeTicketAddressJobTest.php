<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Jobs\GeocodeTicketAddressJob;
use Modules\Fixcity\Models\Ticket;
use PHPUnit\Framework\Assert;

/**
 * Scopo: reverse-geocode Nominatim → `location.address` (nessuna colonna address).
 * Cache per coordinate uguali; warning/error su log; retry su HTTP 500.
 */

test('it geocodes ticket address successfully', function (): void {
    $ticket = TicketFactory::new()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'display_name' => 'Milano, Lombardia, Italia',
        ], 200),
    ]);

    (new GeocodeTicketAddressJob($ticket))->handle();

    $fresh = $ticket->fresh();
    Assert::assertInstanceOf(Ticket::class, $fresh);
    $location = $fresh->location;
    Assert::assertIsArray($location);
    Assert::assertSame('Milano, Lombardia, Italia', $location['address'] ?? null);

    Http::assertSent(static function (Request $request): bool {
        $data = $request->data();

        return $request->url() === 'https://nominatim.openstreetmap.org/reverse'
            && ($data['lat'] ?? null) === '45.4642'
            && ($data['lon'] ?? null) === '9.1900';
    });
});

test('it caches geocoding results', function (): void {
    $ticket = TicketFactory::new()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'display_name' => 'Milano, Lombardia, Italia',
        ], 200),
    ]);

    (new GeocodeTicketAddressJob($ticket))->handle();

    $ticket2 = TicketFactory::new()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket2);

    (new GeocodeTicketAddressJob($ticket2))->handle();

    Http::assertSentCount(1);
    $fresh2 = $ticket2->fresh();
    Assert::assertInstanceOf(Ticket::class, $fresh2);
    $location = $fresh2->location;
    Assert::assertIsArray($location);
    Assert::assertSame('Milano, Lombardia, Italia', $location['address'] ?? null);
});

test('it handles missing coordinates gracefully', function (): void {
    Log::shouldReceive('warning')
        ->once()
        ->withArgs(static function (string $message): bool {
            return str_contains($message, 'Ticket missing coordinates');
        });

    $ticket = TicketFactory::new()->create([
        'latitude' => null,
        'longitude' => null,
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket);

    (new GeocodeTicketAddressJob($ticket))->handle();

    $fresh = $ticket->fresh();
    Assert::assertInstanceOf(Ticket::class, $fresh);
    Assert::assertNull($fresh->location['address'] ?? null);
});

test('it handles nominatim API errors', function (): void {
    Log::shouldReceive('error')
        ->once()
        ->withArgs(static function (string $message): bool {
            return str_contains($message, 'Nominatim API error');
        });

    $ticket = TicketFactory::new()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([], 500),
    ]);

    (new GeocodeTicketAddressJob($ticket))->handle();

    $fresh = $ticket->fresh();
    Assert::assertInstanceOf(Ticket::class, $fresh);
    Assert::assertNull($fresh->location['address'] ?? null);
});

test('it handles invalid response format', function (): void {
    $ticket = TicketFactory::new()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response('invalid response', 200),
    ]);

    (new GeocodeTicketAddressJob($ticket))->handle();

    $fresh = $ticket->fresh();
    Assert::assertInstanceOf(Ticket::class, $fresh);
    Assert::assertNull($fresh->location['address'] ?? null);
});

test('it handles missing display_name in response', function (): void {
    $ticket = TicketFactory::new()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'lat' => '45.4642',
            'lon' => '9.1900',
        ], 200),
    ]);

    (new GeocodeTicketAddressJob($ticket))->handle();

    $fresh = $ticket->fresh();
    Assert::assertInstanceOf(Ticket::class, $fresh);
    Assert::assertNull($fresh->location['address'] ?? null);
});

test('it retries on failure', function (): void {
    $ticket = TicketFactory::new()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);
    Assert::assertInstanceOf(Ticket::class, $ticket);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::sequence()
            ->push([], 500)
            ->push(['display_name' => 'Milano, Lombardia, Italia'], 200),
    ]);

    (new GeocodeTicketAddressJob($ticket))->handle();

    $fresh = $ticket->fresh();
    Assert::assertInstanceOf(Ticket::class, $fresh);
    $location = $fresh->location;
    Assert::assertIsArray($location);
    Assert::assertSame('Milano, Lombardia, Italia', $location['address'] ?? null);
    Http::assertSentCount(2);
});
