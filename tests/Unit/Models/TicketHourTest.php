<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Modules\Fixcity\Database\Factories\TicketHourFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketHour;
use Modules\User\Models\User;

use PHPUnit\Framework\Assert;

describe('TicketHour Model', function () {
    it('can be created with valid data', function () {
        $user = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne();

        $hour = TicketHour::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'value' => 2.5,
            'comment' => 'Work performed on ticket',
        ]);

        Assert::assertInstanceOf(TicketHour::class, $hour);
        Assert::assertSame($ticket->id, $hour->ticket_id);
        Assert::assertSame($user->id, $hour->user_id);
        Assert::assertSame(2.5, $hour->value);
        Assert::assertSame('Work performed on ticket', $hour->comment);
    });

    it('belongs to a ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        $hour = TicketHourFactory::new()->createOne([
            'ticket_id' => $ticket->id,
        ]);

        Assert::assertInstanceOf(Ticket::class, $hour->ticket);
        Assert::assertSame($ticket->id, $hour->ticket->id);
    });

    it('belongs to a user', function () {
        $user = UserFactory::new()->createOne();
        $hour = TicketHourFactory::new()->createOne([
            'user_id' => $user->id,
        ]);

        Assert::assertInstanceOf(User::class, $hour->user);
        Assert::assertSame($user->id, $hour->user->id);
    });

    it('can store decimal hour values', function () {
        $hour = TicketHourFactory::new()->createOne([
            'value' => 1.75,
        ]);

        Assert::assertSame(1.75, $hour->value);
    });

    it('can store whole hour values', function () {
        $hour = TicketHourFactory::new()->createOne([
            'value' => 3,
        ]);

        Assert::assertSame(3.0, $hour->value);
    });

    it('can store fractional hour values', function () {
        $hour = TicketHourFactory::new()->createOne([
            'value' => 0.25,
        ]);

        Assert::assertSame(0.25, $hour->value);
    });

    it('tracks creation timestamp', function () {
        $hour = TicketHourFactory::new()->createOne();

        Assert::assertNotNull($hour->created_at);
    });

    it('can store description of work', function () {
        $description = 'Analyzed the issue, identified root cause, and implemented fix';
        $hour = TicketHourFactory::new()->createOne([
            'comment' => $description,
        ]);

        Assert::assertSame($description, $hour->comment);
    });

    it('can be queried by ticket', function () {
        $ticket = TicketFactory::new()->createOne();
        TicketHourFactory::new()->count(3)->create([
            'ticket_id' => $ticket->id,
        ]);

        $ticketHours = TicketHour::where('ticket_id', $ticket->id)->get();

        Assert::assertCount(3, $ticketHours);
        foreach ($ticketHours as $hour) {
            Assert::assertSame($ticket->id, $hour->ticket_id);
        }
    });

    it('can be queried by user', function () {
        $user = UserFactory::new()->createOne();
        TicketHourFactory::new()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $userHours = TicketHour::where('user_id', $user->id)->get();

        Assert::assertCount(3, $userHours);
        foreach ($userHours as $hour) {
            Assert::assertSame($user->id, $hour->user_id);
        }
    });

    it('can calculate total hours for a ticket', function () {
        $ticket = TicketFactory::new()->createOne();

        TicketHourFactory::new()->createOne([
            'ticket_id' => $ticket->id,
            'value' => 2.5,
        ]);

        TicketHourFactory::new()->createOne([
            'ticket_id' => $ticket->id,
            'value' => 1.75,
        ]);

        TicketHourFactory::new()->createOne([
            'ticket_id' => $ticket->id,
            'value' => 3.0,
        ]);

        $totalHours = TicketHour::where('ticket_id', $ticket->id)->sum('value');

        Assert::assertSame(7.25, $totalHours);
    });

    it('can calculate total hours for a user', function () {
        $user = UserFactory::new()->createOne();

        TicketHourFactory::new()->createOne([
            'user_id' => $user->id,
            'value' => 4.0,
        ]);

        TicketHourFactory::new()->createOne([
            'user_id' => $user->id,
            'value' => 2.5,
        ]);

        $totalHours = TicketHour::where('user_id', $user->id)->sum('value');

        Assert::assertSame(6.5, $totalHours);
    });

    it('can be ordered by creation date', function () {
        $oldHour = TicketHourFactory::new()->createOne([
            'created_at' => now()->subDays(3),
        ]);

        $newHour = TicketHourFactory::new()->createOne([
            'created_at' => now(),
        ]);

        $orderedHours = TicketHour::orderBy('created_at', 'desc')->get();
        $first = $orderedHours->first();
        $last = $orderedHours->last();

        Assert::assertNotNull($first);
        Assert::assertNotNull($last);
        Assert::assertSame($newHour->id, $first->id);
        Assert::assertSame($oldHour->id, $last->id);
    });

    it('can be filtered by minimum hour value', function () {
        TicketHourFactory::new()->createOne(['value' => 0.5]);
        TicketHourFactory::new()->createOne(['value' => 2.0]);
        TicketHourFactory::new()->createOne(['value' => 4.5]);

        $significantHours = TicketHour::where('value', '>=', 2.0)->get();

        Assert::assertCount(2, $significantHours);
    });

    it('can be deleted', function () {
        $hour = TicketHourFactory::new()->createOne();
        $hourId = $hour->id;
        $hour->delete();

        Assert::assertNull(TicketHour::find($hourId));
    });

    it('can handle zero hour values', function () {
        $hour = TicketHourFactory::new()->createOne([
            'value' => 0.0,
        ]);

        Assert::assertSame(0.0, $hour->value);
    });
});
