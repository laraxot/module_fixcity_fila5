<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use ValueError;
use Modules\Fixcity\Actions\ChangeStatus;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeStatusTest extends TestCase
{
    use RefreshDatabase;

    private ChangeStatus $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ChangeStatus();
    }

    /** @test */
    public function it_changes_ticket_status_successfully(): void
    {
        // Arrange
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::OPEN]);
        $newStatus = 'resolved';
        $reason = 'Issue has been resolved by the team';

        // Act
        $this->action->execute($ticket, $newStatus, $reason);

        // Assert
        $ticket->refresh();
        expect($ticket->status)->toBe(TicketStatusEnum::RESOLVED)
            ->and($ticket->status_reason)->toBe($reason);
    }

    /** @test */
    public function it_handles_status_transition_to_urgent(): void
    {
        // Arrange
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::OPEN]);
        $newStatus = 'urgent';
        $reason = 'Customer reported critical issue';

        // Act
        $this->action->execute($ticket, $newStatus, $reason);

        // Assert
        $ticket->refresh();
        expect($ticket->status)->toBe(TicketStatusEnum::URGENT)
            ->and($ticket->status_reason)->toBe($reason);
    }

    /** @test */
    public function it_handles_status_transition_to_closed(): void
    {
        // Arrange
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::RESOLVED]);
        $newStatus = 'closed';
        $reason = 'Ticket completed successfully';

        // Act
        $this->action->execute($ticket, $newStatus, $reason);

        // Assert
        $ticket->refresh();
        expect($ticket->status)->toBe(TicketStatusEnum::CLOSED)
            ->and($ticket->status_reason)->toBe($reason);
    }

    /** @test */
    public function it_preserves_existing_ticket_data_during_status_change(): void
    {
        // Arrange
        $originalData = [
            'title' => 'Original Title',
            'description' => 'Original Description',
            'priority' => 'high',
            'assigned_to' => 'user123'
        ];
        
        $ticket = Ticket::factory()->create($originalData);
        $newStatus = 'in_progress';
        $reason = 'Work started on this ticket';

        // Act
        $this->action->execute($ticket, $newStatus, $reason);

        // Assert
        $ticket->refresh();
        expect($ticket->title)->toBe($originalData['title'])
            ->and($ticket->description)->toBe($originalData['description'])
            ->and($ticket->priority)->toBe($originalData['priority'])
            ->and($ticket->assigned_to)->toBe($originalData['assigned_to'])
            ->and($ticket->status)->toBe(TicketStatusEnum::IN_PROGRESS)
            ->and($ticket->status_reason)->toBe($reason);
    }

    /** @test */
    public function it_handles_invalid_status_gracefully(): void
    {
        // Arrange
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::OPEN]);
        $invalidStatus = 'invalid_status';
        $reason = 'Testing invalid status handling';

        // Act & Assert
        expect(fn() => $this->action->execute($ticket, $invalidStatus, $reason))
            ->toThrow(ValueError::class);
    }

    /** @test */
    public function it_updates_ticket_timestamps_when_status_changes(): void
    {
        // Arrange
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::OPEN]);
        $originalUpdatedAt = $ticket->updated_at;
        
        // Wait a moment to ensure timestamp difference
        sleep(1);
        
        $newStatus = 'resolved';
        $reason = 'Status change test';

        // Act
        $this->action->execute($ticket, $newStatus, $reason);

        // Assert
        $ticket->refresh();
        expect($ticket->updated_at->timestamp)->toBeGreaterThan($originalUpdatedAt->timestamp);
    }

    /** @test */
    public function it_handles_empty_reason_string(): void
    {
        // Arrange
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::OPEN]);
        $newStatus = 'pending';
        $emptyReason = '';

        // Act
        $this->action->execute($ticket, $newStatus, $emptyReason);

        // Assert
        $ticket->refresh();
        expect($ticket->status)->toBe(TicketStatusEnum::PENDING)
            ->and($ticket->status_reason)->toBe('');
    }

    /** @test */
    public function it_handles_long_reason_strings(): void
    {
        // Arrange
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::OPEN]);
        $newStatus = 'on_hold';
        $longReason = str_repeat('A very long reason that tests the handling of extended text content. ', 10);

        // Act
        $this->action->execute($ticket, $newStatus, $longReason);

        // Assert
        $ticket->refresh();
        expect($ticket->status)->toBe(TicketStatusEnum::ON_HOLD)
            ->and($ticket->status_reason)->toBe($longReason);
    }
}
