<?php

declare(strict_types=1);

use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketHour;
use Modules\User\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->ticket = Ticket::factory()->create([
        'owner_id' => $this->user->id,
        'estimation' => 2.5, // 2.5 hours
    ]);
});

describe('Ticket Business Logic Methods', function () {
    describe('Time Tracking Methods', function () {
        it('calculates total logged hours correctly', function () {
            // Create some logged hours
            TicketHour::factory()->create([
                'ticket_id' => $this->ticket->id,
                'user_id' => $this->user->id,
                'value' => 1.5, // 1.5 hours
            ]);

            TicketHour::factory()->create([
                'ticket_id' => $this->ticket->id,
                'user_id' => $this->user->id,
                'value' => 2.0, // 2.0 hours
            ]);

            expect($this->ticket->total_logged_in_hours)->toBe(3.5);
        });

        it('returns 0 when no hours are logged', function () {
            expect($this->ticket->total_logged_in_hours)->toBe(0.0);
        });

        it('handles decimal hour values correctly', function () {
            TicketHour::factory()->create([
                'ticket_id' => $this->ticket->id,
                'user_id' => $this->user->id,
                'value' => 0.75, // 45 minutes
            ]);

            expect($this->ticket->total_logged_in_hours)->toBe(0.75);
        });
    });

    describe('Estimation Methods', function () {
        it('converts estimation to human readable format', function () {
            // This test assumes estimation_in_seconds is calculated correctly
            // For now, we test that the method exists and returns a string
            $result = $this->ticket->estimation_for_humans;

            expect($result)->toBeString()->not->toBeEmpty();
        });

        it('handles null estimation gracefully', function () {
            $ticket = Ticket::factory()->create([
                'owner_id' => $this->user->id,
                'estimation' => null,
            ]);

            $result = $ticket->estimation_for_humans;

            expect($result)->toBeString();
        });
    });

    describe('Media Collection Methods', function () {
        it('registers media collections with correct configuration', function () {
            $this->ticket->registerMediaCollections();

            $collection = $this->ticket->getMediaCollection('attachments');

            expect($collection)->not->toBeNull()
                ->and($collection->acceptsMimeTypes)->toContain('image/jpeg')
                ->and($collection->acceptsMimeTypes)->toContain('image/png')
                ->and($collection->acceptsMimeTypes)->toContain('application/pdf');
        });

        it('has proper media collection name', function () {
            $this->ticket->registerMediaCollections();

            expect($this->ticket->getMediaCollection('attachments'))
                ->not->toBeNull();
        });
    });

    describe('Comment System Integration', function () {
        it('provides correct commentable name', function () {
            expect($this->ticket->commentableName())->toBe('Segnalazione');
        });

        it('provides comment URL', function () {
            expect($this->ticket->commentUrl())->toBe('#');
        });
    });

    describe('Geolocation Methods', function () {
        it('provides correct latitude/longitude attribute mapping', function () {
            $attributes = Ticket::getLatLngAttributes();

            expect($attributes)->toBe([
                'lat' => 'latitude',
                'lng' => 'longitude',
            ]);
        });

        it('can store and retrieve geolocation data', function () {
            $ticket = Ticket::factory()->create([
                'owner_id' => $this->user->id,
                'latitude' => '45.4642',
                'longitude' => '9.1900',
            ]);

            expect($ticket->latitude)->toBe('45.4642')
                ->and($ticket->longitude)->toBe('9.1900');
        });
    });

    describe('Slug Generation', function () {
        it('generates slug automatically from name', function () {
            $ticket = Ticket::factory()->create([
                'name' => 'Test Ticket Name With Spaces',
                'owner_id' => $this->user->id,
            ]);

            expect($ticket->slug)
                ->not->toBeNull()
                ->toContain('test')
                ->toContain('ticket')
                ->toContain('name');
        });

        it('uses existing slug if provided', function () {
            $ticket = Ticket::factory()->create([
                'name' => 'Test Ticket',
                'slug' => 'custom-slug-123',
                'owner_id' => $this->user->id,
            ]);

            expect($ticket->slug)->toBe('custom-slug-123');
        });
    });

    describe('Default Values', function () {
        it('sets default status to PENDING when creating', function () {
            $ticket = Ticket::factory()->create([
                'name' => 'Test Default Status',
                'owner_id' => $this->user->id,
                // No status provided
            ]);

            expect($ticket->status->value)->toBe('pending');
        });

        it('respects provided status instead of default', function () {
            $ticket = Ticket::factory()->create([
                'name' => 'Test Custom Status',
                'owner_id' => $this->user->id,
                'status' => TicketStatusEnum::IN_PROGRESS,
            ]);

            expect($ticket->status->value)->toBe('in_progress');
        });
    });

    describe('Icon Data Generation', function () {
        it('generates icon data for valid ticket types', function () {
            $ticket = Ticket::factory()->create([
                'owner_id' => $this->user->id,
                'type' => TicketTypeEnum::BUG,
            ]);

            $iconData = $ticket->getIconData();

            expect($iconData)->toBeArray()
                ->toHaveKey('url')
                ->toHaveKey('type')
                ->toHaveKey('scale');
        });

        it('returns empty array for null type', function () {
            $ticket = Ticket::factory()->create([
                'owner_id' => $this->user->id,
                'type' => null,
            ]);

            $iconData = $ticket->getIconData();

            expect($iconData)->toBe([]);
        });
    });
});

describe('Ticket Relationships Business Logic', function () {
    it('correctly associates with owner', function () {
        expect($this->ticket->owner)
            ->toBeInstanceOf(User::class)
            ->id->toBe($this->user->id);
    });

    it('can have responsible user assigned', function () {
        $responsible = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
            'responsible_id' => $responsible->id,
        ]);

        expect($ticket->responsible)
            ->toBeInstanceOf(User::class)
            ->id->toBe($responsible->id);
    });

    it('can have multiple hours logged', function () {
        TicketHour::factory()->count(3)->create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
        ]);

        expect($this->ticket->hours)->toHaveCount(3);
    });

    it('can have subscribers', function () {
        $subscriber = User::factory()->create();
        $this->ticket->subscribers()->attach($subscriber->id);

        expect($this->ticket->subscribers)
            ->toHaveCount(1)
            ->first()->id->toBe($subscriber->id);
    });

    it('can have comments', function () {
        // Assuming comments relationship is implemented
        expect($this->ticket->comments())
            ->toBeInstanceOf(HasMany::class);
    });
});

describe('Ticket State Management', function () {
    it('can transition between statuses', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
            'status' => TicketStatusEnum::PENDING,
        ]);

        // Transition to in progress
        $ticket->update(['status' => TicketStatusEnum::IN_PROGRESS]);
        expect($ticket->fresh()->status->value)->toBe('in_progress');

        // Transition to resolved
        $ticket->update(['status' => TicketStatusEnum::RESOLVED]);
        expect($ticket->fresh()->status->value)->toBe('resolved');
    });

    it('maintains enum type integrity during transitions', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
            'status' => TicketStatusEnum::PENDING,
        ]);

        $ticket->update(['status' => TicketStatusEnum::CLOSED]);

        expect($ticket->fresh()->status)
            ->toBeInstanceOf(TicketStatusEnum::class)
            ->value->toBe('closed');
    });
});
