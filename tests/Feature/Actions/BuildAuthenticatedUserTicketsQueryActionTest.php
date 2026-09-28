<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildAuthenticatedUserTicketsQueryAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('returns tickets owned by the authenticated citizen and excludes other citizens tickets', function (): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $otherCitizen = UserFactory::new()->createOne();

    $ownedTicket = TicketFactory::new()->createOne([
        'owner_id' => $citizen->getKey(),
        'created_by' => 'system',
    ]);
    TicketFactory::new()->createOne([
        'owner_id' => $otherCitizen->getKey(),
        'created_by' => 'system',
    ]);

    $this->actingAs($citizen);

    $tickets = app(BuildAuthenticatedUserTicketsQueryAction::class)->execute()->get();

    expect($tickets->modelKeys())->toBe([$ownedTicket->getKey()]);
});
