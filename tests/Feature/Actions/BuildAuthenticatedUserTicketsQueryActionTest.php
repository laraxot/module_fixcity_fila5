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

it('scopes the query to the explicit user, whoever is signed in', function (): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $other = UserFactory::new()->createOne();
    $owned = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey()]);
    TicketFactory::new()->createOne(['owner_id' => $other->getKey()]);

    $this->actingAs($other);

    expect(app(BuildAuthenticatedUserTicketsQueryAction::class)->execute($citizen)->pluck('id')->all())->toBe([$owned->getKey()]);
});

it('returns nothing when nobody is signed in and no user is given', function (): void {
    /** @var TestCase $this */
    TicketFactory::new()->createOne();

    expect(app(BuildAuthenticatedUserTicketsQueryAction::class)->execute()->get())->toBeEmpty();
});
