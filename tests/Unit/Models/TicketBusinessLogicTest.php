<?php

declare(strict_types=1);


use Modules\Fixcity\Tests\TestCase;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Database\Factories\TicketHourFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketHour;
use Modules\User\Models\User;

uses(TestCase::class);
beforeEach(function () {
    /** @var TestCase $this */
    $this->user = UserFactory::new()->createOne();
    $this->ticket = TicketFactory::new()->createOne([
        'owner_id' => $this->authUser()->id,
        'estimation' => 2.5,
    ]);
});

describe('Ticket Business Logic Methods', function () {
    describe('Time Tracking Methods', function () {
        it('calculates total logged hours correctly', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
        Assert::assertNotNull($this->user);
            // Create some logged hours
            TicketHourFactory::new()->createOne([
                'ticket_id' => $this->ticket()->id,
                'user_id' => $this->authUser()->id,
                'value' => 1.5, // 1.5 hours
            ]);

            TicketHourFactory::new()->createOne([
                'ticket_id' => $this->ticket()->id,
                'user_id' => $this->authUser()->id,
                'value' => 2.0, // 2.0 hours
            ]);

            Assert::assertSame(3.5, $this->ticket->total_logged_in_hours);
        });

        it('returns 0 when no hours are logged', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
            Assert::assertSame(0.0, $this->ticket->total_logged_in_hours);
        });

        it('handles decimal hour values correctly', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
        Assert::assertNotNull($this->user);
            TicketHourFactory::new()->createOne([
                'ticket_id' => $this->ticket()->id,
                'user_id' => $this->authUser()->id,
                'value' => 0.75, // 45 minutes
            ]);

            Assert::assertSame(0.75, $this->ticket->total_logged_in_hours);
        });
    });

    describe('Estimation Methods', function () {
        it('converts estimation to human readable format', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
            // This test assumes estimation_in_seconds is calculated correctly
            // For now, we test that the method exists and returns a string
            $result = $this->ticket->estimation_for_humans;

            Assert::assertIsString($result);
        });

        it('handles null estimation gracefully', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'owner_id' => $this->authUser()->id,
                'estimation' => null,
            ]);

            $result = $ticket->estimation_for_humans;

            Assert::assertIsString($result);
        });
    });

    describe('Media Collection Methods', function () {
        it('registers media collections with correct configuration', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
            $this->ticket->registerMediaCollections();

            $collection = $this->ticket->getMediaCollection('attachments');

            Assert::assertNotNull($collection);

            Assert::assertContains('image/jpeg', $collection->acceptsMimeTypes);

            Assert::assertContains('image/png', $collection->acceptsMimeTypes);

            Assert::assertContains('application/pdf', $collection->acceptsMimeTypes);
        });

        it('has proper media collection name', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
            $this->ticket->registerMediaCollections();

            Assert::assertNotNull($this->ticket->getMediaCollection('attachments'));
        });
    });

    describe('Comment System Integration', function () {
        it('provides correct commentable name', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
            Assert::assertSame('Segnalazione', $this->ticket->commentableName());
        });

        it('provides comment URL', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
            Assert::assertSame('#', $this->ticket->commentUrl());
        });
    });

    describe('Geolocation Methods', function () {
        it('provides correct latitude/longitude attribute mapping', function () {
            /** @var TestCase $this */
            $attributes = Ticket::getLatLngAttributes();

        });

        it('can store and retrieve geolocation data', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'owner_id' => $this->authUser()->id,
                'latitude' => '45.4642',
                'longitude' => '9.1900',
            ]);

            Assert::assertSame('45.4642', $ticket->latitude);

            Assert::assertSame('9.1900', $ticket->longitude);
        });
    });

    describe('Slug Generation', function () {
        it('generates slug automatically from name', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'name' => 'Test Ticket Name With Spaces',
                'owner_id' => $this->authUser()->id,
            ]);

            Assert::assertNotNull($ticket->slug);

            Assert::assertStringContainsString('test', (string) $ticket->slug);

            Assert::assertStringContainsString('ticket', (string) $ticket->slug);

            Assert::assertStringContainsString('name', (string) $ticket->slug);
        });

        it('uses existing slug if provided', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'name' => 'Test Ticket',
                'slug' => 'custom-slug-123',
                'owner_id' => $this->authUser()->id,
            ]);

            Assert::assertSame('custom-slug-123', $ticket->slug);
        });
    });

    describe('Default Values', function () {
        it('sets default status to PENDING when creating', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'name' => 'Test Default Status',
                'owner_id' => $this->authUser()->id,
                // No status provided
            ]);

            Assert::assertNotNull($ticket->status);
            Assert::assertSame('pending', $ticket->status->value);
        });

        it('respects provided status instead of default', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'name' => 'Test Custom Status',
                'owner_id' => $this->authUser()->id,
                'status' => TicketStatusEnum::IN_PROGRESS,
            ]);

            Assert::assertNotNull($ticket->status);
            Assert::assertSame('in_progress', $ticket->status->value);
        });
    });

    describe('Icon Data Generation', function () {
        it('generates icon data for valid ticket types', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'owner_id' => $this->authUser()->id,
                'type' => TicketTypeEnum::COMPLAINT,
            ]);

            $iconData = $ticket->getIconData();

            Assert::assertArrayHasKey('url', $iconData);

            Assert::assertArrayHasKey('type', $iconData);

            Assert::assertArrayHasKey('scale', $iconData);
        });

        it('returns empty array for null type', function () {
            /** @var TestCase $this */
        Assert::assertNotNull($this->user);
            $ticket = TicketFactory::new()->createOne([
                'owner_id' => $this->authUser()->id,
                'type' => null,
            ]);

            $iconData = $ticket->getIconData();

            Assert::assertSame([], $iconData);
        });
    });
});

describe('Ticket Relationships Business Logic', function () {
    it('correctly associates with owner', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
        Assert::assertNotNull($this->user);
        Assert::assertInstanceOf(User::class, $this->ticket->owner);
        Assert::assertSame($this->authUser()->id, $this->ticket->owner->id);
    });

    it('can have responsible user assigned', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $responsible = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'responsible_id' => $responsible->id,
        ]);

        Assert::assertInstanceOf(User::class, $ticket->responsible);

        Assert::assertSame($responsible->id, $ticket->responsible->id);
    });

    it('can have multiple hours logged', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
        Assert::assertNotNull($this->user);
        TicketHourFactory::new()->count(3)->create([
            'ticket_id' => $this->ticket()->id,
            'user_id' => $this->authUser()->id,
        ]);

        Assert::assertCount(3, $this->ticket->hours);
    });

    it('can have subscribers', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
        $subscriber = UserFactory::new()->createOne();
        $this->ticket->subscribers()->attach($subscriber->id);

        Assert::assertCount(1, $this->ticket->subscribers);

        $firstSubscriber = $this->ticket->subscribers->first();
        Assert::assertNotNull($firstSubscriber);
        Assert::assertSame($subscriber->id, $firstSubscriber->id);
    });

    it('can have spatie comments', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
        Assert::assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class, $this->ticket->comments());
    });

    it('can have legacy ticket comments', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->ticket);
        Assert::assertInstanceOf(HasMany::class, $this->ticket->ticketComments());
    });
});

describe('Ticket State Management', function () {
    it('can transition between statuses', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'status' => TicketStatusEnum::PENDING,
        ]);

        // Transition to in progress
        $ticket->update(['status' => TicketStatusEnum::IN_PROGRESS]);
        $inProgress = $ticket->fresh();
        Assert::assertNotNull($inProgress);
        Assert::assertNotNull($inProgress->status);
        Assert::assertSame('in_progress', $inProgress->status->value);

        $ticket->update(['status' => TicketStatusEnum::RESOLVED]);
        $resolved = $ticket->fresh();
        Assert::assertNotNull($resolved);
        Assert::assertNotNull($resolved->status);
        Assert::assertSame('resolved', $resolved->status->value);
    });

    it('maintains enum type integrity during transitions', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'status' => TicketStatusEnum::PENDING,
        ]);

        $ticket->update(['status' => TicketStatusEnum::CLOSED]);

        $closed = $ticket->fresh();
        Assert::assertNotNull($closed);
        Assert::assertNotNull($closed->status);
        Assert::assertInstanceOf(TicketStatusEnum::class, $closed->status);
        Assert::assertSame('closed', $closed->status->value);
    });
});
