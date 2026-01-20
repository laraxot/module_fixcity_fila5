<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Modules\Fixcity\Models\Profile;
use Modules\User\Models\Team;
use Modules\User\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\User;
use Tests\TestCase;

describe('User Model (Fixcity)', function () {
    it('can be created with valid data', function () {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        expect($user)
            ->toBeInstanceOf(User::class)
            ->name->toBe('Test User')
            ->email->toBe('test@example.com');
    });

    it('can own tickets', function () {
        $user = User::factory()->create();
        $tickets = Ticket::factory()->count(3)->create([
            'owner_id' => $user->id,
        ]);

        expect($user->ownedTickets)->toHaveCount(3);
        foreach ($user->ownedTickets as $ticket) {
            expect($ticket->owner_id)->toBe($user->id);
        }
    });

    it('can be responsible for tickets', function () {
        $user = User::factory()->create();
        $tickets = Ticket::factory()->count(2)->create([
            'responsible_id' => $user->id,
        ]);

        expect($user->responsibleTickets)->toHaveCount(2);
        foreach ($user->responsibleTickets as $ticket) {
            expect($ticket->responsible_id)->toBe($user->id);
        }
    });

    it('can have a profile', function () {
        $user = User::factory()->create();
        
        // Create profile for user
        $profile = $user->profile()->create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
        ]);

        expect($user->profile)
            ->toBeInstanceOf(Profile::class)
            ->first_name->toBe('Mario')
            ->last_name->toBe('Rossi');
    });

    it('can subscribe to tickets', function () {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        
        // Subscribe user to ticket
        $user->subscribedTickets()->attach($ticket->id);

        expect($user->subscribedTickets)->toHaveCount(1);
        expect($user->subscribedTickets->first()->id)->toBe($ticket->id);
    });

    it('can track ticket activities', function () {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        
        // Create activity for user
        $activity = $ticket->activities()->create([
            'user_id' => $user->id,
            'old_status_id' => 1,
            'new_status_id' => 2,
        ]);

        expect($user->ticketActivities)->toHaveCount(1);
        expect($user->ticketActivities->first()->id)->toBe($activity->id);
    });

    it('can log hours on tickets', function () {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        
        // Log hours for user
        $hour = $ticket->hours()->create([
            'user_id' => $user->id,
            'value' => 2.5,
            'description' => 'Work performed',
            'date' => now()->toDateString(),
        ]);

        expect($user->ticketHours)->toHaveCount(1);
        expect($user->ticketHours->first()->id)->toBe($hour->id);
    });

    it('can comment on tickets', function () {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        
        // Create comment for user
        $comment = $ticket->comments()->create([
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        expect($user->ticketComments)->toHaveCount(1);
        expect($user->ticketComments->first()->id)->toBe($comment->id);
    });

    it('can have multiple roles', function () {
        $user = User::factory()->create();
        
        // Assign roles to user
        $user->assignRole('citizen');
        $user->assignRole('moderator');

        expect($user->roles)->toHaveCount(2);
        expect($user->hasRole('citizen'))->toBeTrue();
        expect($user->hasRole('moderator'))->toBeTrue();
    });

    it('can have permissions', function () {
        $user = User::factory()->create();
        
        // Give permission to user
        $user->givePermissionTo('create_tickets');
        $user->givePermissionTo('edit_tickets');

        expect($user->permissions)->toHaveCount(2);
        expect($user->can('create_tickets'))->toBeTrue();
        expect($user->can('edit_tickets'))->toBeTrue();
    });

    it('can be part of teams', function () {
        $user = User::factory()->create();
        
        // Create team and add user
        $team = Team::create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);
        
        $user->teams()->attach($team->id);

        expect($user->teams)->toHaveCount(1);
        expect($user->teams->first()->id)->toBe($team->id);
    });

    it('can have tenants', function () {
        $user = User::factory()->create();
        
        // Create tenant and add user
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
        ]);
        
        $user->tenants()->attach($tenant->id);

        expect($user->tenants)->toHaveCount(1);
        expect($user->tenants->first()->id)->toBe($tenant->id);
    });

    it('can track authentication logs', function () {
        $user = User::factory()->create();
        
        // Check if authentication logging is implemented
        if (method_exists($user, 'authentications')) {
            expect($user->authentications)->toBeInstanceOf(HasMany::class);
        }
    });

    it('can be searched by name', function () {
        $user = User::factory()->create([
            'name' => 'Searchable User',
        ]);

        $searchResults = User::where('name', 'like', '%Searchable%')->get();
        
        expect($searchResults)->toContain($user);
    });

    it('can be searched by email', function () {
        $user = User::factory()->create([
            'email' => 'searchable@example.com',
        ]);

        $searchResults = User::where('email', 'like', '%searchable%')->get();
        
        expect($searchResults)->toContain($user);
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced
        expect(function () {
            User::create([]);
        })->toThrow(QueryException::class);
    });

    it('can be soft deleted if implemented', function () {
        $user = User::factory()->create();
        
        // Check if soft deletes are implemented
        if (method_exists($user, 'trashed')) {
            $user->delete();
            expect($user->trashed())->toBeTrue();
            
            $trashedUser = User::withTrashed()->find($user->id);
            expect($trashedUser)->not->toBeNull();
        } else {
            // If no soft deletes, test regular deletion
            $userId = $user->id;
            $user->delete();
            
            expect(User::find($userId))->toBeNull();
        }
    });

    it('can be updated', function () {
        $user = User::factory()->create([
            'name' => 'Original Name',
        ]);

        $user->update([
            'name' => 'Updated Name',
        ]);

        expect($user->name)->toBe('Updated Name');
    });

    it('tracks creation and update times', function () {
        $user = User::factory()->create();
        
        expect($user->created_at)->not->toBeNull();
        expect($user->updated_at)->not->toBeNull();
        
        // Update the user
        $user->update(['name' => 'Updated']);
        
        expect($user->updated_at)->toBeGreaterThan($user->created_at);
    });

    it('can handle special characters in names', function () {
        $user = User::factory()->create([
            'name' => 'José O\'Connor',
        ]);

        expect($user->name)->toBe('José O\'Connor');
    });

    it('can handle international email addresses', function () {
        $user = User::factory()->create([
            'email' => 'test+tag@example.co.uk',
        ]);

        expect($user->email)->toBe('test+tag@example.co.uk');
    });
});
