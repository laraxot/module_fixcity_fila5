<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Database;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Modules\Fixcity\Database\Seeders\DemoOperatorPanelAccessSeeder;
use Modules\Fixcity\Database\Seeders\DemoUsersSeeder;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

beforeEach(function (): void {
    (new DemoUsersSeeder)->run();
});

it('grants only the intended panel and operator roles to the demo PA operator', function (): void {
    (new DemoOperatorPanelAccessSeeder)->run();

    $userClass = XotData::make()->getUserClass();
    $operator = $userClass::query()->where('email', 'operatore@fixcity.demo')->first();
    $citizen = $userClass::query()->where('email', DemoUsersSeeder::CITIZEN_EMAIL)->first();
    $panel = Filament::getPanel('fixcity::admin');

    Assert::assertInstanceOf(UserContract::class, $operator);
    Assert::assertInstanceOf(UserContract::class, $citizen);
    Assert::assertInstanceOf(FilamentUser::class, $operator);
    Assert::assertInstanceOf(FilamentUser::class, $citizen);
    Assert::assertInstanceOf(Panel::class, $panel);

    expect($operator->hasRole('operator'))->toBeTrue()
        ->and($operator->hasRole('fixcity::admin'))->toBeTrue()
        ->and($operator->hasRole('admin'))->toBeFalse()
        ->and($operator->hasRole('super-admin'))->toBeFalse()
        ->and($operator->canAccessPanel($panel))->toBeTrue()
        ->and($citizen->hasRole('operator'))->toBeFalse()
        ->and($citizen->hasRole('fixcity::admin'))->toBeFalse()
        ->and($citizen->canAccessPanel($panel))->toBeFalse();
});

it('keeps the demo operator role assignments idempotent', function (): void {
    $seeder = new DemoOperatorPanelAccessSeeder;
    $seeder->run();
    $seeder->run();

    $userClass = XotData::make()->getUserClass();
    $operator = $userClass::query()->where('email', 'operatore@fixcity.demo')->first();

    Assert::assertInstanceOf(UserContract::class, $operator);

    expect($operator->roles()->where('name', 'operator')->count())->toBe(1)
        ->and($operator->roles()->where('name', 'fixcity::admin')->count())->toBe(1);
});
