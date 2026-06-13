<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Database;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Database\Seeders\DatabaseSeeder as FixcityModuleDatabaseSeeder;
use Modules\Fixcity\Models\Category;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use PHPUnit\Framework\Assert;

uses(\Modules\Fixcity\Tests\TestCase::class);

it('populates categories and demo tickets idempotently when a user exists', function (): void {
    if (! Schema::connection('fixcity')->hasTable('categories')
        || ! Schema::connection('fixcity')->hasTable('tickets')) {
        Assert::markTestSkipped('Fixcity migrations required on connection fixcity');
    }

    if (! User::query()->exists()) {
        UserFactory::new()->createOne([
            'email' => 'seeder-fixcity-'.uniqid('', true).'@fixcity.test',
        ]);
    }

    Artisan::call('db:seed', ['--class' => FixcityModuleDatabaseSeeder::class, '--no-interaction' => true]);

    Assert::assertTrue(Category::query()->whereKey('strade')->exists());

    $demoTickets = Ticket::query()->where('code', 'like', 'DEMO-%')->count();
    Assert::assertGreaterThanOrEqual(20, $demoTickets);

    Artisan::call('db:seed', ['--class' => FixcityModuleDatabaseSeeder::class, '--no-interaction' => true]);

    Assert::assertSame($demoTickets, Ticket::query()->where('code', 'like', 'DEMO-%')->count());
});
