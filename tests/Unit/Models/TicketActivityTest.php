<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Modules\Fixcity\Database\Factories\TicketActivityFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\User\Models\User;

use PHPUnit\Framework\Assert;

describe('TicketActivity Model', function () {
    it('can be created with valid data', function () {
        $user = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne();

        $activity = TicketActivity::create([
            'ticket_id' => $ticket->id,
            'old_status_id' => 1,
            'new_status_id' => 2,
            'user_id' => $user->id,
        ]);

        Assert::assertInstanceOf(TicketActivity::class, $activity);
        Assert::assertSame($ticket->id, $activity->ticket_id);
        Assert::assertSame(1, $activity->old_status_id);
        Assert::assertSame(2, $activity->new_status_id);
        Assert::assertSame($user->id, $activity->user_id);
    });

    it('belongs to a ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $activity = TicketActivityFactory::new()->createOne([
            'ticket_id' => $ticket->id,
        ]);

        Assert::assertInstanceOf(Ticket::class, $activity->ticket);
        Assert::assertSame($ticket->id, $activity->ticket->id);
    });

    it('belongs to a user', function () {
        $user = UserFactory::new()->createOne();
        $activity = TicketActivityFactory::new()->createOne([
            'user_id' => $user->id,
        ]);

        Assert::assertInstanceOf(User::class, $activity->user);
        Assert::assertSame($user->id, $activity->user->id);
    });

    it('tracks status changes correctly', function () {
        $activity = TicketActivityFactory::new()->createOne([
            'old_status_id' => 1,
            'new_status_id' => 2,
        ]);

        Assert::assertSame(1, $activity->old_status_id);
        Assert::assertSame(2, $activity->new_status_id);
        Assert::assertNotSame($activity->old_status_id, $activity->new_status_id);
    });

    it('can handle null old status for new tickets', function () {
        $activity = TicketActivityFactory::new()->createOne([
            'old_status_id' => null,
            'new_status_id' => 1,
        ]);

        Assert::assertNull($activity->old_status_id);
        Assert::assertSame(1, $activity->new_status_id);
    });

    it('can handle null new status for deleted tickets', function () {
        $activity = TicketActivityFactory::new()->createOne([
            'old_status_id' => 1,
            'new_status_id' => null,
        ]);

        Assert::assertSame(1, $activity->old_status_id);
        Assert::assertNull($activity->new_status_id);
    });

    it('stores old and new status ids for audit trail', function () {
        $activity = TicketActivityFactory::new()->createOne([
            'old_status_id' => 1,
            'new_status_id' => 2,
        ]);

        Assert::assertSame(1, $activity->old_status_id);
        Assert::assertSame(2, $activity->new_status_id);
    });

    it('can be queried by ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        TicketActivityFactory::new()->count(3)->create([
            'ticket_id' => $ticket->id,
        ]);

        $ticketActivities = TicketActivity::where('ticket_id', $ticket->id)->get();

        Assert::assertCount(3, $ticketActivities);
        foreach ($ticketActivities as $activity) {
            Assert::assertSame($ticket->id, $activity->ticket_id);
        }
    });

    it('can be queried by user', function () {
        $user = UserFactory::new()->createOne();
        TicketActivityFactory::new()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $userActivities = TicketActivity::where('user_id', $user->id)->get();

        Assert::assertCount(3, $userActivities);
        foreach ($userActivities as $activity) {
            Assert::assertSame($user->id, $activity->user_id);
        }
    });

    it('can be queried by date range', function () {
        $activity = TicketActivityFactory::new()->createOne();

        $recentActivities = TicketActivity::where('created_at', '>=', now()->subDays(7))->get();

        Assert::assertContains($activity, $recentActivities);
    });

    it('can be deleted', function () {
        $activity = TicketActivityFactory::new()->createOne();
        $activityId = $activity->id;
        $activity->delete();

        Assert::assertNull(TicketActivity::find($activityId));
    });
});
