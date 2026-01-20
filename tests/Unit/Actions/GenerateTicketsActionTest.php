<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Modules\Fixcity\Actions\GenerateTicketsAction;
use Modules\Fixcity\Models\Ticket;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\PendingBatch;
use Illuminate\Bus\Batch;
use Mockery;

class GenerateTicketsActionTest extends TestCase
{
    use RefreshDatabase;

    private GenerateTicketsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GenerateTicketsAction();
    }

    /** @test */
    public function it_generates_single_ticket_successfully(): void
    {
        // Arrange
        Bus::fake();
        $count = 1;

        // Act
        $this->action->execute($count);

        // Assert
        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->jobs->count() === 1;
        });
    }

    /** @test */
    public function it_generates_multiple_tickets_with_correct_count(): void
    {
        // Arrange
        Bus::fake();
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        Bus::assertBatched(function (PendingBatch $batch) use ($count) {
            return $batch->jobs->count() === $count;
        });
    }

    /** @test */
    public function it_creates_tickets_with_valid_states(): void
    {
        // Arrange
        $validStates = ['open', 'urgent', 'resolved'];
        $count = 10;

        // Act
        $this->action->execute($count);

        // Assert
        // Verify that all created tickets have valid states
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        foreach ($tickets as $ticket) {
            expect($ticket->status->value)->toBeIn($validStates);
        }
    }

    /** @test */
    public function it_handles_zero_count_gracefully(): void
    {
        // Arrange
        Bus::fake();
        $count = 0;

        // Act
        $this->action->execute($count);

        // Assert
        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->jobs->count() === 0;
        });
    }

    /** @test */
    public function it_handles_large_count_efficiently(): void
    {
        // Arrange
        Bus::fake();
        $count = 100;

        // Act
        $this->action->execute($count);

        // Assert
        Bus::assertBatched(function (PendingBatch $batch) use ($count) {
            return $batch->jobs->count() === $count;
        });
    }

    /** @test */
    public function it_creates_tickets_with_different_priorities(): void
    {
        // Arrange
        $count = 20;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that tickets have different priorities (assuming factory creates varied data)
        $priorities = $tickets->pluck('priority')->unique();
        expect($priorities->count())->toBeGreaterThan(1);
    }

    /** @test */
    public function it_creates_tickets_with_assigned_users(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that some tickets have assigned users
        $assignedTickets = $tickets->whereNotNull('assigned_to');
        expect($assignedTickets->count())->toBeGreaterThan(0);
    }

    /** @test */
    public function it_creates_tickets_with_categories(): void
    {
        // Arrange
        $count = 10;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that tickets have categories
        $categorizedTickets = $tickets->whereNotNull('category');
        expect($categorizedTickets->count())->toBeGreaterThan(0);
    }

    /** @test */
    public function it_creates_tickets_with_descriptions(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that all tickets have descriptions
        foreach ($tickets as $ticket) {
            expect($ticket->description)->not->toBeEmpty();
        }
    }

    /** @test */
    public function it_creates_tickets_with_titles(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that all tickets have titles
        foreach ($tickets as $ticket) {
            expect($ticket->title)->not->toBeEmpty();
        }
    }

    /** @test */
    public function it_creates_tickets_with_creation_timestamps(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that all tickets have creation timestamps
        foreach ($tickets as $ticket) {
            expect($ticket->created_at)->not->toBeNull()
                ->and($ticket->updated_at)->not->toBeNull();
        }
    }

    /** @test */
    public function it_creates_tickets_with_unique_identifiers(): void
    {
        // Arrange
        $count = 10;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that all tickets have unique IDs
        $ids = $tickets->pluck('id');
        expect($ids->unique()->count())->toBe($count);
    }

    /** @test */
    public function it_creates_tickets_with_customer_information(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that tickets have customer information
        $customerTickets = $tickets->whereNotNull('customer_name');
        expect($customerTickets->count())->toBeGreaterThan(0);
    }

    /** @test */
    public function it_creates_tickets_with_location_data(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that some tickets have location data
        $locationTickets = $tickets->whereNotNull('location');
        expect($locationTickets->count())->toBeGreaterThan(0);
    }

    /** @test */
    public function it_creates_tickets_with_estimated_completion_times(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that some tickets have estimated completion times
        $estimatedTickets = $tickets->whereNotNull('estimated_completion_time');
        expect($estimatedTickets->count())->toBeGreaterThan(0);
    }

    /** @test */
    public function it_creates_tickets_with_attachments_support(): void
    {
        // Arrange
        $count = 5;

        // Act
        $this->action->execute($count);

        // Assert
        $tickets = Ticket::all();
        expect($tickets)->toHaveCount($count);
        
        // Verify that tickets support attachments (check if media relationship exists)
        foreach ($tickets as $ticket) {
            expect(method_exists($ticket, 'media'))->toBeTrue();
        }
    }
}
