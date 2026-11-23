<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildSegnalazioniFilterAggregateAction;
use Modules\Fixcity\Actions\LoadPublicTicketsGeoJsonAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('builds public filter aggregates from the GeoJSON source used by the map', function (): void {
    /** @var TestCase $this */
    TicketFactory::new()->createOne([
        'name' => 'Segnalazione live aggregato 2026',
        'type' => TicketTypeEnum::REPORT,
        'status' => TicketStatusEnum::IN_PROGRESS,
        'location' => ['lat' => 45.4642, 'lng' => 9.1900],
        'code' => 'TCK-AGGREGATE-SECRET',
    ]);

    $source = app(LoadPublicTicketsGeoJsonAction::class)->execute();
    $aggregate = app(BuildSegnalazioniFilterAggregateAction::class)->execute();
    $sourceTotal = isset($source['total']) ? $source['total'] : count($source['features']);

    expect($aggregate['features'])->toBe($source['features'])
        ->and($aggregate['totalCount'])->toBe($sourceTotal);

    foreach ($aggregate['features'] as $feature) {
        $properties = $feature['properties'] ?? [];
        Assert::assertIsArray($properties);
        Assert::assertArrayNotHasKey('code', $properties);
    }
});
