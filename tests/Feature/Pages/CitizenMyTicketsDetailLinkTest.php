<?php

declare(strict_types=1);

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

test('guest is redirected from personal practices to the localized login', function (): void {
    /** @var TestCase $this */
    $this->get('/it/area-personale/pratiche')
        ->assertRedirect('/it/auth/login');
});

test('citizen can open owned private ticket details without exposing another citizen ticket', function (): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $otherCitizen = UserFactory::new()->createOne();

    $ownedTicket = TicketFactory::new()->createOne([
        'name' => 'Private practice owned by current citizen',
        'owner_id' => $citizen->getKey(),
        'status' => TicketStatusEnum::PENDING,
        'code' => 'TCK-OWNER-PRIVATE-518',
        'location' => ['lat' => 45.0, 'lng' => 9.0],
    ]);
    $otherTicket = TicketFactory::new()->createOne([
        'name' => 'Private practice owned by another citizen',
        'owner_id' => $otherCitizen->getKey(),
        'status' => TicketStatusEnum::PENDING,
        'code' => 'TCK-OTHER-PRIVATE-518',
        'location' => ['lat' => 45.1, 'lng' => 9.1],
    ]);

    $this->get('/it/tickets/'.$ownedTicket->id)
        ->assertNotFound();

    $this->actingAs($citizen)
        ->get('/it/area-personale/pratiche')
        ->assertOk()
        ->assertSee('Nuova segnalazione')
        ->assertDontSee('>create<', false)
        ->assertSee('Private practice owned by current citizen')
        ->assertSee('/it/tickets/'.$ownedTicket->id, false)
        ->assertSee('/it/tickets/track/TCK-OWNER-PRIVATE-518', false)
        ->assertDontSee('Private practice owned by another citizen')
        ->assertDontSee('TCK-OTHER-PRIVATE-518');

    $this->get('/it/tickets/'.$ownedTicket->id)
        ->assertOk()
        ->assertSee('Private practice owned by current citizen');

    $this->get('/it/tickets/'.$otherTicket->id)
        ->assertNotFound();
});
