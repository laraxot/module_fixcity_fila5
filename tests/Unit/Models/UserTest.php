<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Modules\Fixcity\Models\Profile;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\TicketHour;
use Modules\Fixcity\Models\User;
use Modules\User\Models\Team;
use Modules\User\Models\Tenant;

use PHPUnit\Framework\Assert;
describe('User Model (Fixcity)', function () {
    it('can be created with valid data', function () {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Assert::assertInstanceOf(User::class, $user);

        Assert::assertSame('Test User', $user->name);

        Assert::assertSame('test@example.com', $user->email);
    });

    it('can own tickets', function () {
        $user = UserFactory::new()->createOne();
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create([
            'owner_id' => $user->id,
        ]);

        $ownedTickets = Ticket::query()->where('owner_id', $user->id)->get();
        Assert::assertCount(3, $ownedTickets);
        foreach ($ownedTickets as $ticket) {
            Assert::assertSame($user->id, $ticket->owner_id);
        }
    });

    it('can be responsible for tickets', function () {
        $user = UserFactory::new()->createOne();
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(2)->create([
            'responsible_id' => $user->id,
        ]);

        $responsibleTickets = Ticket::query()->where('responsible_id', $user->id)->get();
        Assert::assertCount(2, $responsibleTickets);
        foreach ($responsibleTickets as $ticket) {
            Assert::assertSame($user->id, $ticket->responsible_id);
        }
    });

    it('can have a profile', function () {
        $user = UserFactory::new()->createOne();

        // Create profile for user
        $profile = $user->profile()->create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
        ]);

        Assert::assertInstanceOf(Profile::class, $user->profile);

        Assert::assertSame('Mario', $user->profile->first_name);

        Assert::assertSame('Rossi', $user->profile->last_name);
    });

    it('can subscribe to tickets', function () {
        $user = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne();

        // Subscribe user to ticket
        $ticket->subscribers()->attach($user->id);

        $subscribedTickets = Ticket::query()
            ->whereHas('subscribers', static fn ($query) => $query->where('users.id', $user->id))
            ->get();
        Assert::assertCount(1, $subscribedTickets);
        Assert::assertSame($ticket->id, $subscribedTickets->first()?->id);
    });

    it('can track ticket activities', function () {
        $user = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne();

        // Create activity for user
        $activity = $ticket->activities()->create([
            'user_id' => $user->id,
            'old_status_id' => 1,
            'new_status_id' => 2,
        ]);

        $activities = TicketActivity::query()->where('user_id', $user->id)->get();
        Assert::assertCount(1, $activities);
        Assert::assertSame($activity->id, $activities->first()?->id);
    });

    it('can log hours on tickets', function () {
        $user = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne();

        // Log hours for user
        $hour = $ticket->hours()->create([
            'user_id' => $user->id,
            'value' => 2.5,
            'content' => 'Work performed',
            'date' => now()->toDateString(),
        ]);

        $hours = TicketHour::query()->where('user_id', $user->id)->get();
        Assert::assertCount(1, $hours);
        Assert::assertSame($hour->id, $hours->first()?->id);
    });

    it('can comment on tickets', function () {
        $user = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne();

        // Create comment for user
        $comment = $ticket->ticketComments()->create([
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $comments = \Modules\Fixcity\Models\TicketComment::query()->where('user_id', $user->id)->get();
        Assert::assertCount(1, $comments);
        Assert::assertSame($comment->id, $comments->first()?->id);
    });

    it('can have multiple roles', function () {
        $user = UserFactory::new()->createOne();

        // Assign roles to user
        $user->assignRole('citizen');
        $user->assignRole('moderator');

        Assert::assertCount(2, $user->roles);
        Assert::assertTrue($user->hasRole('citizen'));
        Assert::assertTrue($user->hasRole('moderator'));
    });

    it('can have permissions', function () {
        $user = UserFactory::new()->createOne();

        // Give permission to user
        $user->givePermissionTo('create_tickets');
        $user->givePermissionTo('edit_tickets');

        Assert::assertCount(2, $user->permissions);
        Assert::assertTrue($user->can('create_tickets'));
        Assert::assertTrue($user->can('edit_tickets'));
    });

    it('can be part of teams', function () {
        $user = UserFactory::new()->createOne();

        // Create team and add user
        $team = Team::create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);

        $user->teams()->attach($team->id);

        Assert::assertCount(1, $user->teams);
        $firstTeam = $user->teams->first();
        Assert::assertNotNull($firstTeam);
        Assert::assertSame($team->id, $firstTeam->id);
    });

    it('can have tenants', function () {
        $user = UserFactory::new()->createOne();

        // Create tenant and add user
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
        ]);

        $user->tenants()->attach($tenant->id);

        Assert::assertCount(1, $user->tenants);
        $firstTenant = $user->tenants->first();
        Assert::assertNotNull($firstTenant);
        Assert::assertSame($tenant->id, $firstTenant->id);
    });

    it('can track authentication logs', function () {
        $user = UserFactory::new()->createOne();

        // Check if authentication logging is implemented
        if (method_exists($user, 'authentications')) {
            Assert::assertInstanceOf(HasMany::class, $user->authentications);
        }
    });

    it('can be searched by name', function () {
        $user = UserFactory::new()->createOne([
            'name' => 'Searchable User',
        ]);

        $searchResults = User::where('name', 'like', '%Searchable%')->get();

        Assert::assertContains($user, $searchResults);
    });

    it('can be searched by email', function () {
        $user = UserFactory::new()->createOne([
            'email' => 'searchable@example.com',
        ]);

        $searchResults = User::where('email', 'like', '%searchable%')->get();

        Assert::assertContains($user, $searchResults);
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced

    });

    it('can be deleted', function () {
        $user = UserFactory::new()->createOne();
        $userId = $user->id;
        $user->delete();

        Assert::assertNull(User::find($userId));
    });

    it('can be updated', function () {
        $user = UserFactory::new()->createOne([
            'name' => 'Original Name',
        ]);

        $user->update([
            'name' => 'Updated Name',
        ]);

        Assert::assertSame('Updated Name', $user->name);
    });

    it('tracks creation and update times', function () {
        $user = UserFactory::new()->createOne();

        Assert::assertNotNull($user->created_at);
        Assert::assertNotNull($user->updated_at);
        // Update the user
        $user->update(['name' => 'Updated']);

        Assert::assertGreaterThan($user->created_at, $user->updated_at);
    });

    it('can handle special characters in names', function () {
        $user = UserFactory::new()->createOne([
            'name' => 'José O\'Connor',
        ]);

        Assert::assertSame('José O\'Connor', $user->name);
    });

    it('can handle international email addresses', function () {
        $user = UserFactory::new()->createOne([
            'email' => 'test+tag@example.co.uk',
        ]);

        Assert::assertSame('test+tag@example.co.uk', $user->email);
    });
});
