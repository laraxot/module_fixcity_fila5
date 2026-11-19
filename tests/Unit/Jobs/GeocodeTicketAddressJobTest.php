<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Actions\GeocodeTicketAddressAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Il job `Modules\Fixcity\Jobs\GeocodeTicketAddressJob` non esiste piu': il caso d'uso e' la Queueable Action
 * `GeocodeTicketAddressAction`, che scrive l'indirizzo in `location['address']` (il ticket non ha una colonna `address`).
 */
$addressOf = static function (Ticket $ticket): ?string {
    $address = Arr::get($ticket->refresh()->location ?? [], 'address');

    return is_string($address) ? $address : null;
};

test('it geocodes ticket address successfully', function () use ($addressOf): void {
    $ticket = TicketFactory::new()->createOne([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'location' => null,
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'display_name' => 'Milano, Lombardia, Italia',
        ], 200),
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket);

    Assert::assertSame('Milano, Lombardia, Italia', $addressOf($ticket));
    Http::assertSent(static function (Request $request): bool {
        $query = $request->data();

        return str_starts_with($request->url(), 'https://nominatim.openstreetmap.org/reverse')
            && $query['lat'] === '45.4642'
            && $query['lon'] === '9.1900';
    });
});

test('it caches geocoding results', function () use ($addressOf): void {
    $ticket = TicketFactory::new()->createOne([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'display_name' => 'Milano, Lombardia, Italia',
        ], 200),
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket);

    $ticket2 = TicketFactory::new()->createOne([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket2);

    Http::assertSentCount(1);
    Assert::assertSame('Milano, Lombardia, Italia', $addressOf($ticket2));
});

test('it handles missing coordinates gracefully', function () use ($addressOf): void {
    $log = Log::spy();
    $ticket = TicketFactory::new()->createOne([
        'latitude' => null,
        'longitude' => null,
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket);

    $log->shouldHaveReceived('warning', ['Ticket missing coordinates', ['ticket_id' => $ticket->getKey()]]);
    Assert::assertNull($addressOf($ticket));
});

test('it handles nominatim API errors', function () use ($addressOf): void {
    $log = Log::spy();
    $ticket = TicketFactory::new()->createOne([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([], 500),
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket);

    $log->shouldHaveReceived('error', ['Nominatim API error', ['ticket_id' => $ticket->getKey(), 'status' => 500]]);
    Assert::assertNull($addressOf($ticket));
});

test('it handles invalid response format', function () use ($addressOf): void {
    $ticket = TicketFactory::new()->createOne([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response('invalid response', 200),
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket);

    Assert::assertNull($addressOf($ticket));
});

test('it handles missing display_name in response', function () use ($addressOf): void {
    $ticket = TicketFactory::new()->createOne([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            'lat' => '45.4642',
            'lon' => '9.1900',
        ], 200),
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket);

    Assert::assertNull($addressOf($ticket));
});

test('it retries on failure', function () use ($addressOf): void {
    $ticket = TicketFactory::new()->createOne([
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::sequence()
            ->push([], 500)
            ->push(['display_name' => 'Milano, Lombardia, Italia'], 200),
    ]);

    app(GeocodeTicketAddressAction::class)->execute($ticket);

    Assert::assertSame('Milano, Lombardia, Italia', $addressOf($ticket));
    Http::assertSentCount(2);
});
