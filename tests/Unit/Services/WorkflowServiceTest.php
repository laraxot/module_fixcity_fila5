<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Services\WorkflowService;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(\Modules\Fixcity\Tests\TestCase::class);

beforeEach(function (): void {
    /** @var \Modules\Fixcity\Tests\TestCase $this */
$this->workflowService = new WorkflowService;
        $this->user = UserFactory::new()->createOne();
        $this->ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
            'status' => 'pending',
        ]);
});

describe('Workflow Service', function (): void {
    test('_allows_transition_from_pending_to_in_review', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
$result = $this->workflow()->canTransitionTo($this->ticket(), 'in_review');

        Assert::assertTrue($result);
    });

    test('_allows_transition_from_pending_to_in_progress', function (): void {
$result = $this->workflow()->canTransitionTo($this->ticket(), 'in_progress');

        Assert::assertTrue($result);
    });

    test('_allows_transition_from_in_review_to_in_progress', function (): void {
$this->ticket()->update(['status' => 'in_review']);
        $result = $this->workflow()->canTransitionTo($this->ticket(), 'in_progress');

        Assert::assertTrue($result);
    });

    test('_allows_transition_from_in_progress_to_on_hold', function (): void {
$this->ticket()->update(['status' => 'in_progress']);
        $result = $this->workflow()->canTransitionTo($this->ticket(), 'on_hold');

        Assert::assertTrue($result);
    });

    test('_allows_transition_from_in_progress_to_resolved', function (): void {
$this->ticket()->update(['status' => 'in_progress']);
        $result = $this->workflow()->canTransitionTo($this->ticket(), 'resolved');

        Assert::assertTrue($result);
    });

    test('_allows_transition_from_resolved_to_closed', function (): void {
$this->ticket()->update(['status' => 'resolved']);
        $result = $this->workflow()->canTransitionTo($this->ticket(), 'closed');

        Assert::assertTrue($result);
    });

    test('_allows_transition_from_closed_to_reopened', function (): void {
$this->ticket()->update(['status' => 'closed']);
        $result = $this->workflow()->canTransitionTo($this->ticket(), 'reopened');

        Assert::assertTrue($result);
    });

    test('_prevents_invalid_transitions', function (): void {
$result = $this->workflow()->canTransitionTo($this->ticket(), 'invalid_status');

        Assert::assertFalse($result);
    });

    test('_prevents_transition_from_pending_to_resolved', function (): void {
$result = $this->workflow()->canTransitionTo($this->ticket(), 'resolved');

        Assert::assertFalse($result);
    });

    test('_prevents_transition_from_pending_to_closed', function (): void {
$result = $this->workflow()->canTransitionTo($this->ticket(), 'closed');

        Assert::assertFalse($result);
    });

    test('_returns_correct_transitions_for_pending_status', function (): void {
$transitions = $this->workflow()->getAvailableTransitions($this->ticket());

        Assert::assertContains('in_review', $transitions);
        Assert::assertContains('in_progress', $transitions);
        Assert::assertNotContains('resolved', $transitions);
        Assert::assertNotContains('closed', $transitions);
    });

    test('_returns_correct_transitions_for_in_review_status', function (): void {
$this->ticket()->update(['status' => 'in_review']);
        $transitions = $this->workflow()->getAvailableTransitions($this->ticket());

        Assert::assertContains('in_progress', $transitions);
        Assert::assertContains('on_hold', $transitions);
        Assert::assertNotContains('pending', $transitions);
    });

    test('_returns_correct_transitions_for_in_progress_status', function (): void {
$this->ticket()->update(['status' => 'in_progress']);
        $transitions = $this->workflow()->getAvailableTransitions($this->ticket());

        Assert::assertContains('on_hold', $transitions);
        Assert::assertContains('resolved', $transitions);
        Assert::assertNotContains('pending', $transitions);
    });

    test('_returns_correct_transitions_for_resolved_status', function (): void {
$this->ticket()->update(['status' => 'resolved']);
        $transitions = $this->workflow()->getAvailableTransitions($this->ticket());

        Assert::assertContains('closed', $transitions);
        Assert::assertNotContains('in_progress', $transitions);
    });

    test('_returns_correct_transitions_for_closed_status', function (): void {
$this->ticket()->update(['status' => 'closed']);
        $transitions = $this->workflow()->getAvailableTransitions($this->ticket());

        Assert::assertContains('reopened', $transitions);
        Assert::assertNotContains('resolved', $transitions);
    });

    test('_successfully_transitions_ticket_status', function (): void {
$result = $this->workflow()->transitionTo($this->ticket(), 'in_review');

        Assert::assertTrue($result);
        $freshTicket = $this->ticket()->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame('in_review', $freshTicket->status?->value);
    });

    test('_creates_activity_log_for_transition', function (): void {
$result = $this->workflow()->transitionTo($this->ticket(), 'in_review');

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->ticket()->activities);
        /** @var TicketActivity $firstActivity */
        $firstActivity = $this->ticket()->activities->first();
        Assert::assertStringContainsString(
            'Status changed from pending to in_review',
            SafeStringCastAction::cast($firstActivity->getAttribute('description'))
        );
    });

    test('_prevents_invalid_transitions_for_transition to', function (): void {
$result = $this->workflow()->transitionTo($this->ticket(), 'invalid_status');

        Assert::assertFalse($result);
        $freshTicket = $this->ticket()->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame('pending', $freshTicket->status?->value);
    });

    test('_prevents_transition_from_pending_to_resolved_for_transition to', function (): void {
$result = $this->workflow()->transitionTo($this->ticket(), 'resolved');

        Assert::assertFalse($result);
        $freshTicket = $this->ticket()->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame('pending', $freshTicket->status?->value);
    });

    test('_updates_ticket_timestamps_on_transition', function (): void {
$oldUpdatedAt = $this->ticket()->updated_at;
        sleep(1);

        $result = $this->workflow()->transitionTo($this->ticket(), 'in_review');

        Assert::assertTrue($result);
        $freshTicket = $this->ticket()->fresh();
        Assert::assertNotNull($freshTicket);
        $updatedAt = $freshTicket->updated_at;
        Assert::assertNotNull($updatedAt);
        Assert::assertNotNull($oldUpdatedAt);
        Assert::assertTrue($updatedAt->gt($oldUpdatedAt));
    });

    test('_returns_workflow_rules_for_ticket_type', function (): void {
$rules = $this->workflow()->getWorkflowRules($this->ticket());

        Assert::assertArrayHasKey('transitions', $rules);
        Assert::assertArrayHasKey('constraints', $rules);
    });

    test('_returns_different_rules_for_different_ticket_types', function (): void {
$this->ticket()->update(['type' => 'road_maintenance']);
        $roadRules = $this->workflow()->getWorkflowRules($this->ticket());

        $this->ticket()->update(['type' => 'public_lighting']);
        $lightingRules = $this->workflow()->getWorkflowRules($this->ticket());

        $this->assertNotSame($roadRules, $lightingRules);
    });

    test('_includes_priority_based_rules', function (): void {
$this->ticket()->update(['priority' => 'urgent']);
        $rules = $this->workflow()->getWorkflowRules($this->ticket());

        /** @var array<string, mixed> $constraints */
        $constraints = $rules['constraints'];
        Assert::assertArrayHasKey('urgent_priority', $constraints);
    });

    test('_validates_transition_requirements', function (): void {
$result = $this->workflow()->validateTransition($this->ticket(), 'in_progress');

        Assert::assertTrue($result['valid']);
    });

    test('_requires_assignee_for_in_progress_transition', function (): void {
$result = $this->workflow()->validateTransition($this->ticket(), 'in_progress');

        Assert::assertFalse($result['valid']);
        /** @var list<string> $errors */
        $errors = $result['errors'];
        Assert::assertContains('Ticket must be assigned to proceed', $errors);
    });

    test('_validates_transition_with_assignee', function (): void {
$assignee = UserFactory::new()->createOne();
        $this->ticket()->update(['responsible_id' => $assignee->id]);

        $result = $this->workflow()->validateTransition($this->ticket(), 'in_progress');

        Assert::assertTrue($result['valid']);
    });

    test('_requires_resolution_note_for_resolved_transition', function (): void {
$this->ticket()->update(['status' => 'in_progress']);
        $result = $this->workflow()->validateTransition($this->ticket(), 'resolved');

        Assert::assertFalse($result['valid']);
        /** @var list<string> $resolutionErrors */
        $resolutionErrors = $result['errors'];
        Assert::assertContains('Resolution note is required', $resolutionErrors);
    });

    test('_validates_resolved_transition_with_note', function (): void {
$this->ticket()->update([
            'status' => 'in_progress',
            'resolution_note' => 'Issue has been resolved',
        ]);
        $result = $this->workflow()->validateTransition($this->ticket(), 'resolved');

        Assert::assertTrue($result['valid']);
    });

    test('_returns_transition_history_for_ticket', function (): void {
$this->workflow()->transitionTo($this->ticket(), 'in_review');
        $this->workflow()->transitionTo($this->ticket(), 'in_progress');

        $history = $this->workflow()->getTransitionHistory($this->ticket());

        Assert::assertCount(2, $history);
        $first = $history->first();
        Assert::assertNotNull($first);
        Assert::assertSame('in_review', $first->getAttribute('to_status'));
        $last = $history->last();
        Assert::assertNotNull($last);
        Assert::assertSame('in_progress', $last->getAttribute('to_status'));
    });

    test('_orders_transitions_by_timestamp', function (): void {
$this->workflow()->transitionTo($this->ticket(), 'in_review');
        sleep(1);
        $this->workflow()->transitionTo($this->ticket(), 'in_progress');

        $history = $this->workflow()->getTransitionHistory($this->ticket());

        $first = $history->first();
        $last = $history->last();
        Assert::assertNotNull($first);
        Assert::assertNotNull($last);
        Assert::assertNotNull($first->created_at);
        Assert::assertNotNull($last->created_at);
        Assert::assertTrue($first->created_at->lt($last->created_at));
    });

    test('_allows_reopening_closed_tickets', function (): void {
$this->ticket()->update(['status' => 'closed']);
        $result = $this->workflow()->canReopenTicket($this->ticket());

        Assert::assertTrue($result);
    });

    test('_prevents_reopening_non_closed_tickets', function (): void {
$result = $this->workflow()->canReopenTicket($this->ticket());

        Assert::assertFalse($result);
    });

    test('_prevents_reopening_tickets_closed_for_too_long', function (): void {
$this->ticket()->update([
            'status' => 'closed',
            'closed_at' => now()->subDays(31),
        ]);
        $result = $this->workflow()->canReopenTicket($this->ticket());

        Assert::assertFalse($result);
    });

    test('_successfully_reopens_closed_ticket', function (): void {
$this->ticket()->update(['status' => 'closed']);
        $result = $this->workflow()->reopenTicket($this->ticket(), 'Reopening for additional work');

        Assert::assertTrue($result);
        $freshTicket = $this->ticket()->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame('reopened', $freshTicket->status?->value);
    });

    test('_creates_activity_log_for_reopening', function (): void {
$this->ticket()->update(['status' => 'closed']);
        $result = $this->workflow()->reopenTicket($this->ticket(), 'Reopening for additional work');

        Assert::assertTrue($result);
        $lastActivity = $this->ticket()->activities->last();
        Assert::assertNotNull($lastActivity);
        Assert::assertStringContainsString(
            'Ticket reopened',
            SafeStringCastAction::cast($lastActivity->getAttribute('description'))
        );
    });

    test('_prevents_reopening_non_closed_tickets_for_reopen', function (): void {
$result = $this->workflow()->reopenTicket($this->ticket(), 'Test reason');

        Assert::assertFalse($result);
        $freshTicket = $this->ticket()->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame('pending', $freshTicket->status?->value);
    });

    test('_returns_workflow_performance_metrics', function (): void {
$metrics = $this->workflow()->getWorkflowMetrics($this->ticket());

        Assert::assertArrayHasKey('avg_resolution_time', $metrics);
        Assert::assertArrayHasKey('transition_count', $metrics);
        Assert::assertArrayHasKey('workflow_efficiency', $metrics);
    });

    test('_calculates_average_resolution_time', function (): void {
$freshTicket = $this->ticket()->fresh();
        Assert::assertNotNull($freshTicket);
        $freshTicket->setAttribute('resolved_at', now()->subDays(1));

        $metrics = $this->workflow()->getWorkflowMetrics($freshTicket);

        Assert::assertGreaterThan(0, $metrics['avg_resolution_time']);
    });

    test('_counts_total_transitions', function (): void {
$this->workflow()->transitionTo($this->ticket(), 'in_review');
        $this->workflow()->transitionTo($this->ticket(), 'in_progress');

        $metrics = $this->workflow()->getWorkflowMetrics($this->ticket());

        Assert::assertSame(2, $metrics['transition_count']);
    });

    test('_automatically_assigns_high_priority_tickets', function (): void {
$this->ticket()->update(['priority' => 'urgent']);
        $result = $this->workflow()->applyWorkflowAutomation($this->ticket());

        Assert::assertTrue($result);
    });

    test('_automatically_escalates_overdue_tickets', function (): void {
$this->ticket()->update([
            'due_date' => now()->subDays(2),
            'status' => 'in_progress',
        ]);
        $result = $this->workflow()->applyWorkflowAutomation($this->ticket());

        Assert::assertTrue($result);
    });

    test('_applies_type_specific_automation_rules', function (): void {
$this->ticket()->update(['type' => 'road_maintenance']);
        $result = $this->workflow()->applyWorkflowAutomation($this->ticket());

        Assert::assertTrue($result);
    });
});
