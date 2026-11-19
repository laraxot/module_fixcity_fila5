<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Datas\PublicTicketGeoJsonOptions;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DB-12 / SEC-12: GET /api/tickets/geojson con limite, bbox, colonne ridotte e cache breve per gli ospiti.
 * Il contratto dei consumatori (map-lit, viste mobile, widget admin) e' `type`, `features[].geometry`, `features[].properties`.
 * Story: Modules/Fixcity/docs/stories/public-api-media-and-geojson.story.md
 */

beforeEach(function (): void {
    Cache::flush();
});

/**
 * @param  array<string, mixed>  $attributes
 */
function geoJsonLimitTicket(float $lat, float $lng, array $attributes = []): Ticket
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => 'Segnalazione '.$lat,
        'content' => 'Testo libero che non deve uscire nella mappa.',
        'status' => TicketStatusEnum::IN_PROGRESS,
        'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        'location' => ['lat' => $lat, 'lng' => $lng, 'address' => 'Via Test', 'city' => 'Mogliano'],
        'latitude' => $lat,
        'longitude' => $lng,
    ], $attributes));
}

/**
 * @return list<int>
 */
function geoJsonIds(mixed $features): array
{
    Assert::assertIsArray($features);

    return array_values(array_map(static fn (mixed $feature): int => SafeIntCastAction::cast(data_get($feature, 'properties.id')), $features));
}

it('keeps the existing contract without parameters and adds limit and truncated', function (): void {
    /** @var TestCase $this */
    $ticket = geoJsonLimitTicket(45.5, 12.2);

    $json = $this->getJson('/api/tickets/geojson')->assertOk()
        ->assertJsonPath('type', 'FeatureCollection')
        ->assertJsonPath('limit', PublicTicketGeoJsonOptions::MAX_FEATURES)
        ->assertJsonPath('truncated', false)
        ->json();
    Assert::assertIsArray($json);
    $features = $json['features'] ?? null;
    Assert::assertIsArray($features);
    Assert::assertContains($ticket->id, geoJsonIds($features));
    Assert::assertSame(count($features), $json['total']);

    $feature = collect($features)->firstWhere('properties.id', $ticket->id);
    Assert::assertIsArray($feature);
    $properties = $feature['properties'] ?? null;
    Assert::assertIsArray($properties);
    Assert::assertSame(['address', 'city', 'detail_url', 'id', 'status', 'title', 'type'], collect($properties)->keys()->sort()->values()->all());
});

it('caps the number of features and flags the truncation', function (): void {
    /** @var TestCase $this */
    geoJsonLimitTicket(45.1, 12.1);
    geoJsonLimitTicket(45.2, 12.2);

    $this->getJson('/api/tickets/geojson?limit=1')->assertOk()
        ->assertJsonCount(1, 'features')
        ->assertJsonPath('limit', 1)
        ->assertJsonPath('truncated', true);

    $this->getJson('/api/tickets/geojson?limit=999999')->assertOk()->assertJsonPath('limit', PublicTicketGeoJsonOptions::MAX_FEATURES);
});

it('filters by bounding box and ignores an invalid one', function (): void {
    /** @var TestCase $this */
    $inside = geoJsonLimitTicket(45.55, 12.23);
    $outside = geoJsonLimitTicket(41.90, 12.49);

    $ids = geoJsonIds($this->getJson('/api/tickets/geojson?bbox=12.0,45.0,12.5,46.0')->assertOk()->json('features'));
    Assert::assertContains($inside->id, $ids);
    Assert::assertNotContains($outside->id, $ids);

    $all = geoJsonIds($this->getJson('/api/tickets/geojson?bbox=nonsense')->assertOk()->json('features'));
    Assert::assertContains($outside->id, $all);
});

it('never exposes non-public tickets to guests', function (): void {
    /** @var TestCase $this */
    $pending = geoJsonLimitTicket(45.3, 12.3, ['status' => TicketStatusEnum::PENDING]);

    Assert::assertNotContains($pending->id, geoJsonIds($this->getJson('/api/tickets/geojson')->assertOk()->json('features')));
});

it('caches the guest response briefly and never caches an authenticated one', function (): void {
    /** @var TestCase $this */
    $first = geoJsonLimitTicket(45.4, 12.4);
    $before = geoJsonIds($this->getJson('/api/tickets/geojson')->assertOk()->json('features'));
    Assert::assertContains($first->id, $before);

    $second = geoJsonLimitTicket(45.6, 12.6);
    // stessa richiesta entro il TTL: risposta dalla cache
    Assert::assertSame($before, geoJsonIds($this->getJson('/api/tickets/geojson')->assertOk()->json('features')));
    // filtri diversi: chiave diversa
    Assert::assertContains($second->id, geoJsonIds($this->getJson('/api/tickets/geojson?limit=1999')->assertOk()->json('features')));

    // utente loggato: query viva (include le sue non pubbliche, mai condivise)
    $owner = UserFactory::new()->createOne();
    $own = geoJsonLimitTicket(45.7, 12.7, ['status' => TicketStatusEnum::PENDING, 'owner_id' => $owner->getKey()]);
    $ids = geoJsonIds($this->actingAs($owner)->getJson('/api/tickets/geojson')->assertOk()->json('features'));
    Assert::assertContains($second->id, $ids);
    Assert::assertContains($own->id, $ids);
});
