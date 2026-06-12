<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fixcity\Tests\TestCase;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Spatie\MediaLibrary\HasMedia;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        Assert::assertNotNull($this->admin);
    $this->user = UserFactory::new()->createOne();
    $this->admin = UserFactory::new()->createOne();
});

describe('Ticket Model', function () {
    it('can be created with valid data', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Test Ticket',
            'content' => 'Test content for ticket',
            'owner_id' => $this->user->id,
            'status' => TicketStatusEnum::PENDING,
            'priority' => TicketPriorityEnum::MEDIUM,
            'type' => TicketTypeEnum::COMPLAINT,
        ]);

        Assert::assertInstanceOf(Ticket::class, $ticket);

        Assert::assertSame('Test Ticket', $ticket->name);

        Assert::assertSame('Test content for ticket', $ticket->content);

        Assert::assertSame($this->user->id, $ticket->owner_id);

        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);

        Assert::assertSame(TicketPriorityEnum::MEDIUM, $ticket->priority);

        Assert::assertSame(TicketTypeEnum::COMPLAINT, $ticket->type);
    });

    it('automatically sets status to PENDING when creating without status', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Auto Status Test',
            'owner_id' => $this->user->id,
        ]);

        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
    });

    it('generates a slug automatically', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'This Is A Test Ticket Name',
            'owner_id' => $this->user->id,
        ]);

        Assert::assertNotNull($ticket->slug);

        Assert::assertStringContainsString('this', (string) $ticket->slug);
    });

    it('can store geolocation data', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Geo Test',
            'owner_id' => $this->user->id,
            'latitude' => '45.4642',
            'longitude' => '9.1900',
        ]);

        Assert::assertSame('45.4642', $ticket->latitude);

        Assert::assertSame('9.1900', $ticket->longitude);
    });

    it('stores location as JSON and mirrors lat/lng columns', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Location JSON Test',
            'owner_id' => $this->user->id,
            'location' => [
                'lat' => '45.562246',
                'lng' => '12.249756',
                'address' => 'Via Rodolfo Morandi, Mogliano Veneto',
                'provider' => 'nominatim',
            ],
        ]);

        Assert::assertSame('45.562246', $ticket->latitude);

        Assert::assertSame('12.249756', $ticket->longitude);

        Assert::assertIsArray($ticket->location);

        Assert::assertSame('45.562246', $ticket->location['lat']);

        Assert::assertSame('12.249756', $ticket->location['lng']);

        Assert::assertSame('Via Rodolfo Morandi, Mogliano Veneto', $ticket->location['address']);
    });

    it('parses Nominatim addressdetails into structured location fields', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Nominatim Test',
            'owner_id' => $this->user->id,
            'location' => [
                'lat' => '45.562246',
                'lng' => '12.249756',
                'address' => 'Via Rodolfo Morandi 5, Mogliano Veneto',
                'addressdetails' => [
                    'road' => 'Via Rodolfo Morandi',
                    'house_number' => '5',
                    'postcode' => '31021',
                    'city' => 'Mogliano Veneto',
                    'state' => 'Veneto',
                    'country' => 'Italia',
                    'country_code' => 'it',
                ],
            ],
        ]);

        $location = $ticket->location;

        Assert::assertIsArray($location);

        Assert::assertSame('Via Rodolfo Morandi', $location['street']);

        Assert::assertSame('5', $location['street_number']);

        Assert::assertSame('31021', $location['zip']);

        Assert::assertSame('Mogliano Veneto', $location['city']);

        Assert::assertSame('Veneto', $location['region']);

        Assert::assertSame('Italia', $location['country']);

        Assert::assertSame('it', $location['country_code']);
    });

    it('does not write address column — only location, latitude, longitude', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'No Address Column Test',
            'owner_id' => $this->user->id,
            'location' => [
                'lat' => '41.9028',
                'lng' => '12.4964',
                'address' => 'Roma',
            ],
        ]);

        // Must not throw "no column named address"
        Assert::assertSame('41.9028', $ticket->latitude);
        Assert::assertSame('12.4964', $ticket->longitude);
    });
});

describe('Ticket Relationships', function () {
    it('belongs to an owner', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertInstanceOf(User::class, $ticket->owner);

        Assert::assertSame($this->user->id, $ticket->owner->id);
    });

    it('can have a responsible user', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        Assert::assertNotNull($this->admin);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
            'responsible_id' => $this->admin->id,
        ]);

        Assert::assertInstanceOf(User::class, $ticket->responsible);

        Assert::assertSame($this->admin->id, $ticket->responsible->id);
    });

    it('can have activities', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertInstanceOf(HasMany::class, $ticket->activities());
    });

    it('can have hours logged', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertInstanceOf(HasMany::class, $ticket->hours());
    });

    it('can have relations to other tickets', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertInstanceOf(HasMany::class, $ticket->relations());
    });

    it('can have spatie comments', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class, $ticket->comments());
    });

    it('can have subscribers', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertInstanceOf(BelongsToMany::class, $ticket->ticketSubscribers());
    });
});

describe('Ticket Enums', function () {
    it('can use status enum values', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $statuses = [
            TicketStatusEnum::PENDING,
            TicketStatusEnum::IN_PROGRESS,
            TicketStatusEnum::RESOLVED,
            TicketStatusEnum::CLOSED,
        ];

        foreach ($statuses as $status) {
            $ticket = TicketFactory::new()->createOne([
                'owner_id' => $this->authUser()->id,
                'status' => $status,
            ]);

            Assert::assertSame($status, $ticket->status);
        }
    });

    it('can use priority enum values', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        /** @var list<TicketPriorityEnum> $priorities */
        $priorities = [
            TicketPriorityEnum::LOW,
            TicketPriorityEnum::MEDIUM,
            TicketPriorityEnum::HIGH,
            TicketPriorityEnum::URGENT,
        ];

        foreach ($priorities as $priority) {
            $ticket = TicketFactory::new()->createOne([
                'owner_id' => $this->authUser()->id,
                'priority' => $priority,
            ]);

            Assert::assertSame($priority, $ticket->priority);
        }
    });

    it('can use type enum values', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $types = [
            TicketTypeEnum::COMPLAINT,
            TicketTypeEnum::REQUEST,
            TicketTypeEnum::SUGGESTION,
            TicketTypeEnum::REQUEST,
        ];

        foreach ($types as $type) {
            $ticket = TicketFactory::new()->createOne([
                'owner_id' => $this->authUser()->id,
                'type' => $type,
            ]);

            Assert::assertSame($type, $ticket->type);
        }
    });
});

describe('Ticket Methods', function () {
    it('can get icon data for type', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
            'type' => TicketTypeEnum::COMPLAINT,
        ]);

        $iconData = $ticket->getIconData();

        Assert::assertArrayHasKey('url', $iconData);

        Assert::assertArrayHasKey('type', $iconData);

        Assert::assertArrayHasKey('scale', $iconData);
    });

    it('returns lat lng attributes', function () {
        /** @var TestCase $this */
        $attributes = Ticket::getLatLngAttributes();

        Assert::assertArrayHasKey('lat', $attributes);

        Assert::assertSame('latitude', $attributes['lat']);

        Assert::assertArrayHasKey('lng', $attributes);

        Assert::assertSame('longitude', $attributes['lng']);
    });

    it('can provide commentable name', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertSame('Segnalazione', $ticket->commentableName());
    });

    it('can provide comment url', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertSame('#', $ticket->commentUrl());
    });
});

describe('Ticket Media', function () {
    it('implements HasMedia interface', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        Assert::assertInstanceOf(HasMedia::class, $ticket);
    });

    it('can register media collections', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);

        $ticket->registerMediaCollections();

        // Test that attachments collection is registered
        Assert::assertNotNull($ticket->getMediaCollection('attachments'));
    });
});

describe('Ticket Factory', function () {
    it('can create ticket with factory', function () {
        /** @var TestCase $this */
        $ticket = TicketFactory::new()->createOne();

        Assert::assertInstanceOf(Ticket::class, $ticket);

        Assert::assertNotNull($ticket->name);

        Assert::assertNotNull($ticket->content);

        Assert::assertNotNull($ticket->owner_id);
    });

    it('can create multiple tickets', function () {
        /** @var TestCase $this */
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(5)->create();

        Assert::assertCount(5, $tickets);

        $tickets->each(function ($ticket) {
            Assert::assertInstanceOf(Ticket::class, $ticket);
        });
    });

    it('can create ticket with specific status', function () {
        /** @var TestCase $this */
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::RESOLVED,
        ]);

        Assert::assertSame(TicketStatusEnum::RESOLVED, $ticket->status);
    });
});
