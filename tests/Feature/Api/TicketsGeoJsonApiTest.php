<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Api;

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

uses(TestCase::class);

it('returns geojson feature collection from live api', function (): void {
    /** @var TestCase $this */
    $response = $this->getJson('/api/tickets/geojson');

    $response->assertOk()
        ->assertJsonStructure([
            'type',
            'generated_at',
            'total',
            'features',
        ])
        ->assertJsonPath('type', 'FeatureCollection');
});

it('returns ticket details or not found for unknown id', function (): void {
    /** @var TestCase $this */
    $response = $this->getJson('/api/ticket-details/999999999');

    $response->assertNotFound();
});

it('returns details for a public ticket marker id', function (): void {
    /** @var TestCase $this */
    $ticket = TicketFactory::new()->createOne([
        'name' => 'Ramo pericoloso su via Piovan',
        'content' => 'Ramo da mettere in sicurezza.',
        'status' => TicketStatusEnum::RESOLVED,
        'type' => TicketTypeEnum::PARKS_AND_GARDENS,
        'location' => [
            'lat' => 45.557,
            'lng' => 12.236,
            'address' => 'Via Piovan, Mogliano Veneto',
        ],
        'latitude' => 45.557,
        'longitude' => 12.236,
    ]);

    $ticketKey = $ticket->getKey();
    expect($ticketKey)->not->toBeNull();
    $url = '/api/ticket-details/'.SafeStringCastAction::cast($ticketKey);
    $response = $this->getJson($url);

    $response->assertOk()
        ->assertJsonPath('id', $ticket->getKey())
        ->assertJsonPath('title', 'Ramo pericoloso su via Piovan')
        ->assertJsonPath('code', '');
});

it('does not expose the capability code to an authenticated non-owner', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $viewer = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'status' => TicketStatusEnum::RESOLVED,
        'code' => 'TCK-PRIVATECODE01',
        'owner_id' => $owner->getKey(),
        'responsible_id' => $owner->getKey(),
        'location' => ['lat' => 45.557, 'lng' => 12.236],
    ]);

    $this->actingAs($viewer)
        ->getJson('/api/ticket-details/'.SafeStringCastAction::cast($ticket->getKey()))
        ->assertOk()
        ->assertJsonPath('code', '');
});

it('exposes the capability code to the ticket owner only', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'name' => 'Lampione guasto',
        'status' => TicketStatusEnum::PENDING,
        'code' => 'TCK-OWNERSECRET01',
        'owner_id' => $owner->getKey(),
        'responsible_id' => $owner->getKey(),
        'location' => ['lat' => 45.557, 'lng' => 12.236],
    ]);

    $this->actingAs($owner)
        ->getJson('/api/ticket-details/'.SafeStringCastAction::cast($ticket->getKey()))
        ->assertOk()
        ->assertJsonPath('code', 'TCK-OWNERSECRET01');
});
