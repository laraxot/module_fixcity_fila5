<?php

declare(strict_types=1);

use Illuminate\Auth\Access\AuthorizationException;
use Modules\Fixcity\Actions\SetTicketSubscriptionAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('sets the authenticated citizen ticket subscription idempotently', function (): void {
    $citizen = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $citizen->getAuthIdentifier(),
    ]);
    $action = app(SetTicketSubscriptionAction::class);

    Assert::assertTrue($action->execute($ticket, $citizen, true));
    Assert::assertTrue($action->execute($ticket, $citizen, true));
    Assert::assertSame(1, $ticket->ticketSubscribers()->count());

    Assert::assertFalse($action->execute($ticket, $citizen, false));
    Assert::assertFalse($action->execute($ticket, $citizen, false));
    Assert::assertSame(0, $ticket->ticketSubscribers()->count());
});

it('does not allow following a private ticket owned by another citizen', function (): void {
    $citizen = UserFactory::new()->createOne();
    $otherCitizen = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $otherCitizen->getAuthIdentifier(),
    ]);

    expect(fn (): bool => app(SetTicketSubscriptionAction::class)->execute($ticket, $citizen, true))
        ->toThrow(AuthorizationException::class);
});
