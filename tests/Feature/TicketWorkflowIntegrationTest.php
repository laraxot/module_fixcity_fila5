<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\NotificationService;
use Modules\Fixcity\Services\TicketService;
use Modules\Fixcity\Services\WorkflowService;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Tests\TestCase;

class TicketWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected WorkflowService $workflowService;

    protected TicketService $ticketService;

    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workflowService = app(WorkflowService::class);
        $this->ticketService = app(TicketService::class);
        $this->notificationService = app(NotificationService::class);
    }

    public function test_complete_ticket_workflow(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Act - Draft -> Pending
        $result1 = $this->workflowService->submitForReview($ticket);
        $this->assertTrue($result1);
        $this->assertEquals('pending', $ticket->fresh()?->status?->value);

        // Act - Pending -> Assigned
        $result2 = $this->workflowService->assignTicket($ticket, $assignee);
        $this->assertTrue($result2);
        $this->assertEquals('assigned', $ticket->fresh()?->status?->value);
        $this->assertEquals($assignee->id, $ticket->fresh()?->getAttribute('assigned_to'));

        // Act - Assigned -> In Progress
        $result3 = $this->workflowService->startWork($ticket);
        $this->assertTrue($result3);
        $this->assertEquals('in_progress', $ticket->fresh()?->status?->value);

        // Act - In Progress -> Review
        $result4 = $this->workflowService->submitForApproval($ticket);
        $this->assertTrue($result4);
        $this->assertEquals('review', $ticket->fresh()?->status?->value);

        // Act - Review -> Approved
        $result5 = $this->workflowService->approveTicket($ticket, $approver);
        $this->assertTrue($result5);
        $this->assertEquals('approved', $ticket->fresh()?->status?->value);
        $this->assertEquals($approver->id, $ticket->fresh()?->getAttribute('approved_by'));

        // Act - Approved -> Resolved
        $result6 = $this->workflowService->resolveTicket($ticket, $assignee);
        $this->assertTrue($result6);
        $this->assertEquals('resolved', $ticket->fresh()?->status?->value);
        $this->assertEquals($assignee->id, $ticket->fresh()?->getAttribute('resolved_by'));

        // Act - Resolved -> Closed
        $result7 = $this->ticketService->closeTicket($ticket, $assignee);
        $this->assertTrue($result7);
        $this->assertEquals('closed', $ticket->fresh()?->status?->value);
        $this->assertEquals($assignee->id, $ticket->fresh()?->getAttribute('closed_by'));

        // Assert - Verifica stato finale
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
            'approved_by' => $approver->id,
            'resolved_by' => $assignee->id,
            'closed_by' => $assignee->id,
        ]);
    }

    public function test_ticket_workflow_with_rejection(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Workflow fino a review
        $this->workflowService->submitForReview($ticket);
        $this->workflowService->assignTicket($ticket, $assignee);
        $this->workflowService->startWork($ticket);
        $this->workflowService->submitForApproval($ticket);

        // Act - Review -> Rejected
        $result = $this->workflowService->rejectTicket($ticket, $approver, 'Insufficient information');
        $this->assertTrue($result);
        $this->assertEquals('rejected', $ticket->fresh()?->status?->value);
        $this->assertEquals('Insufficient information', $ticket->fresh()?->getAttribute('rejection_reason'));

        // Act - Rejected -> Draft (per correzioni)
        $result2 = $this->workflowService->submitForReview($ticket);
        $this->assertTrue($result2);
        $this->assertEquals('pending', $ticket->fresh()?->status?->value);
    }

    public function test_ticket_workflow_with_escalation(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'pending',
        ]);

        // Act - Escalation
        $result = $this->workflowService->escalateTicket($ticket, $creator, 'High priority issue');
        $this->assertTrue($result);
        $this->assertEquals('escalated', $ticket->fresh()?->status?->value);
        $this->assertEquals('High priority issue', $ticket->fresh()?->getAttribute('escalation_reason'));

        // Act - De-escalation -> Assigned
        $result2 = $this->workflowService->assignTicket($ticket, $assignee);
        $this->assertTrue($result2);
        $this->assertEquals('assigned', $ticket->fresh()?->status?->value);
    }

    public function test_ticket_workflow_with_return_to_work(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Workflow fino a review
        $this->workflowService->submitForReview($ticket);
        $this->workflowService->assignTicket($ticket, $assignee);
        $this->workflowService->startWork($ticket);
        $this->workflowService->submitForApproval($ticket);

        // Act - Review -> Return to Work
        $result = $this->workflowService->returnToWork($ticket, $approver, 'Additional work required');
        $this->assertTrue($result);
        $this->assertEquals('in_progress', $ticket->fresh()?->status?->value);
        $this->assertEquals('Additional work required', $ticket->fresh()?->getAttribute('return_reason'));

        // Act - Continue workflow
        $result2 = $this->workflowService->submitForApproval($ticket);
        $this->assertTrue($result2);
        $this->assertEquals('review', $ticket->fresh()?->status?->value);
    }

    public function test_ticket_workflow_with_comments(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Act - Add comment
        $result = $this->ticketService->addComment($ticket, 'Initial comment', $creator);
        $this->assertTrue($result);

        // Assert - Comment exists
        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $creator->id,
            'comment' => 'Initial comment',
        ]);

        // Continue workflow
        $this->workflowService->submitForReview($ticket);
        $this->workflowService->assignTicket($ticket, $assignee);

        // Add another comment
        $result2 = $this->ticketService->addComment($ticket, 'Work in progress', $assignee);
        $this->assertTrue($result2);

        // Assert - Both comments exist
        $this->assertDatabaseCount('ticket_comments', 2);
    }

    public function test_ticket_workflow_performance(): void
    {
        // Arrange
        $startTime = microtime(true);
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        // Act - Complete workflow
        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        $this->workflowService->submitForReview($ticket);
        $this->workflowService->assignTicket($ticket, $assignee);
        $this->workflowService->startWork($ticket);
        $this->workflowService->submitForApproval($ticket);
        $this->workflowService->approveTicket($ticket, $approver);
        $this->workflowService->resolveTicket($ticket, $assignee);
        $this->ticketService->closeTicket($ticket, $assignee);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert - Performance within limits
        $this->assertLessThan(2.0, $executionTime, 'Workflow execution should complete within 2 seconds');
        $this->assertEquals('closed', $ticket->fresh()?->status?->value);
    }

    public function test_ticket_workflow_concurrent_updates(): void
    {
        // Arrange
        $ticket = TicketFactory::new()->createOne(['status' => 'pending']);
        $assignee1 = UserFactory::new()->createOne();
        $assignee2 = UserFactory::new()->createOne();

        // Act - Simulate concurrent assignment attempts
        $result1 = $this->workflowService->assignTicket($ticket, $assignee1);
        $result2 = $this->workflowService->assignTicket($ticket, $assignee2);

        // Assert - Only one assignment should succeed
        $this->assertTrue($result1);
        $this->assertFalse($result2); // Second assignment should fail

        $finalTicket = $ticket->fresh();
        $this->assertNotNull($finalTicket);
        $this->assertEquals('assigned', $finalTicket->status?->value);
        $this->assertEquals($assignee1->id, $finalTicket->getAttribute('assigned_to'));
    }

    public function test_ticket_workflow_audit_trail(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Act - Execute workflow steps
        $this->workflowService->submitForReview($ticket);
        $this->workflowService->assignTicket($ticket, $assignee);
        $this->workflowService->startWork($ticket);

        // Assert - Audit trail exists
        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'status_changed',
            'old_status' => 'draft',
            'new_status' => 'pending',
        ]);

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'assigned',
            'assigned_to' => $assignee->id,
        ]);

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'status_changed',
            'old_status' => 'assigned',
            'new_status' => 'in_progress',
        ]);
    }

    public function test_ticket_workflow_notifications(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Mock notification service
        $this->mock(NotificationService::class, function (mixed $mock) use ($ticket, $assignee) {
            /** @var \Mockery\MockInterface $mock */
            /** @var \Mockery\Expectation $statusExpectation */
            $statusExpectation = $mock->shouldReceive('sendTicketStatusChanged');
            $statusExpectation->with($ticket, 'pending')->once();

            /** @var \Mockery\Expectation $assignedExpectation */
            $assignedExpectation = $mock->shouldReceive('sendTicketAssigned');
            $assignedExpectation->with($ticket, $assignee)->once();
        });

        // Act - Execute workflow with notifications
        $this->workflowService->submitForReview($ticket);
        $this->workflowService->assignTicket($ticket, $assignee);

        // Assert - Notifications were sent via mock
        $this->assertEquals('assigned', $ticket->fresh()?->status?->value);
    }

    public function test_ticket_workflow_edge_cases(): void
    {
        // Arrange
        $ticket = TicketFactory::new()->createOne(['status' => 'closed']);
        $user = UserFactory::new()->createOne();

        // Act & Assert - Cannot perform invalid transitions
        $this->expectException(InvalidArgumentException::class);
        $this->workflowService->submitForReview($ticket);

        // Reset ticket status
        $ticket->update(['status' => 'draft']);

        // Act & Assert - Cannot assign without pending status
        $this->expectException(InvalidArgumentException::class);
        $this->workflowService->assignTicket($ticket, $user);
    }

    public function test_ticket_workflow_data_integrity(): void
    {
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
            'priority' => 'low',
            'category' => 'general',
        ]);

        // Act - Execute workflow
        $this->workflowService->submitForReview($ticket);
        $this->workflowService->assignTicket($ticket, $assignee);
        $this->workflowService->startWork($ticket);

        // Assert - Data integrity maintained
        $freshTicket = $ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertEquals($creator->id, $freshTicket->created_by);
        $this->assertEquals($assignee->id, $freshTicket->getAttribute('assigned_to'));
        $this->assertEquals('low', $freshTicket->priority?->value);
        $this->assertEquals('general', $freshTicket->getAttribute('category'));
        $this->assertEquals('in_progress', $freshTicket->status?->value);
    }
}
