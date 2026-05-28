<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\ResolveTicketTypeMarkerPropertiesAction;
use Tests\TestCase;

uses(TestCase::class);

use Modules\Fixcity\Enums\TicketTypeEnum;
it('resolves heroicon marker properties for road maintenance', function (): void {
    $props = app(ResolveTicketTypeMarkerPropertiesAction::class)
        ->execute(TicketTypeEnum::ROAD_MAINTENANCE);

    expect($props['value'])->toBe('road_maintenance')
        ->and($props['icon'])->toContain('heroicon-o-')
        ->and($props['color'])->toBe('#ff9800')
        ->and($props['label'])->toBeString()
        ->not->toBeEmpty()
        ->and($props['iconUrl'])->toBeString()
        ->not->toBeEmpty();
});

it('returns safe defaults for unknown type value', function (): void {
    $props = app(ResolveTicketTypeMarkerPropertiesAction::class)
        ->executeFromValue('not_a_real_type');

    expect($props['value'])->toBe('not_a_real_type')
        ->and($props['iconUrl'])->toBeNull();
});
