<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildPublicNeighborhoodStatsAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/** @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md */
it('aggregates only recent publicly visible reports by neighborhood', function (): void {
    $location = ['latitude' => 45.5, 'longitude' => 12.25, 'suburb' => 'Centro', 'city' => 'Comune', 'address' => 'Via Roma'];

    TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN, 'location' => $location]);
    TicketFactory::new()->createOne(['status' => TicketStatusEnum::RESOLVED, 'location' => $location]);
    TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING, 'location' => $location]);
    TicketFactory::new()->createOne([
        'status' => TicketStatusEnum::OPEN,
        'location' => $location,
        'created_at' => now()->subDays(60),
    ]);

    $stats = app(BuildPublicNeighborhoodStatsAction::class)->execute(30);

    Assert::assertSame(30, $stats['period_days']);
    Assert::assertSame(2, $stats['total_reports']);
    Assert::assertSame(0, $stats['without_area']);
    Assert::assertSame('Centro', $stats['neighborhoods'][0]['name']);
    Assert::assertSame(2, $stats['neighborhoods'][0]['reports']);
    Assert::assertSame(1, $stats['neighborhoods'][0]['active']);
    Assert::assertSame(1, $stats['neighborhoods'][0]['resolved']);
});

/** @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md */
it('returns a public, throttled JSON summary without ticket identifiers', function (): void {
    TicketFactory::new()->createOne([
        'status' => TicketStatusEnum::OPEN,
        'name' => 'Nome non divulgabile nel report',
        'location' => ['latitude' => 45.5, 'longitude' => 12.25, 'city' => 'Comune'],
    ]);

    $response = $this->get('/api/tickets/neighborhood-stats?days=7')->assertOk()
        ->assertJsonPath('period_days', 7)
        ->assertJsonPath('total_reports', 1);

    $json = $response->getContent();
    Assert::assertIsString($json);
    Assert::assertStringNotContainsString('Nome non divulgabile', $json);
    Assert::assertStringNotContainsString('owner_id', $json);
});
