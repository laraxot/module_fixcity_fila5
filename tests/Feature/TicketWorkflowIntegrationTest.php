<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature;

use InvalidArgumentException;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\NotificationService;
use Modules\Fixcity\Services\TicketService;
use Modules\Fixcity\Services\WorkflowService;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(\Modules\Fixcity\Tests\TestCase::class);

beforeEach(function (): void {
    /** @var \Modules\Fixcity\Tests\TestCase $this */
    $this->workflowService = app(WorkflowService::class);
    $this->ticketService = app(TicketService::class);
    $this->notificationService = app(NotificationService::class);
});

describe('Ticket Workflow Integration', function (): void {
    test('_complete_ticket_workflow', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        Assert::assertNotNull($this->workflowService);
        Assert::assertNotNull($this->ticketService);
        Assert::assertNotNull($this->notificationService);
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Act - Draft -> Pending
        $result1 = $this->workflow()->submitForReview($ticket);
        Assert::assertTrue($result1);
        Assert::assertEquals('pending', $ticket->fresh()?->status?->value);

        // Act - Pending -> Assigned
        $result2 = $this->workflow()->assignTicket($ticket, $assignee);
        Assert::assertTrue($result2);
        Assert::assertEquals('assigned', $ticket->fresh()?->status?->value);
        Assert::assertEquals($assignee->id, $ticket->fresh()?->getAttribute('assigned_to'));

        // Act - Assigned -> In Progress
        $result3 = $this->workflow()->startWork($ticket);
        Assert::assertTrue($result3);
        Assert::assertEquals('in_progress', $ticket->fresh()?->status?->value);

        // Act - In Progress -> Review
        $result4 = $this->workflow()->submitForApproval($ticket);
        Assert::assertTrue($result4);
        Assert::assertEquals('review', $ticket->fresh()?->status?->value);

        // Act - Review -> Approved
        $result5 = $this->workflow()->approveTicket($ticket, $approver);
        Assert::assertTrue($result5);
        Assert::assertEquals('approved', $ticket->fresh()?->status?->value);
        Assert::assertEquals($approver->id, $ticket->fresh()?->getAttribute('approved_by'));

        // Act - Approved -> Resolved
        $result6 = $this->workflow()->resolveTicket($ticket, $assignee);
        Assert::assertTrue($result6);
        Assert::assertEquals('resolved', $ticket->fresh()?->status?->value);
        Assert::assertEquals($assignee->id, $ticket->fresh()?->getAttribute('resolved_by'));

        // Act - Resolved -> Closed
        $result7 = $this->ticketService()->closeTicket($ticket, $assignee);
        Assert::assertTrue($result7);
        Assert::assertEquals('closed', $ticket->fresh()?->status?->value);
        Assert::assertEquals($assignee->id, $ticket->fresh()?->getAttribute('closed_by'));

        // Assert - Verifica stato finale
        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
            'approved_by' => $approver->id,
            'resolved_by' => $assignee->id,
            'closed_by' => $assignee->id,
        ]);
    });

    test('_ticket_workflow_with_rejection', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        /** @var \Modules\Fixcity\Services\WorkflowService $workflowService */
        $workflowService = $this->workflowService;
        Assert::assertNotNull($workflowService);
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Workflow fino a review
        $workflowService->submitForReview($ticket);
        $workflowService->assignTicket($ticket, $assignee);
        $workflowService->startWork($ticket);
        $workflowService->submitForApproval($ticket);

        // Act - Review -> Rejected
        $result = $workflowService->rejectTicket($ticket, $approver, 'Insufficient information');
        Assert::assertTrue($result);
        Assert::assertEquals('rejected', $ticket->fresh()?->status?->value);
        Assert::assertEquals('Insufficient information', $ticket->fresh()?->getAttribute('rejection_reason'));

        // Act - Rejected -> Draft (per correzioni)
        $result2 = $workflowService->submitForReview($ticket);
        Assert::assertTrue($result2);
        Assert::assertEquals('pending', $ticket->fresh()?->status?->value);
    });

    test('_ticket_workflow_with_escalation', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        Assert::assertNotNull($this->workflowService);
        Assert::assertNotNull($this->ticketService);
        Assert::assertNotNull($this->notificationService);
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'pending',
        ]);

        // Act - Escalation
        $result = $this->workflow()->escalateTicket($ticket, $creator, 'High priority issue');
        Assert::assertTrue($result);
        Assert::assertEquals('escalated', $ticket->fresh()?->status?->value);
        Assert::assertEquals('High priority issue', $ticket->fresh()?->getAttribute('escalation_reason'));

        // Act - De-escalation -> Assigned
        $result2 = $this->workflow()->assignTicket($ticket, $assignee);
        Assert::assertTrue($result2);
        Assert::assertEquals('assigned', $ticket->fresh()?->status?->value);
    });

    test('_ticket_workflow_with_return_to_work', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $approver = UserFactory::new()->createOne(['role' => 'approver']);

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Workflow fino a review
        $this->workflow()->submitForReview($ticket);
        $this->workflow()->assignTicket($ticket, $assignee);
        $this->workflow()->startWork($ticket);
        $this->workflow()->submitForApproval($ticket);

        // Act - Review -> Return to Work
        $result = $this->workflow()->returnToWork($ticket, $approver, 'Additional work required');
        Assert::assertTrue($result);
        Assert::assertEquals('in_progress', $ticket->fresh()?->status?->value);
        Assert::assertEquals('Additional work required', $ticket->fresh()?->getAttribute('return_reason'));

        // Act - Continue workflow
        $result2 = $this->workflow()->submitForApproval($ticket);
        Assert::assertTrue($result2);
        Assert::assertEquals('review', $ticket->fresh()?->status?->value);
    });

    test('_ticket_workflow_with_comments', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        $workflowService = $this->workflow();
        /** @var \Modules\Fixcity\Services\TicketService $ticketService */
        $ticketService = $this->ticketService();
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Act - Add comment
        $result = $ticketService->addComment($ticket, 'Initial comment', $creator);
        Assert::assertTrue($result);

        // Assert - Comment exists
        $this->assertDatabaseHasRow('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $creator->id,
            'comment' => 'Initial comment',
        ]);

        // Continue workflow
        $workflowService->submitForReview($ticket);
        $workflowService->assignTicket($ticket, $assignee);

        // Add another comment
        $result2 = $ticketService->addComment($ticket, 'Work in progress', $assignee);
        Assert::assertTrue($result2);

        // Assert - Both comments exist
        $this->assertDatabaseCountRow('ticket_comments', 2);
    });

    test('_ticket_workflow_performance', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        Assert::assertNotNull($this->workflowService);
        Assert::assertNotNull($this->ticketService);
        Assert::assertNotNull($this->notificationService);
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

        $this->workflow()->submitForReview($ticket);
        $this->workflow()->assignTicket($ticket, $assignee);
        $this->workflow()->startWork($ticket);
        $this->workflow()->submitForApproval($ticket);
        $this->workflow()->approveTicket($ticket, $approver);
        $this->workflow()->resolveTicket($ticket, $assignee);
        $this->ticketService()->closeTicket($ticket, $assignee);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert - Performance within limits
        Assert::assertLessThan(2.0, $executionTime, 'Workflow execution should complete within 2 seconds');
        Assert::assertEquals('closed', $ticket->fresh()?->status?->value);
    });

    test('_ticket_workflow_concurrent_updates', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        Assert::assertNotNull($this->workflowService);
        Assert::assertNotNull($this->ticketService);
        Assert::assertNotNull($this->notificationService);
        // Arrange
        $ticket = TicketFactory::new()->createOne(['status' => 'pending']);
        $assignee1 = UserFactory::new()->createOne();
        $assignee2 = UserFactory::new()->createOne();

        // Act - Simulate concurrent assignment attempts
        $result1 = $this->workflow()->assignTicket($ticket, $assignee1);
        $result2 = $this->workflow()->assignTicket($ticket, $assignee2);

        // Assert - Only one assignment should succeed
        Assert::assertTrue($result1);
        Assert::assertFalse($result2); // Second assignment should fail

        $finalTicket = $ticket->fresh();
        Assert::assertNotNull($finalTicket);
        Assert::assertEquals('assigned', $finalTicket->status?->value);
        Assert::assertEquals($assignee1->id, $finalTicket->getAttribute('assigned_to'));
    });

    test('_ticket_workflow_audit_trail', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        Assert::assertNotNull($this->workflowService);
        Assert::assertNotNull($this->ticketService);
        Assert::assertNotNull($this->notificationService);
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Act - Execute workflow steps
        $this->workflow()->submitForReview($ticket);
        $this->workflow()->assignTicket($ticket, $assignee);
        $this->workflow()->startWork($ticket);

        // Assert - Audit trail exists
        $this->assertDatabaseHasRow('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'status_changed',
            'old_status' => 'draft',
            'new_status' => 'pending',
        ]);
        $this->assertDatabaseHasRow('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'assigned',
            'assigned_to' => $assignee->id,
        ]);

        $this->assertDatabaseHasRow('ticket_activities', [
            'ticket_id' => $ticket->id,
            'action' => 'status_changed',
            'old_status' => 'assigned',
            'new_status' => 'in_progress',
        ]);
    });

    test('_ticket_workflow_notifications', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        Assert::assertNotNull($this->workflowService);
        Assert::assertNotNull($this->ticketService);
        Assert::assertNotNull($this->notificationService);
        // Arrange
        $creator = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $ticket = TicketFactory::new()->createOne([
            'created_by' => $creator->id,
            'status' => 'draft',
        ]);

        // Mock notification service
        $this->mockService(NotificationService::class, function (mixed $mock) use ($ticket, $assignee) {
            /** @var \Mockery\MockInterface $mock */
            /** @var \Mockery\Expectation $statusExpectation */
            $statusExpectation = $mock->shouldReceive('sendTicketStatusChanged');
            $statusExpectation->with($ticket, 'pending')->once();

            /** @var \Mockery\Expectation $assignedExpectation */
            $assignedExpectation = $mock->shouldReceive('sendTicketAssigned');
            $assignedExpectation->with($ticket, $assignee)->once();
        });

        // Act - Execute workflow with notifications
        $this->workflow()->submitForReview($ticket);
        $this->workflow()->assignTicket($ticket, $assignee);

        // Assert - Notifications were sent via mock
        Assert::assertEquals('assigned', $ticket->fresh()?->status?->value);
    });

    test('_ticket_workflow_edge_cases', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        // Arrange
        $ticket = TicketFactory::new()->createOne(['status' => 'closed']);
        $user = UserFactory::new()->createOne();

        // Act & Assert - Cannot perform invalid transitions
        $this->expectApplicationException(InvalidArgumentException::class);
        $this->workflow()->submitForReview($ticket);

        // Reset ticket status
        $ticket->update(['status' => 'draft']);

        // Act & Assert - Cannot assign without pending status
        $this->expectApplicationException(InvalidArgumentException::class);
        $this->workflow()->assignTicket($ticket, $user);
    });

    test('_ticket_workflow_data_integrity', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
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
        Assert::assertNotNull($this->workflowService);
        $this->workflow()->submitForReview($ticket);
        $this->workflow()->assignTicket($ticket, $assignee);
        $this->workflow()->startWork($ticket);

        // Assert - Data integrity maintained
        $freshTicket = $ticket->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertEquals($creator->id, $freshTicket->created_by);
        Assert::assertEquals($assignee->id, $freshTicket->getAttribute('assigned_to'));
        Assert::assertEquals('low', $freshTicket->priority?->value);
        Assert::assertEquals('general', $freshTicket->getAttribute('category'));
        Assert::assertEquals('in_progress', $freshTicket->status?->value);
    });
});
