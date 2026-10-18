<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Modules\Xot\Datas\XotData;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create();
});

describe('Ticket Model', function () {
    it('can be created with valid data', function () {
        $ticket = Ticket::factory()->create([
            'name' => 'Test Ticket',
            'content' => 'Test content for ticket',
            'owner_id' => $this->user->id,
            'status' => TicketStatusEnum::PENDING,
            'priority' => TicketPriorityEnum::MEDIUM,
            'type' => TicketTypeEnum::BUG,
        ]);

        expect($ticket)
            ->toBeInstanceOf(Ticket::class)
            ->name->toBe('Test Ticket')
            ->content->toBe('Test content for ticket')
            ->owner_id->toBe($this->user->id)
            ->status->toBe(TicketStatusEnum::PENDING)
            ->priority->toBe(TicketPriorityEnum::MEDIUM)
            ->type->toBe(TicketTypeEnum::BUG);
    });

    it('automatically sets status to PENDING when creating without status', function () {
        $ticket = Ticket::factory()->create([
            'name' => 'Auto Status Test',
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->status)->toBe(TicketStatusEnum::PENDING);
    });

    it('generates a slug automatically', function () {
        $ticket = Ticket::factory()->create([
            'name' => 'This Is A Test Ticket Name',
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->slug)
            ->not->toBeNull()
            ->toContain('this');
    });

    it('can store geolocation data', function () {
        $ticket = Ticket::factory()->create([
            'name' => 'Geo Test',
            'owner_id' => $this->user->id,
            'latitude' => '45.4642',
            'longitude' => '9.1900',
        ]);

        expect($ticket)
            ->latitude->toBe('45.4642')
            ->longitude->toBe('9.1900');
    });
});

describe('Ticket Relationships', function () {
    it('belongs to an owner', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->owner)
            ->toBeInstanceOf(User::class)
            ->id->toBe($this->user->id);
    });

    it('can have a responsible user', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
            'responsible_id' => $this->admin->id,
        ]);

        expect($ticket->responsible)
            ->toBeInstanceOf(User::class)
            ->id->toBe($this->admin->id);
    });

    it('can have activities', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->activities())
            ->toBeInstanceOf(HasMany::class);
    });

    it('can have hours logged', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->hours())
            ->toBeInstanceOf(HasMany::class);
    });

    it('can have relations to other tickets', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->relations())
            ->toBeInstanceOf(HasMany::class);
    });

    it('can have comments', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->comments())
            ->toBeInstanceOf(HasMany::class);
    });

    it('can have subscribers', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->subscribers())
            ->toBeInstanceOf(BelongsToMany::class);
    });
});

describe('Ticket Enums', function () {
    it('can use status enum values', function () {
        $statuses = [
            TicketStatusEnum::PENDING,
            TicketStatusEnum::IN_PROGRESS,
            TicketStatusEnum::RESOLVED,
            TicketStatusEnum::CLOSED,
        ];

        foreach ($statuses as $status) {
            $ticket = Ticket::factory()->create([
                'owner_id' => $this->user->id,
                'status' => $status,
            ]);

            expect($ticket->status)->toBe($status);
        }
    });

    it('can use priority enum values', function () {
        $priorities = [
            TicketPriorityEnum::LOW,
            TicketPriorityEnum::MEDIUM,
            TicketPriorityEnum::HIGH,
            TicketPriorityEnum::URGENT,
        ];

        foreach ($priorities as $priority) {
            $ticket = Ticket::factory()->create([
                'owner_id' => $this->user->id,
                'priority' => $priority,
            ]);

            expect($ticket->priority)->toBe($priority);
        }
    });

    it('can use type enum values', function () {
        $types = [
            TicketTypeEnum::BUG,
            TicketTypeEnum::FEATURE,
            TicketTypeEnum::IMPROVEMENT,
            TicketTypeEnum::TASK,
        ];

        foreach ($types as $type) {
            $ticket = Ticket::factory()->create([
                'owner_id' => $this->user->id,
                'type' => $type,
            ]);

            expect($ticket->type)->toBe($type);
        }
    });
});

describe('Ticket Methods', function () {
    it('can get icon data for type', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
            'type' => TicketTypeEnum::BUG,
        ]);

        $iconData = $ticket->getIconData();

        expect($iconData)
            ->toBeArray()
            ->toHaveKey('url')
            ->toHaveKey('type')
            ->toHaveKey('scale');
    });

    it('returns lat lng attributes', function () {
        $attributes = Ticket::getLatLngAttributes();

        expect($attributes)
            ->toBeArray()
            ->toHaveKey('lat', 'latitude')
            ->toHaveKey('lng', 'longitude');
    });

    it('can provide commentable name', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->commentableName())->toBe('Segnalazione');
    });

    it('can provide comment url', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket->commentUrl())->toBe('#');
    });
});

describe('Ticket Media', function () {
    it('implements HasMedia interface', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        expect($ticket)->toBeInstanceOf(HasMedia::class);
    });

    it('can register media collections', function () {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $ticket->registerMediaCollections();

        // Test that attachments collection is registered
        expect($ticket->getMediaCollection('attachments'))
            ->not->toBeNull();
    });
});

describe('Ticket Factory', function () {
    it('can create ticket with factory', function () {
        $ticket = Ticket::factory()->create();

        expect($ticket)
            ->toBeInstanceOf(Ticket::class)
            ->name->not->toBeNull()
            ->content->not->toBeNull()
            ->owner_id->not->toBeNull();
    });

    it('can create multiple tickets', function () {
        $tickets = Ticket::factory()->count(5)->create();

        expect($tickets)
            ->toHaveCount(5);

        $tickets->each(function ($ticket) {
            expect($ticket)->toBeInstanceOf(Ticket::class);
        });
    });

    it('can create ticket with specific status', function () {
        $ticket = Ticket::factory()->create([
            'status' => TicketStatusEnum::RESOLVED,
        ]);

        expect($ticket->status)->toBe(TicketStatusEnum::RESOLVED);
    });
});
