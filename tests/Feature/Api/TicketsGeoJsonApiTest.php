<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Api;

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
