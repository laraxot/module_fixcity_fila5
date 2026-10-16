<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use InvalidArgumentException;
use Modules\Fixcity\Actions\ValidateTicketsGeoJsonAction;

it('accepts a RFC 7946 point feature collection with longitude first', function (): void {
    $payload = [
        'type' => 'FeatureCollection',
        'features' => [[
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [12.249756, 45.562246]],
            'properties' => ['id' => 'DEMO-001'],
        ]],
    ];

    expect(app(ValidateTicketsGeoJsonAction::class)->execute($payload))->toBe($payload);
});

it('rejects invalid or out of range ticket coordinates', function (): void {
    $payload = [
        'type' => 'FeatureCollection',
        'features' => [[
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [181.0, 45.0]],
            'properties' => [],
        ]],
    ];

    expect(fn (): array => app(ValidateTicketsGeoJsonAction::class)->execute($payload))
        ->toThrow(InvalidArgumentException::class);
});
