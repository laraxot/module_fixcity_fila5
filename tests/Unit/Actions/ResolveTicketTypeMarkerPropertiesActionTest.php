<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\ResolveTicketTypeMarkerPropertiesAction;
use Tests\TestCase;

uses(TestCase::class);

use Modules\Fixcity\Enums\TicketTypeEnum;
it('resolves canonical fixcity svg for road maintenance', function (): void {
    $props = app(ResolveTicketTypeMarkerPropertiesAction::class)
        ->execute(TicketTypeEnum::ROAD_MAINTENANCE);

    expect($props['value'])->toBe('road_maintenance')
        ->and($props['label'])->toBeString()
        ->not->toBeEmpty()
        ->and($props)->not->toHaveKey('icon')
        ->and($props['iconUrl'])->toBeString()
        ->toContain('/assets/fixcity/svg/road-maintenance.svg')
        ->not->toContain('/assets/ui/svg/');
});

it('returns safe defaults for unknown type value', function (): void {
    $props = app(ResolveTicketTypeMarkerPropertiesAction::class)
        ->executeFromValue('not_a_real_type');

    expect($props['value'])->toBe('not_a_real_type')
        ->and($props['iconUrl'])->toBeString()
        ->toContain('/assets/fixcity/svg/');
});
