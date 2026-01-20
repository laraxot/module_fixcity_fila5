<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Illuminate\Database\QueryException;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketHour;
use Modules\User\Models\User;
use Tests\TestCase;

describe('TicketHour Model', function () {
    it('can be created with valid data', function () {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        
        $hour = TicketHour::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'value' => 2.5,
            'description' => 'Work performed on ticket',
            'date' => now()->toDateString(),
        ]);

        expect($hour)
            ->toBeInstanceOf(TicketHour::class)
            ->ticket_id->toBe($ticket->id)
            ->user_id->toBe($user->id)
            ->value->toBe(2.5)
            ->description->toBe('Work performed on ticket');
    });

    it('belongs to a ticket', function () {
        $ticket = Ticket::factory()->create();
        $hour = TicketHour::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        expect($hour->ticket)
            ->toBeInstanceOf(Ticket::class)
            ->id->toBe($ticket->id);
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $hour = TicketHour::factory()->create([
            'user_id' => $user->id,
        ]);

        expect($hour->user)
            ->toBeInstanceOf(User::class)
            ->id->toBe($user->id);
    });

    it('can store decimal hour values', function () {
        $hour = TicketHour::factory()->create([
            'value' => 1.75,
        ]);

        expect($hour->value)->toBe(1.75);
    });

    it('can store whole hour values', function () {
        $hour = TicketHour::factory()->create([
            'value' => 3,
        ]);

        expect($hour->value)->toBe(3.0);
    });

    it('can store fractional hour values', function () {
        $hour = TicketHour::factory()->create([
            'value' => 0.25,
        ]);

        expect($hour->value)->toBe(0.25);
    });

    it('tracks date of work', function () {
        $workDate = now()->subDays(2)->toDateString();
        $hour = TicketHour::factory()->create([
            'date' => $workDate,
        ]);

        expect($hour->date)->toBe($workDate);
    });

    it('can store description of work', function () {
        $description = 'Analyzed the issue, identified root cause, and implemented fix';
        $hour = TicketHour::factory()->create([
            'description' => $description,
        ]);

        expect($hour->description)->toBe($description);
    });

    it('can be queried by ticket', function () {
        $ticket = Ticket::factory()->create();
        $hours = TicketHour::factory()->count(3)->create([
            'ticket_id' => $ticket->id,
        ]);

        $ticketHours = TicketHour::where('ticket_id', $ticket->id)->get();
        
        expect($ticketHours)->toHaveCount(3);
        foreach ($ticketHours as $hour) {
            expect($hour->ticket_id)->toBe($ticket->id);
        }
    });

    it('can be queried by user', function () {
        $user = User::factory()->create();
        $hours = TicketHour::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $userHours = TicketHour::where('user_id', $user->id)->get();
        
        expect($userHours)->toHaveCount(3);
        foreach ($userHours as $hour) {
            expect($hour->user_id)->toBe($user->id);
        }
    });

    it('can be queried by date range', function () {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        
        $todayHour = TicketHour::factory()->create(['date' => $today]);
        $yesterdayHour = TicketHour::factory()->create(['date' => $yesterday]);
        
        $recentHours = TicketHour::where('date', '>=', $yesterday)->get();
        
        expect($recentHours)->toContain($todayHour);
        expect($recentHours)->toContain($yesterdayHour);
    });

    it('can calculate total hours for a ticket', function () {
        $ticket = Ticket::factory()->create();
        
        TicketHour::factory()->create([
            'ticket_id' => $ticket->id,
            'value' => 2.5,
        ]);
        
        TicketHour::factory()->create([
            'ticket_id' => $ticket->id,
            'value' => 1.75,
        ]);
        
        TicketHour::factory()->create([
            'ticket_id' => $ticket->id,
            'value' => 3.0,
        ]);

        $totalHours = TicketHour::where('ticket_id', $ticket->id)->sum('value');
        
        expect($totalHours)->toBe(7.25);
    });

    it('can calculate total hours for a user', function () {
        $user = User::factory()->create();
        
        TicketHour::factory()->create([
            'user_id' => $user->id,
            'value' => 4.0,
        ]);
        
        TicketHour::factory()->create([
            'user_id' => $user->id,
            'value' => 2.5,
        ]);

        $totalHours = TicketHour::where('user_id', $user->id)->sum('value');
        
        expect($totalHours)->toBe(6.5);
    });

    it('can be ordered by date', function () {
        $oldHour = TicketHour::factory()->create([
            'date' => now()->subDays(3)->toDateString(),
        ]);
        
        $newHour = TicketHour::factory()->create([
            'date' => now()->toDateString(),
        ]);

        $orderedHours = TicketHour::orderBy('date', 'desc')->get();
        
        expect($orderedHours->first()->id)->toBe($newHour->id);
        expect($orderedHours->last()->id)->toBe($oldHour->id);
    });

    it('can be filtered by minimum hour value', function () {
        TicketHour::factory()->create(['value' => 0.5]);
        TicketHour::factory()->create(['value' => 2.0]);
        TicketHour::factory()->create(['value' => 4.5]);

        $significantHours = TicketHour::where('value', '>=', 2.0)->get();
        
        expect($significantHours)->toHaveCount(2);
        foreach ($significantHours as $hour) {
            expect($hour->value)->toBeGreaterThanOrEqual(2.0);
        }
    });

    it('maintains data integrity constraints', function () {
        // Test that required fields are enforced
        expect(function () {
            TicketHour::create([]);
        })->toThrow(QueryException::class);
    });

    it('can be soft deleted if implemented', function () {
        $hour = TicketHour::factory()->create();
        
        // Check if soft deletes are implemented
        if (method_exists($hour, 'trashed')) {
            $hour->delete();
            expect($hour->trashed())->toBeTrue();
            
            $trashedHour = TicketHour::withTrashed()->find($hour->id);
            expect($trashedHour)->not->toBeNull();
        } else {
            // If no soft deletes, test regular deletion
            $hourId = $hour->id;
            $hour->delete();
            
            expect(TicketHour::find($hourId))->toBeNull();
        }
    });

    it('validates hour values are positive', function () {
        // Test that negative values are not allowed
        expect(function () {
            TicketHour::factory()->create(['value' => -1.0]);
        })->toThrow(QueryException::class);
    });

    it('can handle zero hour values', function () {
        $hour = TicketHour::factory()->create([
            'value' => 0.0,
        ]);

        expect($hour->value)->toBe(0.0);
    });
});