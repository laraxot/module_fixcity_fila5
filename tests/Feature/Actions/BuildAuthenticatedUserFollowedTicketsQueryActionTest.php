<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildAuthenticatedUserFollowedTicketsQueryAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketSubscriber;
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

    foreach ([$olderFollowedTicket, $newerFollowedTicket, $privateFollowedTicket, $ownedPrivateFollowedTicket] as $followedTicket) {
        TicketSubscriber::query()->create(['ticket_id' => $followedTicket->getKey(), 'user_id' => $citizen->getAuthIdentifier()]);
    }
    TicketSubscriber::query()->create(['ticket_id' => $notFollowedTicket->getKey(), 'user_id' => $otherCitizen->getAuthIdentifier()]);

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
