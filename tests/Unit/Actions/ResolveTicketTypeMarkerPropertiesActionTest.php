<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\ResolveTicketTypeMarkerPropertiesAction;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('resolves canonical fixcity svg for road maintenance', function (): void {
    $props = app(ResolveTicketTypeMarkerPropertiesAction::class)
        ->execute(TicketTypeEnum::ROAD_MAINTENANCE);

    Assert::assertSame('road_maintenance', $props['value']);
    Assert::assertIsString($props['label']);
    Assert::assertNotEmpty($props['label']);
    Assert::assertArrayNotHasKey('icon', $props);
    Assert::assertIsString($props['iconUrl']);
    Assert::assertStringContainsString('/assets/fixcity/svg/road-maintenance.svg', $props['iconUrl']);
    Assert::assertStringNotContainsString('/assets/ui/svg/', $props['iconUrl']);
});

it('returns safe defaults for unknown type value', function (): void {
    $props = app(ResolveTicketTypeMarkerPropertiesAction::class)
        ->executeFromValue('not_a_real_type');

    Assert::assertSame('not_a_real_type', $props['value']);
    Assert::assertIsString($props['iconUrl']);
    Assert::assertStringContainsString('/assets/fixcity/svg/', $props['iconUrl']);
});
