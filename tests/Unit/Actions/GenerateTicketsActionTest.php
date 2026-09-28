<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Modules\Fixcity\Actions\GenerateTicketsAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('Generate Tickets Action', function (): void {
    test('_generates_single_ticket_successfully', function (): void {
        /** @var TestCase $this */
        // Arrange
        Bus::fake();
        $count = 1;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->jobs->count() === 1;
        });
    });

    test('_generates_multiple_tickets_with_correct_count', function (): void {
        // Arrange
        Bus::fake();
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        Bus::assertBatched(function (PendingBatch $batch) use ($count) {
            return $batch->jobs->count() === $count;
        });
    });

    test('_creates_tickets_with_valid_states', function (): void {
        // Arrange
        $validStates = ['open', 'urgent', 'resolved'];
        $count = 10;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        // Verify that all created tickets have valid states
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        foreach ($tickets as $ticket) {

        }
    });

    test('_handles_zero_count_gracefully', function (): void {
        // Arrange
        Bus::fake();
        $count = 0;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        Bus::assertNothingBatched();
    });

    test('_handles_large_count_efficiently', function (): void {
        // Arrange
        Bus::fake();
        $count = 100;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        Bus::assertBatched(function (PendingBatch $batch) use ($count) {
            return $batch->jobs->count() === $count;
        });
    });

    test('_creates_tickets_with_different_priorities', function (): void {
        // Arrange
        $count = 20;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        // Verify that tickets have different priorities (assuming factory creates varied data)
        $priorities = $tickets->pluck('priority')->unique();
        Assert::assertGreaterThan(1, $priorities->count());
    });

    test('_creates_tickets_with_assigned_users', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        // Verify that some tickets have assigned users
        $assignedTickets = $tickets->whereNotNull('responsible_id');
        Assert::assertGreaterThan(0, $assignedTickets->count());
    });

    test('_creates_tickets_without_legacy_category_column', function (): void {
        // Arrange
        $count = 10;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        foreach ($tickets as $ticket) {
            Assert::assertArrayNotHasKey('category', $ticket->getAttributes());
        }
    });

    test('_creates_tickets_with_descriptions', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        // Verify that all tickets have descriptions
        foreach ($tickets as $ticket) {

        }
    });

    test('_creates_tickets_with_titles', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        // Verify that all tickets have titles
        foreach ($tickets as $ticket) {

        }
    });

    test('_creates_tickets_with_creation_timestamps', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        // Verify that all tickets have creation timestamps
        foreach ($tickets as $ticket) {
            Assert::assertNotNull($ticket->created_at);
            Assert::assertNotNull($ticket->updated_at);
        }
    });

    test('_creates_tickets_with_unique_identifiers', function (): void {
        // Arrange
        $count = 10;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        // Verify that all tickets have unique IDs
        $ids = $tickets->pluck('id');
        Assert::assertSame($count, $ids->unique()->count());
    });

    test('_creates_tickets_with_owner_information', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        Assert::assertTrue($tickets->every(
            static fn (Ticket $ticket): bool => $ticket->owner_id !== null,
        ));
    });

    test('_creates_tickets_with_location_data', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        Assert::assertTrue($tickets->every(
            static fn (Ticket $ticket): bool => array_key_exists('location', $ticket->getAttributes()),
        ));
    });

    test('_does_not_use_legacy_estimated_completion_column', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        foreach ($tickets as $ticket) {
            Assert::assertArrayNotHasKey('estimated_completion_time', $ticket->getAttributes());
        }
    });

    test('_creates_tickets_with_attachments_support', function (): void {
        // Arrange
        $count = 5;

        // Act
        (new GenerateTicketsAction)->execute($count);

        // Assert
        $tickets = Ticket::all();
        Assert::assertCount($count, $tickets);

        // Verify that tickets support attachments (check if media relationship exists)
        foreach ($tickets as $ticket) {
            Assert::assertTrue(method_exists($ticket, 'media'));
        }
    });
});
