<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('shows public tickets and only the authenticated owners private tickets', function (): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $otherCitizen = UserFactory::new()->createOne();

    $ownedPrivate = TicketFactory::new()->createOne([
        'owner_id' => $citizen->getKey(),
        'status' => TicketStatusEnum::PENDING,
        'location' => ['lat' => 45.0, 'lng' => 9.0],
    ]);
    TicketFactory::new()->createOne([
        'owner_id' => $otherCitizen->getKey(),
        'status' => TicketStatusEnum::PENDING,
        'location' => ['lat' => 45.1, 'lng' => 9.1],
    ]);
    $publicTicket = TicketFactory::new()->createOne([
        'owner_id' => $otherCitizen->getKey(),
        'status' => TicketStatusEnum::IN_PROGRESS,
        'location' => ['lat' => 45.2, 'lng' => 9.2],
    ]);

    $this->actingAs($citizen);

    $ids = app(BuildPublicTicketsQueryAction::class)->execute()->pluck('id')->all();

    expect($ids)->toEqualCanonicalizing([$ownedPrivate->getKey(), $publicTicket->getKey()]);
});

it('shows only public statuses to a guest', function (): void {
    /** @var TestCase $this */
    TicketFactory::new()->createOne([
        'status' => TicketStatusEnum::PENDING,
        'location' => ['lat' => 45.0, 'lng' => 9.0],
    ]);
    $publicTicket = TicketFactory::new()->createOne([
        'status' => TicketStatusEnum::IN_PROGRESS,
        'location' => ['lat' => 45.2, 'lng' => 9.2],
    ]);

    Auth::logout();

    $ids = app(BuildPublicTicketsQueryAction::class)->execute()->pluck('id')->all();

    expect($ids)->toBe([$publicTicket->getKey()]);
});
