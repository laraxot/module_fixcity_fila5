<?php

declare(strict_types=1);

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('links followed reports to tracking by capability code and never by database id', function (): void {
    $citizen = UserFactory::new()->createOne();
    $this->actingAs($citizen);

    $ticket = TicketFactory::new()->createOne([
        'code' => 'TCK-FOLLOWED123456',
        'status' => TicketStatusEnum::IN_PROGRESS,
    ]);
    $ticket->ticketSubscribers()->attach($citizen->getAuthIdentifier());

    $this->get('/it/area-personale/seguite')
        ->assertOk()
        ->assertSee('/it/tickets/track?code=TCK-FOLLOWED123456', false)
        ->assertDontSee('/tickets/track?ticket_id='.$ticket->id, false);
});

it('shows an honest tracking fallback when a legacy ticket has no code', function (): void {
    $citizen = UserFactory::new()->createOne();
    $this->actingAs($citizen);

    $ticket = TicketFactory::new()->createOne([
        'code' => null,
        'status' => TicketStatusEnum::IN_PROGRESS,
    ]);
    $ticket->ticketSubscribers()->attach($citizen->getAuthIdentifier());

    $this->get('/it/area-personale/seguite')
        ->assertOk()
        ->assertSee('Il codice di tracciamento non è disponibile per questa segnalazione.')
        ->assertDontSee('/tickets/track?code=');
});

it('limits public ticket tracking lookups to ten requests per minute', function (): void {
    for ($request = 0; $request < 10; $request++) {
        $this->get('/it/tickets/track')->assertOk();
    }

    $this->get('/it/tickets/track')->assertTooManyRequests();
});
