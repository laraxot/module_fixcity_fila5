<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Modules\Fixcity\Actions\GenerateTicketsJsonAction;
use Modules\Fixcity\Database\Seeders\DemoUsersSeeder;
use Modules\Fixcity\Database\Seeders\FixcityDatabaseSeeder;
use Modules\Fixcity\Database\Seeders\TicketDatabaseSeeder;
use Modules\Fixcity\Models\Category;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('assigns demo tickets to the declared citizen despite unrelated existing users', function (): void {
    if (! Schema::connection('fixcity')->hasTable('categories')
        || ! Schema::connection('fixcity')->hasTable('tickets')) {
        Assert::markTestSkipped('Fixcity migrations required on connection fixcity');
    }

    $unrelatedUser = UserFactory::new()->createOne([
        'id' => '00000000-0000-4000-8000-000000000001',
        'email' => 'seeder-fixcity-'.uniqid('', true).'@fixcity.test',
    ]);
    $unrelatedUserId = SafeStringCastAction::cast($unrelatedUser->getAuthIdentifier());

    Storage::fake('public');
    $geoJsonAction = \Mockery::mock(GenerateTicketsJsonAction::class);
    // TicketDatabaseSeeder and the final GeoJsonUpdateSeeder both regenerate
    // the static map export; the full orchestrator is intentionally idempotent.
    $geoJsonAction->shouldReceive('execute')->times(4)->andReturn('/tmp/fixcity-seeder-test-tickets.json');
    app()->instance(GenerateTicketsJsonAction::class, $geoJsonAction);

    Artisan::call('db:seed', ['--class' => DemoUsersSeeder::class, '--no-interaction' => true]);
    Artisan::call('db:seed', ['--class' => FixcityDatabaseSeeder::class, '--no-interaction' => true]);

    Assert::assertTrue(Category::query()->whereKey('strade')->exists());

    $demoTickets = Ticket::query()->where('code', 'like', 'DEMO-%')->count();
    Assert::assertGreaterThanOrEqual(20, $demoTickets);

    /** @var class-string<Model&UserContract> $userModel */
    $userModel = XotData::make()->getUserClass();
    $citizen = $userModel::query()->where('email', DemoUsersSeeder::CITIZEN_EMAIL)->first();
    Assert::assertInstanceOf(UserContract::class, $citizen);

    $firstDemoTicket = Ticket::query()->where('code', 'DEMO-001')->firstOrFail();
    $citizenId = SafeStringCastAction::cast($citizen->getAuthIdentifier());
    $firstDemoTicketOwnerId = SafeStringCastAction::cast($firstDemoTicket->owner_id);
    Assert::assertSame($citizenId, $firstDemoTicketOwnerId);
    Assert::assertNotSame($unrelatedUserId, $firstDemoTicketOwnerId);

    Artisan::call('db:seed', ['--class' => FixcityDatabaseSeeder::class, '--no-interaction' => true]);

    Assert::assertSame($demoTickets, Ticket::query()->where('code', 'like', 'DEMO-%')->count());
});

it('skips demo tickets when the declared citizen account is missing', function (): void {
    if (! Schema::connection('fixcity')->hasTable('tickets')) {
        Assert::markTestSkipped('Fixcity migrations required on connection fixcity');
    }

    /** @var class-string<Model&UserContract> $userModel */
    $userModel = XotData::make()->getUserClass();
    $userModel::query()->where('email', DemoUsersSeeder::CITIZEN_EMAIL)->delete();

    $existingDemoTickets = Ticket::query()->where('code', 'like', 'DEMO-%')->count();
    $geoJsonAction = \Mockery::mock(GenerateTicketsJsonAction::class);
    $geoJsonAction->shouldNotReceive('execute');
    app()->instance(GenerateTicketsJsonAction::class, $geoJsonAction);

    app(TicketDatabaseSeeder::class)->run();

    Assert::assertSame($existingDemoTickets, Ticket::query()->where('code', 'like', 'DEMO-%')->count());
});
