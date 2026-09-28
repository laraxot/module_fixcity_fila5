<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildAuthenticatedUserFollowedTicketsQueryAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('returns only tickets followed by the requested citizen in newest-first order', function (): void {
    $citizen = UserFactory::new()->createOne();
    $otherCitizen = UserFactory::new()->createOne();
    $olderFollowedTicket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);
    $newerFollowedTicket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::RESOLVED]);
    $privateFollowedTicket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);
    $ownedPrivateFollowedTicket = TicketFactory::new()->createOne([
        'owner_id' => $citizen->getAuthIdentifier(),
        'status' => TicketStatusEnum::PENDING,
    ]);
    $notFollowedTicket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

    $olderFollowedTicket->ticketSubscribers()->attach($citizen->getAuthIdentifier());
    $newerFollowedTicket->ticketSubscribers()->attach($citizen->getAuthIdentifier());
    $privateFollowedTicket->ticketSubscribers()->attach($citizen->getAuthIdentifier());
    $ownedPrivateFollowedTicket->ticketSubscribers()->attach($citizen->getAuthIdentifier());
    $notFollowedTicket->ticketSubscribers()->attach($otherCitizen->getAuthIdentifier());

    $tickets = app(BuildAuthenticatedUserFollowedTicketsQueryAction::class)
        ->execute($citizen)
        ->get();

    Assert::assertSame(
        [
            $ownedPrivateFollowedTicket->getKey(),
            $newerFollowedTicket->getKey(),
            $olderFollowedTicket->getKey(),
        ],
        $tickets->modelKeys(),
    );
    Assert::assertNotContains($notFollowedTicket->getKey(), $tickets->modelKeys());
    Assert::assertNotContains($privateFollowedTicket->getKey(), $tickets->modelKeys());
});
