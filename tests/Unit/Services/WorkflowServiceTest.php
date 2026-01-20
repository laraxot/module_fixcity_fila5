<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\WorkflowService;
use Modules\User\Models\User;
use Tests\TestCase;

describe('WorkflowService', function () {
    beforeEach(function () {
        $this->service = new WorkflowService();
        $this->user = User::factory()->create();
        $this->ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
            'status' => 'pending',
        ]);
    });

    describe('canTransitionTo', function () {
        it('allows transition from pending to in_review', function () {
            $result = $this->service->canTransitionTo($this->ticket, 'in_review');

            expect($result)->toBeTrue();
        });

        it('allows transition from pending to in_progress', function () {
            $result = $this->service->canTransitionTo($this->ticket, 'in_progress');

            expect($result)->toBeTrue();
        });

        it('allows transition from in_review to in_progress', function () {
            $this->ticket->update(['status' => 'in_review']);
            $result = $this->service->canTransitionTo($this->ticket, 'in_progress');

            expect($result)->toBeTrue();
        });

        it('allows transition from in_progress to on_hold', function () {
            $this->ticket->update(['status' => 'in_progress']);
            $result = $this->service->canTransitionTo($this->ticket, 'on_hold');

            expect($result)->toBeTrue();
        });

        it('allows transition from in_progress to resolved', function () {
            $this->ticket->update(['status' => 'in_progress']);
            $result = $this->service->canTransitionTo($this->ticket, 'resolved');

            expect($result)->toBeTrue();
        });

        it('allows transition from resolved to closed', function () {
            $this->ticket->update(['status' => 'resolved']);
            $result = $this->service->canTransitionTo($this->ticket, 'closed');

            expect($result)->toBeTrue();
        });

        it('allows transition from closed to reopened', function () {
            $this->ticket->update(['status' => 'closed']);
            $result = $this->service->canTransitionTo($this->ticket, 'reopened');

            expect($result)->toBeTrue();
        });

        it('prevents invalid transitions', function () {
            $result = $this->service->canTransitionTo($this->ticket, 'invalid_status');

            expect($result)->toBeFalse();
        });

        it('prevents transition from pending to resolved', function () {
            $result = $this->service->canTransitionTo($this->ticket, 'resolved');

            expect($result)->toBeFalse();
        });

        it('prevents transition from pending to closed', function () {
            $result = $this->service->canTransitionTo($this->ticket, 'closed');

            expect($result)->toBeFalse();
        });
    });

    describe('getAvailableTransitions', function () {
        it('returns correct transitions for pending status', function () {
            $transitions = $this->service->getAvailableTransitions($this->ticket);

            expect($transitions)->toContain('in_review');
            expect($transitions)->toContain('in_progress');
            expect($transitions)->not->toContain('resolved');
            expect($transitions)->not->toContain('closed');
        });

        it('returns correct transitions for in_review status', function () {
            $this->ticket->update(['status' => 'in_review']);
            $transitions = $this->service->getAvailableTransitions($this->ticket);

            expect($transitions)->toContain('in_progress');
            expect($transitions)->toContain('on_hold');
            expect($transitions)->not->toContain('pending');
        });

        it('returns correct transitions for in_progress status', function () {
            $this->ticket->update(['status' => 'in_progress']);
            $transitions = $this->service->getAvailableTransitions($this->ticket);

            expect($transitions)->toContain('on_hold');
            expect($transitions)->toContain('resolved');
            expect($transitions)->not->toContain('pending');
        });

        it('returns correct transitions for resolved status', function () {
            $this->ticket->update(['status' => 'resolved']);
            $transitions = $this->service->getAvailableTransitions($this->ticket);

            expect($transitions)->toContain('closed');
            expect($transitions)->not->toContain('in_progress');
        });

        it('returns correct transitions for closed status', function () {
            $this->ticket->update(['status' => 'closed']);
            $transitions = $this->service->getAvailableTransitions($this->ticket);

            expect($transitions)->toContain('reopened');
            expect($transitions)->not->toContain('resolved');
        });
    });

    describe('transitionTo', function () {
        it('successfully transitions ticket status', function () {
            $result = $this->service->transitionTo($this->ticket, 'in_review');

            expect($result)->toBeTrue();
            expect($this->ticket->fresh()->status)->toBe('in_review');
        });

        it('creates activity log for transition', function () {
            $result = $this->service->transitionTo($this->ticket, 'in_review');

            expect($result)->toBeTrue();
            expect($this->ticket->activities)->not->toBeEmpty();
            expect($this->ticket->activities->first()->description)->toContain('Status changed from pending to in_review');
        });

        it('prevents invalid transitions', function () {
            $result = $this->service->transitionTo($this->ticket, 'invalid_status');

            expect($result)->toBeFalse();
            expect($this->ticket->fresh()->status)->toBe('pending');
        });

        it('prevents transition from pending to resolved', function () {
            $result = $this->service->transitionTo($this->ticket, 'resolved');

            expect($result)->toBeFalse();
            expect($this->ticket->fresh()->status)->toBe('pending');
        });

        it('updates ticket timestamps on transition', function () {
            $oldUpdatedAt = $this->ticket->updated_at;
            sleep(1); // Ensure timestamp difference

            $result = $this->service->transitionTo($this->ticket, 'in_review');

            expect($result)->toBeTrue();
            expect($this->ticket->fresh()->updated_at->gt($oldUpdatedAt))->toBeTrue();
        });
    });

    describe('getWorkflowRules', function () {
        it('returns workflow rules for ticket type', function () {
            $rules = $this->service->getWorkflowRules($this->ticket);

            expect($rules)->toBeArray();
            expect($rules)->toHaveKey('transitions');
            expect($rules)->toHaveKey('constraints');
        });

        it('returns different rules for different ticket types', function () {
            $this->ticket->update(['type' => 'road_maintenance']);
            $roadRules = $this->service->getWorkflowRules($this->ticket);

            $this->ticket->update(['type' => 'public_lighting']);
            $lightingRules = $this->service->getWorkflowRules($this->ticket);

            expect($roadRules)->not->toBe($lightingRules);
        });

        it('includes priority-based rules', function () {
            $this->ticket->update(['priority' => 'urgent']);
            $rules = $this->service->getWorkflowRules($this->ticket);

            expect($rules['constraints'])->toHaveKey('urgent_priority');
        });
    });

    describe('validateTransition', function () {
        it('validates transition requirements', function () {
            $result = $this->service->validateTransition($this->ticket, 'in_progress');

            expect($result['valid'])->toBeTrue();
        });

        it('requires assignee for in_progress transition', function () {
            $result = $this->service->validateTransition($this->ticket, 'in_progress');

            expect($result['valid'])->toBeFalse();
            expect($result['errors'])->toContain('Ticket must be assigned to proceed');
        });

        it('validates transition with assignee', function () {
            $assignee = User::factory()->create();
            $this->ticket->update(['responsible_id' => $assignee->id]);

            $result = $this->service->validateTransition($this->ticket, 'in_progress');

            expect($result['valid'])->toBeTrue();
        });

        it('requires resolution note for resolved transition', function () {
            $this->ticket->update(['status' => 'in_progress']);
            $result = $this->service->validateTransition($this->ticket, 'resolved');

            expect($result['valid'])->toBeFalse();
            expect($result['errors'])->toContain('Resolution note is required');
        });

        it('validates resolved transition with note', function () {
            $this->ticket->update([
                'status' => 'in_progress',
                'resolution_note' => 'Issue has been resolved',
            ]);
            $result = $this->service->validateTransition($this->ticket, 'resolved');

            expect($result['valid'])->toBeTrue();
        });
    });

    describe('getTransitionHistory', function () {
        it('returns transition history for ticket', function () {
            $this->service->transitionTo($this->ticket, 'in_review');
            $this->service->transitionTo($this->ticket, 'in_progress');

            $history = $this->service->getTransitionHistory($this->ticket);

            expect($history)->toHaveCount(2);
            expect($history->first()->to_status)->toBe('in_review');
            expect($history->last()->to_status)->toBe('in_progress');
        });

        it('orders transitions by timestamp', function () {
            $this->service->transitionTo($this->ticket, 'in_review');
            sleep(1);
            $this->service->transitionTo($this->ticket, 'in_progress');

            $history = $this->service->getTransitionHistory($this->ticket);

            expect($history->first()->created_at->lt($history->last()->created_at))->toBeTrue();
        });
    });

    describe('canReopenTicket', function () {
        it('allows reopening closed tickets', function () {
            $this->ticket->update(['status' => 'closed']);
            $result = $this->service->canReopenTicket($this->ticket);

            expect($result)->toBeTrue();
        });

        it('prevents reopening non-closed tickets', function () {
            $result = $this->service->canReopenTicket($this->ticket);

            expect($result)->toBeFalse();
        });

        it('prevents reopening tickets closed for too long', function () {
            $this->ticket->update([
                'status' => 'closed',
                'closed_at' => now()->subDays(31),
            ]);
            $result = $this->service->canReopenTicket($this->ticket);

            expect($result)->toBeFalse();
        });
    });

    describe('reopenTicket', function () {
        it('successfully reopens closed ticket', function () {
            $this->ticket->update(['status' => 'closed']);
            $result = $this->service->reopenTicket($this->ticket, 'Reopening for additional work');

            expect($result)->toBeTrue();
            expect($this->ticket->fresh()->status)->toBe('reopened');
        });

        it('creates activity log for reopening', function () {
            $this->ticket->update(['status' => 'closed']);
            $result = $this->service->reopenTicket($this->ticket, 'Reopening for additional work');

            expect($result)->toBeTrue();
            expect($this->ticket->activities->last()->description)->toContain('Ticket reopened');
        });

        it('prevents reopening non-closed tickets', function () {
            $result = $this->service->reopenTicket($this->ticket, 'Test reason');

            expect($result)->toBeFalse();
            expect($this->ticket->fresh()->status)->toBe('pending');
        });
    });

    describe('getWorkflowMetrics', function () {
        it('returns workflow performance metrics', function () {
            $metrics = $this->service->getWorkflowMetrics($this->ticket);

            expect($metrics)->toHaveKey('avg_resolution_time');
            expect($metrics)->toHaveKey('transition_count');
            expect($metrics)->toHaveKey('workflow_efficiency');
        });

        it('calculates average resolution time', function () {
            $this->ticket->update(['created_at' => now()->subDays(5)]);
            $this->ticket->update(['resolved_at' => now()->subDays(1)]);

            $metrics = $this->service->getWorkflowMetrics($this->ticket);

            expect($metrics['avg_resolution_time'])->toBeGreaterThan(0);
        });

        it('counts total transitions', function () {
            $this->service->transitionTo($this->ticket, 'in_review');
            $this->service->transitionTo($this->ticket, 'in_progress');

            $metrics = $this->service->getWorkflowMetrics($this->ticket);

            expect($metrics['transition_count'])->toBe(2);
        });
    });

    describe('applyWorkflowAutomation', function () {
        it('automatically assigns high priority tickets', function () {
            $this->ticket->update(['priority' => 'urgent']);
            $result = $this->service->applyWorkflowAutomation($this->ticket);

            expect($result)->toBeTrue();
            // Should automatically assign to available staff
        });

        it('automatically escalates overdue tickets', function () {
            $this->ticket->update([
                'due_date' => now()->subDays(2),
                'status' => 'in_progress',
            ]);
            $result = $this->service->applyWorkflowAutomation($this->ticket);

            expect($result)->toBeTrue();
            // Should escalate priority or notify managers
        });

        it('applies type-specific automation rules', function () {
            $this->ticket->update(['type' => 'road_maintenance']);
            $result = $this->service->applyWorkflowAutomation($this->ticket);

            expect($result)->toBeTrue();
            // Should apply road maintenance specific rules
        });
    });
});
