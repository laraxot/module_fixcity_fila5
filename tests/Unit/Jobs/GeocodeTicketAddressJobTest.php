<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Jobs\GeocodeTicketAddressJob;
use Modules\Fixcity\Models\Ticket;

test('it geocodes ticket address successfully', function (): void {
    // Arrange
    $ticket = Ticket::factory()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'address' => null,
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'display_name' => 'Milano, Lombardia, Italia',
        ], 200),
    ]);

    // Act
    $job = new GeocodeTicketAddressJob($ticket);
    $job->handle();

    // Assert
    expect($ticket->fresh()->address)->toBe('Milano, Lombardia, Italia');
    Http::assertSent(fn ($request) => $request->url() === 'https://nominatim.openstreetmap.org/reverse'
        && $request['lat'] === '45.4642'
        && $request['lon'] === '9.1900'
    );
});

test('it caches geocoding results', function (): void {
    // Arrange
    $ticket = Ticket::factory()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'display_name' => 'Milano, Lombardia, Italia',
        ], 200),
    ]);

    // Act - first call
    $job1 = new GeocodeTicketAddressJob($ticket);
    $job1->handle();

    // Create another ticket with same coordinates
    $ticket2 = Ticket::factory()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    $job2 = new GeocodeTicketAddressJob($ticket2);
    $job2->handle();

    // Assert - HTTP should be called only once due to caching
    Http::assertSentCount(1);
    expect($ticket2->fresh()->address)->toBe('Milano, Lombardia, Italia');
});

test('it handles missing coordinates gracefully', function (): void {
    // Arrange
    Log::fake();
    $ticket = Ticket::factory()->create([
        'latitude' => null,
        'longitude' => null,
    ]);

    // Act
    $job = new GeocodeTicketAddressJob($ticket);
    $job->handle();

    // Assert
    Log::assertLogged('warning', fn ($message) => str_contains($message, 'Ticket missing coordinates'));
    expect($ticket->fresh()->address)->toBeNull();
});

test('it handles nominatim API errors', function (): void {
    // Arrange
    Log::fake();
    $ticket = Ticket::factory()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([], 500),
    ]);

    // Act
    $job = new GeocodeTicketAddressJob($ticket);
    $job->handle();

    // Assert
    Log::assertLogged('error', fn ($message) => str_contains($message, 'Nominatim API error'));
    expect($ticket->fresh()->address)->toBeNull();
});

test('it handles invalid response format', function (): void {
    // Arrange
    $ticket = Ticket::factory()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response('invalid response', 200),
    ]);

    // Act
    $job = new GeocodeTicketAddressJob($ticket);
    $job->handle();

    // Assert
    expect($ticket->fresh()->address)->toBeNull();
});

test('it handles missing display_name in response', function (): void {
    // Arrange
    $ticket = Ticket::factory()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'lat' => '45.4642',
            'lon' => '9.1900',
        ], 200),
    ]);

    // Act
    $job = new GeocodeTicketAddressJob($ticket);
    $job->handle();

    // Assert
    expect($ticket->fresh()->address)->toBeNull();
});

test('it retries on failure', function (): void {
    // Arrange
    $ticket = Ticket::factory()->create([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    // First call fails, second succeeds
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::sequence()
            ->push([], 500)
            ->push(['display_name' => 'Milano, Lombardia, Italia'], 200),
    ]);

    // Act
    $job = new GeocodeTicketAddressJob($ticket);
    $job->handle();

    // Assert
    expect($ticket->fresh()->address)->toBe('Milano, Lombardia, Italia');
    Http::assertSentCount(2); // Initial + 1 retry
});
