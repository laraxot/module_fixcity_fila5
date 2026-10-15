<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildSegnalazioniFilterAggregateAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('builds public filter aggregates from current ticket records', function (): void {
    /** @var TestCase $this */
    $ticket = TicketFactory::new()->createOne([
        'name' => 'Segnalazione live aggregato 2026',
        'type' => TicketTypeEnum::REPORT,
        'status' => TicketStatusEnum::IN_PROGRESS,
        'location' => ['lat' => 45.4642, 'lng' => 9.1900],
        'code' => 'TCK-AGGREGATE-SECRET',
    ]);

    $aggregate = app(BuildSegnalazioniFilterAggregateAction::class)->execute();
    $matchingFeatures = array_values(array_filter(
        $aggregate['features'],
        static fn (array $feature): bool => is_array($feature['properties'] ?? null)
            && ($feature['properties']['id'] ?? null) === $ticket->id,
    ));

    expect($aggregate['totalCount'])->toBeGreaterThan(0)
        ->and($matchingFeatures)->toHaveCount(1);

    $properties = $matchingFeatures[0]['properties'] ?? null;
    Assert::assertIsArray($properties);
    Assert::assertArrayNotHasKey('code', $properties);

    expect($aggregate['countsPerType'][TicketTypeEnum::REPORT->value])->toBeGreaterThan(0);
});
