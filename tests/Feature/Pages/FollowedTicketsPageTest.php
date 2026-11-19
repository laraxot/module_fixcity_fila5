<?php

declare(strict_types=1);

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('gives a follower the detail link of the report and never its tracking code', function (): void {
    $citizen = UserFactory::new()->createOne();
    $this->actingAs($citizen);

    // il proprietario e' un altro utente: il codice e' una capability e non e' di chi segue
    $ticket = TicketFactory::new()->createOne([
        'code' => 'TCK-FOLLOWED123456',
        'status' => TicketStatusEnum::IN_PROGRESS,
    ]);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $citizen->getAuthIdentifier()]);

    $this->get('/it/area-personale/seguite')
        ->assertOk()
        ->assertSee('/it/tickets/'.$ticket->id, false)
        ->assertDontSee('TCK-FOLLOWED123456')
        ->assertDontSee('/tickets/track?code=', false)
        ->assertDontSee('/tickets/track/TCK', false)
        ->assertDontSee('Il codice di tracciamento non è disponibile');
});

it('keeps the tracking link for the owner who follows the own report, by capability code and never by database id', function (): void {
    $citizen = UserFactory::new()->createOne();
    $this->actingAs($citizen);

    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $citizen->getKey(),
        'code' => 'TCK-OWNFOLLOW1234',
        'status' => TicketStatusEnum::IN_PROGRESS,
    ]);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $citizen->getAuthIdentifier()]);

    $this->get('/it/area-personale/seguite')
        ->assertOk()
        ->assertSee('TCK-OWNFOLLOW1234')
        ->assertSee('/it/tickets/track?code=TCK-OWNFOLLOW1234', false)
        ->assertDontSee('/tickets/track?ticket_id='.$ticket->id, false);
});

it('shows the honest tracking fallback only to the owner of a legacy ticket without code', function (): void {
    $citizen = UserFactory::new()->createOne();
    $this->actingAs($citizen);

    $own = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'code' => null, 'status' => TicketStatusEnum::IN_PROGRESS]);
    TicketSubscriber::query()->create(['ticket_id' => $own->getKey(), 'user_id' => $citizen->getAuthIdentifier()]);

    $this->get('/it/area-personale/seguite')
        ->assertOk()
        ->assertSee('Il codice di tracciamento non è disponibile per questa segnalazione.')
        ->assertDontSee('/tickets/track?code=', false);
});

it('does not announce a missing tracking code to a follower of a legacy ticket of others', function (): void {
    $citizen = UserFactory::new()->createOne();
    $this->actingAs($citizen);

    $ticket = TicketFactory::new()->createOne([
        'code' => null,
        'status' => TicketStatusEnum::IN_PROGRESS,
    ]);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $citizen->getAuthIdentifier()]);

    $this->get('/it/area-personale/seguite')
        ->assertOk()
        ->assertSee('/it/tickets/'.$ticket->id, false)
        ->assertDontSee('Il codice di tracciamento non è disponibile')
        ->assertDontSee('/tickets/track?code=', false);
});

it('limits public ticket tracking lookups to ten requests per minute', function (): void {
    for ($request = 0; $request < 10; $request++) {
        $this->get('/it/tickets/track')->assertOk();
    }

    $this->get('/it/tickets/track')->assertTooManyRequests();
});
