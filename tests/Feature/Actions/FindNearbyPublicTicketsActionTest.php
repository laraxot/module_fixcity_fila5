<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\FindNearbyPublicTicketsAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/** @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md */
it('suggests only nearby public reports of the same type', function (): void {
    $near = TicketFactory::new()->createOne([
        'type' => TicketTypeEnum::REPORT,
        'status' => TicketStatusEnum::OPEN,
        'latitude' => '45.500000',
        'longitude' => '12.250000',
        'location' => ['latitude' => 45.5, 'longitude' => 12.25, 'address' => 'Via A'],
    ]);
    $far = TicketFactory::new()->createOne([
        'type' => TicketTypeEnum::REPORT,
        'status' => TicketStatusEnum::OPEN,
        'latitude' => '45.502000',
        'longitude' => '12.250000',
        'location' => ['latitude' => 45.502, 'longitude' => 12.25, 'address' => 'Via B'],
    ]);
    $private = TicketFactory::new()->createOne([
        'type' => TicketTypeEnum::REPORT,
        'status' => TicketStatusEnum::PENDING,
        'latitude' => '45.500000',
        'longitude' => '12.250000',
        'location' => ['latitude' => 45.5, 'longitude' => 12.25, 'address' => 'Via C'],
    ]);
    $otherType = TicketFactory::new()->createOne([
        'type' => TicketTypeEnum::COMPLAINT,
        'status' => TicketStatusEnum::OPEN,
        'latitude' => '45.500000',
        'longitude' => '12.250000',
        'location' => ['latitude' => 45.5, 'longitude' => 12.25, 'address' => 'Via D'],
    ]);

    $results = app(FindNearbyPublicTicketsAction::class)->execute(TicketTypeEnum::REPORT->value, 45.5, 12.25);

    Assert::assertSame([$near->getKey()], $results->modelKeys());
    Assert::assertNotContains($far->getKey(), $results->modelKeys());
    Assert::assertNotContains($private->getKey(), $results->modelKeys());
    Assert::assertNotContains($otherType->getKey(), $results->modelKeys());
});

/** @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md */
it('returns no matches when report coordinates or type are invalid', function (): void {
    $action = app(FindNearbyPublicTicketsAction::class);

    Assert::assertCount(0, $action->execute('invalid_type', 45.5, 12.25));
    Assert::assertCount(0, $action->execute(TicketTypeEnum::REPORT->value, 91, 12.25));
    Assert::assertCount(0, $action->execute(TicketTypeEnum::REPORT->value, 45.5, 181));
    Assert::assertCount(0, $action->execute(TicketTypeEnum::REPORT->value, INF, 12.25));
});
