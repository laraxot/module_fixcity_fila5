<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Api;

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;

uses(\Modules\Fixcity\Tests\TestCase::class);

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

    $response = $this->getJson('/api/ticket-details/'.$ticket->getKey());

    $response->assertOk()
        ->assertJsonPath('id', $ticket->getKey())
        ->assertJsonPath('title', 'Ramo pericoloso su via Piovan');
});
