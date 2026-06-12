<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Services\WorkflowService;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Tests\TestCase;

/**
 * Test WorkflowService methods.
 *
 * @group WorkflowService
 */
class WorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WorkflowService $service;

    protected User $user;

    protected Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WorkflowService;
        $this->user = UserFactory::new()->createOne();
        $this->ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }

    public function test_allows_transition_from_pending_to_in_review(): void
    {
        $result = $this->service->canTransitionTo($this->ticket, 'in_review');

        $this->assertTrue($result);
    }

    public function test_allows_transition_from_pending_to_in_progress(): void
    {
        $result = $this->service->canTransitionTo($this->ticket, 'in_progress');

        $this->assertTrue($result);
    }

    public function test_allows_transition_from_in_review_to_in_progress(): void
    {
        $this->ticket->update(['status' => 'in_review']);
        $result = $this->service->canTransitionTo($this->ticket, 'in_progress');

        $this->assertTrue($result);
    }

    public function test_allows_transition_from_in_progress_to_on_hold(): void
    {
        $this->ticket->update(['status' => 'in_progress']);
        $result = $this->service->canTransitionTo($this->ticket, 'on_hold');

        $this->assertTrue($result);
    }

    public function test_allows_transition_from_in_progress_to_resolved(): void
    {
        $this->ticket->update(['status' => 'in_progress']);
        $result = $this->service->canTransitionTo($this->ticket, 'resolved');

        $this->assertTrue($result);
    }

    public function test_allows_transition_from_resolved_to_closed(): void
    {
        $this->ticket->update(['status' => 'resolved']);
        $result = $this->service->canTransitionTo($this->ticket, 'closed');

        $this->assertTrue($result);
    }

    public function test_allows_transition_from_closed_to_reopened(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $result = $this->service->canTransitionTo($this->ticket, 'reopened');

        $this->assertTrue($result);
    }

    public function test_prevents_invalid_transitions(): void
    {
        $result = $this->service->canTransitionTo($this->ticket, 'invalid_status');

        $this->assertFalse($result);
    }

    public function test_prevents_transition_from_pending_to_resolved(): void
    {
        $result = $this->service->canTransitionTo($this->ticket, 'resolved');

        $this->assertFalse($result);
    }

    public function test_prevents_transition_from_pending_to_closed(): void
    {
        $result = $this->service->canTransitionTo($this->ticket, 'closed');

        $this->assertFalse($result);
    }

    public function test_returns_correct_transitions_for_pending_status(): void
    {
        $transitions = $this->service->getAvailableTransitions($this->ticket);

        $this->assertContains('in_review', $transitions);
        $this->assertContains('in_progress', $transitions);
        $this->assertNotContains('resolved', $transitions);
        $this->assertNotContains('closed', $transitions);
    }

    public function test_returns_correct_transitions_for_in_review_status(): void
    {
        $this->ticket->update(['status' => 'in_review']);
        $transitions = $this->service->getAvailableTransitions($this->ticket);

        $this->assertContains('in_progress', $transitions);
        $this->assertContains('on_hold', $transitions);
        $this->assertNotContains('pending', $transitions);
    }

    public function test_returns_correct_transitions_for_in_progress_status(): void
    {
        $this->ticket->update(['status' => 'in_progress']);
        $transitions = $this->service->getAvailableTransitions($this->ticket);

        $this->assertContains('on_hold', $transitions);
        $this->assertContains('resolved', $transitions);
        $this->assertNotContains('pending', $transitions);
    }

    public function test_returns_correct_transitions_for_resolved_status(): void
    {
        $this->ticket->update(['status' => 'resolved']);
        $transitions = $this->service->getAvailableTransitions($this->ticket);

        $this->assertContains('closed', $transitions);
        $this->assertNotContains('in_progress', $transitions);
    }

    public function test_returns_correct_transitions_for_closed_status(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $transitions = $this->service->getAvailableTransitions($this->ticket);

        $this->assertContains('reopened', $transitions);
        $this->assertNotContains('resolved', $transitions);
    }

    public function test_successfully_transitions_ticket_status(): void
    {
        $result = $this->service->transitionTo($this->ticket, 'in_review');

        $this->assertTrue($result);
        $freshTicket = $this->ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame('in_review', $freshTicket->status?->value);
    }

    public function test_creates_activity_log_for_transition(): void
    {
        $result = $this->service->transitionTo($this->ticket, 'in_review');

        $this->assertTrue($result);
        $this->assertNotEmpty($this->ticket->activities);
        /** @var TicketActivity $firstActivity */
        $firstActivity = $this->ticket->activities->first();
        $this->assertStringContainsString(
            'Status changed from pending to in_review',
            (string) $firstActivity->getAttribute('description')
        );
    }

    public function test_prevents_invalid_transitions_for_transitionTo(): void
    {
        $result = $this->service->transitionTo($this->ticket, 'invalid_status');

        $this->assertFalse($result);
        $freshTicket = $this->ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame('pending', $freshTicket->status?->value);
    }

    public function test_prevents_transition_from_pending_to_resolved_for_transitionTo(): void
    {
        $result = $this->service->transitionTo($this->ticket, 'resolved');

        $this->assertFalse($result);
        $freshTicket = $this->ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame('pending', $freshTicket->status?->value);
    }

    public function test_updates_ticket_timestamps_on_transition(): void
    {
        $oldUpdatedAt = $this->ticket->updated_at;
        sleep(1);

        $result = $this->service->transitionTo($this->ticket, 'in_review');

        $this->assertTrue($result);
        $freshTicket = $this->ticket->fresh();
        $this->assertNotNull($freshTicket);
        $updatedAt = $freshTicket->updated_at;
        $this->assertNotNull($updatedAt);
        $this->assertNotNull($oldUpdatedAt);
        $this->assertTrue($updatedAt->gt($oldUpdatedAt));
    }

    public function test_returns_workflow_rules_for_ticket_type(): void
    {
        $rules = $this->service->getWorkflowRules($this->ticket);

        $this->assertArrayHasKey('transitions', $rules);
        $this->assertArrayHasKey('constraints', $rules);
    }

    public function test_returns_different_rules_for_different_ticket_types(): void
    {
        $this->ticket->update(['type' => 'road_maintenance']);
        $roadRules = $this->service->getWorkflowRules($this->ticket);

        $this->ticket->update(['type' => 'public_lighting']);
        $lightingRules = $this->service->getWorkflowRules($this->ticket);

        $this->assertNotSame($roadRules, $lightingRules);
    }

    public function test_includes_priority_based_rules(): void
    {
        $this->ticket->update(['priority' => 'urgent']);
        $rules = $this->service->getWorkflowRules($this->ticket);

        /** @var array<string, mixed> $constraints */
        $constraints = $rules['constraints'];
        $this->assertArrayHasKey('urgent_priority', $constraints);
    }

    public function test_validates_transition_requirements(): void
    {
        $result = $this->service->validateTransition($this->ticket, 'in_progress');

        $this->assertTrue($result['valid']);
    }

    public function test_requires_assignee_for_in_progress_transition(): void
    {
        $result = $this->service->validateTransition($this->ticket, 'in_progress');

        $this->assertFalse($result['valid']);
        /** @var list<string> $errors */
        $errors = $result['errors'];
        $this->assertContains('Ticket must be assigned to proceed', $errors);
    }

    public function test_validates_transition_with_assignee(): void
    {
        $assignee = UserFactory::new()->createOne();
        $this->ticket->update(['responsible_id' => $assignee->id]);

        $result = $this->service->validateTransition($this->ticket, 'in_progress');

        $this->assertTrue($result['valid']);
    }

    public function test_requires_resolution_note_for_resolved_transition(): void
    {
        $this->ticket->update(['status' => 'in_progress']);
        $result = $this->service->validateTransition($this->ticket, 'resolved');

        $this->assertFalse($result['valid']);
        /** @var list<string> $resolutionErrors */
        $resolutionErrors = $result['errors'];
        $this->assertContains('Resolution note is required', $resolutionErrors);
    }

    public function test_validates_resolved_transition_with_note(): void
    {
        $this->ticket->update([
            'status' => 'in_progress',
            'resolution_note' => 'Issue has been resolved',
        ]);
        $result = $this->service->validateTransition($this->ticket, 'resolved');

        $this->assertTrue($result['valid']);
    }

    public function test_returns_transition_history_for_ticket(): void
    {
        $this->service->transitionTo($this->ticket, 'in_review');
        $this->service->transitionTo($this->ticket, 'in_progress');

        $history = $this->service->getTransitionHistory($this->ticket);

        $this->assertCount(2, $history);
        $first = $history->first();
        $this->assertNotNull($first);
        $this->assertSame('in_review', $first->getAttribute('to_status'));
        $last = $history->last();
        $this->assertNotNull($last);
        $this->assertSame('in_progress', $last->getAttribute('to_status'));
    }

    public function test_orders_transitions_by_timestamp(): void
    {
        $this->service->transitionTo($this->ticket, 'in_review');
        sleep(1);
        $this->service->transitionTo($this->ticket, 'in_progress');

        $history = $this->service->getTransitionHistory($this->ticket);

        $first = $history->first();
        $last = $history->last();
        $this->assertNotNull($first);
        $this->assertNotNull($last);
        $this->assertNotNull($first->created_at);
        $this->assertNotNull($last->created_at);
        $this->assertTrue($first->created_at->lt($last->created_at));
    }

    public function test_allows_reopening_closed_tickets(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $result = $this->service->canReopenTicket($this->ticket);

        $this->assertTrue($result);
    }

    public function test_prevents_reopening_non_closed_tickets(): void
    {
        $result = $this->service->canReopenTicket($this->ticket);

        $this->assertFalse($result);
    }

    public function test_prevents_reopening_tickets_closed_for_too_long(): void
    {
        $this->ticket->update([
            'status' => 'closed',
            'closed_at' => now()->subDays(31),
        ]);
        $result = $this->service->canReopenTicket($this->ticket);

        $this->assertFalse($result);
    }

    public function test_successfully_reopens_closed_ticket(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $result = $this->service->reopenTicket($this->ticket, 'Reopening for additional work');

        $this->assertTrue($result);
        $freshTicket = $this->ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame('reopened', $freshTicket->status?->value);
    }

    public function test_creates_activity_log_for_reopening(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $result = $this->service->reopenTicket($this->ticket, 'Reopening for additional work');

        $this->assertTrue($result);
        $lastActivity = $this->ticket->activities->last();
        $this->assertNotNull($lastActivity);
        $this->assertStringContainsString(
            'Ticket reopened',
            (string) $lastActivity->getAttribute('description')
        );
    }

    public function test_prevents_reopening_non_closed_tickets_for_reopen(): void
    {
        $result = $this->service->reopenTicket($this->ticket, 'Test reason');

        $this->assertFalse($result);
        $freshTicket = $this->ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame('pending', $freshTicket->status?->value);
    }

    public function test_returns_workflow_performance_metrics(): void
    {
        $metrics = $this->service->getWorkflowMetrics($this->ticket);

        $this->assertArrayHasKey('avg_resolution_time', $metrics);
        $this->assertArrayHasKey('transition_count', $metrics);
        $this->assertArrayHasKey('workflow_efficiency', $metrics);
    }

    public function test_calculates_average_resolution_time(): void
    {
        $freshTicket = $this->ticket->fresh();
        $this->assertNotNull($freshTicket);
        $freshTicket->setAttribute('resolved_at', now()->subDays(1));

        $metrics = $this->service->getWorkflowMetrics($freshTicket);

        $this->assertGreaterThan(0, $metrics['avg_resolution_time']);
    }

    public function test_counts_total_transitions(): void
    {
        $this->service->transitionTo($this->ticket, 'in_review');
        $this->service->transitionTo($this->ticket, 'in_progress');

        $metrics = $this->service->getWorkflowMetrics($this->ticket);

        $this->assertSame(2, $metrics['transition_count']);
    }

    public function test_automatically_assigns_high_priority_tickets(): void
    {
        $this->ticket->update(['priority' => 'urgent']);
        $result = $this->service->applyWorkflowAutomation($this->ticket);

        $this->assertTrue($result);
    }

    public function test_automatically_escalates_overdue_tickets(): void
    {
        $this->ticket->update([
            'due_date' => now()->subDays(2),
            'status' => 'in_progress',
        ]);
        $result = $this->service->applyWorkflowAutomation($this->ticket);

        $this->assertTrue($result);
    }

    public function test_applies_type_specific_automation_rules(): void
    {
        $this->ticket->update(['type' => 'road_maintenance']);
        $result = $this->service->applyWorkflowAutomation($this->ticket);

        $this->assertTrue($result);
    }
}
