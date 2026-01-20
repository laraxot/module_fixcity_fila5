<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Illuminate\Database\QueryException;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\User\Models\User;
use Tests\TestCase;

describe('TicketActivity Model', function () {
    it('can be created with valid data', function () {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        
        $activity = TicketActivity::create([
            'ticket_id' => $ticket->id,
            'old_status_id' => 1,
            'new_status_id' => 2,
            'user_id' => $user->id,
        ]);

        expect($activity)
            ->toBeInstanceOf(TicketActivity::class)
            ->ticket_id->toBe($ticket->id)
            ->old_status_id->toBe(1)
            ->new_status_id->toBe(2)
            ->user_id->toBe($user->id);
    });

    it('belongs to a ticket', function () {
        $ticket = Ticket::factory()->create();
        $activity = TicketActivity::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        expect($activity->ticket)
            ->toBeInstanceOf(Ticket::class)
            ->id->toBe($ticket->id);
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $activity = TicketActivity::factory()->create([
            'user_id' => $user->id,
        ]);

        expect($activity->user)
            ->toBeInstanceOf(User::class)
            ->id->toBe($user->id);
    });

    it('tracks status changes correctly', function () {
        $activity = TicketActivity::factory()->create([
            'old_status_id' => 1,
            'new_status_id' => 2,
        ]);

        expect($activity->old_status_id)->toBe(1);
        expect($activity->new_status_id)->toBe(2);
        expect($activity->hasStatusChanged())->toBeTrue();
    });

    it('can handle null old status for new tickets', function () {
        $activity = TicketActivity::factory()->create([
            'old_status_id' => null,
            'new_status_id' => 1,
        ]);

        expect($activity->old_status_id)->toBeNull();
        expect($activity->new_status_id)->toBe(1);
        expect($activity->hasStatusChanged())->toBeTrue();
    });

    it('can handle null new status for deleted tickets', function () {
        $activity = TicketActivity::factory()->create([
            'old_status_id' => 1,
            'new_status_id' => null,
        ]);

        expect($activity->old_status_id)->toBe(1);
        expect($activity->new_status_id)->toBeNull();
        expect($activity->hasStatusChanged())->toBeTrue();
    });

    it('provides readable status change description', function () {
        $activity = TicketActivity::factory()->create([
            'old_status_id' => 1,
            'new_status_id' => 2,
        ]);

        $description = $activity->getStatusChangeDescription();
        expect($description)->not->toBeEmpty();
        expect($description)->toContain('Status changed');
    });

    it('can be queried by ticket', function () {
        $ticket = Ticket::factory()->create();
        $activities = TicketActivity::factory()->count(3)->create([
            'ticket_id' => $ticket->id,
        ]);

        $ticketActivities = TicketActivity::where('ticket_id', $ticket->id)->get();
        
        expect($ticketActivities)->toHaveCount(3);
        foreach ($ticketActivities as $activity) {
            expect($activity->ticket_id)->toBe($ticket->id);
        }
    });

    it('can be queried by user', function () {
        $user = User::factory()->create();
        $activities = TicketActivity::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $userActivities = TicketActivity::where('user_id', $user->id)->get();
        
        expect($userActivities)->toHaveCount(3);
        foreach ($userActivities as $activity) {
            expect($activity->user_id)->toBe($user->id);
        }
    });

    it('can be queried by date range', function () {
        $activity = TicketActivity::factory()->create();
        
        $recentActivities = TicketActivity::where('created_at', '>=', now()->subDays(7))->get();
        
        expect($recentActivities)->toContain($activity);
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced
        expect(function () {
            TicketActivity::create([]);
        })->toThrow(QueryException::class);
    });

    it('can be soft deleted if implemented', function () {
        $activity = TicketActivity::factory()->create();
        
        // Check if soft deletes are implemented
        if (method_exists($activity, 'trashed')) {
            $activity->delete();
            expect($activity->trashed())->toBeTrue();
            
            $trashedActivity = TicketActivity::withTrashed()->find($activity->id);
            expect($trashedActivity)->not->toBeNull();
        } else {
            // If no soft deletes, test regular deletion
            $activityId = $activity->id;
            $activity->delete();
            
            expect(TicketActivity::find($activityId))->toBeNull();
        }
    });
});
