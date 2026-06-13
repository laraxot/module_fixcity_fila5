<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use InvalidArgumentException;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\TicketService;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use PHPUnit\Framework\Assert;

uses(\Modules\Fixcity\Tests\TestCase::class);

beforeEach(function (): void {
    /** @var \Modules\Fixcity\Tests\TestCase $this */
$this->ticketService = new TicketService;
        $this->user = UserFactory::new()->createOne();
});

describe('Ticket Service', function (): void {
    test('_creates_a_new_ticket_with_valid_data', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
$ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test content',
            'owner_id' => $this->authUser()->id,
            'status' => TicketStatusEnum::PENDING,
            'priority' => TicketPriorityEnum::MEDIUM,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        ];

        $ticket = $this->ticketService()->createTicket($ticketData, $this->authUser());

        Assert::assertInstanceOf(Ticket::class, $ticket);
        Assert::assertSame('Test Ticket', $ticket->name);
        Assert::assertSame('Test content', $ticket->content);
        Assert::assertSame($this->authUser()->id, $ticket->owner_id);
        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
        Assert::assertSame(TicketPriorityEnum::MEDIUM, $ticket->priority);
        Assert::assertSame(TicketTypeEnum::ROAD_MAINTENANCE, $ticket->type);
        Assert::assertSame($this->authUser()->id, $ticket->created_by);
    });

    test('_sets_default_status_to_draft_if_not_provided', function (): void {
$ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test content',
            'owner_id' => $this->authUser()->id,
        ];

        $ticket = $this->ticketService()->createTicket($ticketData, $this->authUser());

        Assert::assertSame('draft', $ticket->status?->value);
    });

    test('_sets_created_by_to_the_user_who_created_the_ticket', function (): void {
$ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test content',
            'owner_id' => $this->authUser()->id,
        ];

        $ticket = $this->ticketService()->createTicket($ticketData, $this->authUser());

        Assert::assertSame($this->authUser()->id, $ticket->created_by);
    });

    test('_assigns_ticket_to_a_user_when_status_is_pending', function (): void {
$ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $assignee = UserFactory::new()->createOne();

        $result = $this->ticketService()->assignTicket($ticket, $assignee);

        Assert::assertTrue($result);
        $freshTicket = $ticket->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame($assignee->id, $freshTicket->getAttribute('assigned_to'));
        Assert::assertSame('assigned', $freshTicket->status?->value);
    });

    test('_throws_exception_when_trying_to_assign_non_pending_ticket', function (): void {
$ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
        ]);

        $assignee = UserFactory::new()->createOne();

        $this->expectApplicationException(InvalidArgumentException::class);
        $this->expectThrowableMessage('Ticket must be in pending status to assign');
        $this->ticketService()->assignTicket($ticket, $assignee);
    });

    test('_updates_ticket_status_with_valid_transition', function (): void {
$ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $result = $this->ticketService()->updateStatus($ticket, TicketStatusEnum::IN_PROGRESS->value);

        Assert::assertTrue($result);
        $freshTicket = $ticket->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame(TicketStatusEnum::IN_PROGRESS, $freshTicket->status);
    });

    test('_throws_exception_with_invalid_status_transition', function (): void {
$ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::CLOSED,
        ]);

        $this->expectApplicationException(InvalidArgumentException::class);
        $this->ticketService()->updateStatus($ticket, TicketStatusEnum::PENDING->value);
    });

    test('_updates_ticket_priority_with_valid_value', function (): void {
$ticket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::LOW,
        ]);

        $result = $this->ticketService()->updatePriority($ticket, 'high');

        Assert::assertTrue($result);
        $freshTicket = $ticket->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame('high', $freshTicket->priority?->value);
    });

    test('_throws_exception_with_invalid_priority_value', function (): void {
$ticket = TicketFactory::new()->createOne();

        $this->expectApplicationException(InvalidArgumentException::class);
        $this->expectThrowableMessage('Invalid priority value: invalid_priority');
        $this->ticketService()->updatePriority($ticket, 'invalid_priority');
    });

    test('_updates_ticket_category_with_valid_value', function (): void {
$ticket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        ]);

        $result = $this->ticketService()->updateCategory($ticket, 'technical');

        Assert::assertTrue($result);
        $freshTicket = $ticket->fresh();
        Assert::assertNotNull($freshTicket);
        Assert::assertSame('technical', $freshTicket->type?->value);
    });

    test('_throws_exception_with_invalid_category_value', function (): void {
$ticket = TicketFactory::new()->createOne();

        $this->expectApplicationException(InvalidArgumentException::class);
        $this->expectThrowableMessage('Invalid category value: invalid_category');
        $this->ticketService()->updateCategory($ticket, 'invalid_category');
    });

    test('_returns_valid_status_transitions_for_pending_ticket', function (): void {
$ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $transitions = $this->ticketService()->getValidStatusTransitions($ticket->status->value ?? '');

        Assert::assertContains(TicketStatusEnum::IN_PROGRESS->value, $transitions);
        Assert::assertContains(TicketStatusEnum::ON_HOLD->value, $transitions);
    });

    test('_returns_valid_status_transitions_for_in_progress_ticket', function (): void {
$ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
        ]);

        $transitions = $this->ticketService()->getValidStatusTransitions($ticket->status->value ?? '');

        Assert::assertContains(TicketStatusEnum::RESOLVED->value, $transitions);
        Assert::assertContains(TicketStatusEnum::ON_HOLD->value, $transitions);
    });

    test('_returns_tickets_matching_search_criteria', function (): void {
$ticket1 = TicketFactory::new()->createOne([
            'name' => 'Road maintenance issue',
            'content' => 'Pothole in via Roma',
        ]);

        $ticket2 = TicketFactory::new()->createOne([
            'name' => 'Lighting problem',
            'content' => 'Street light not working',
        ]);

        $results = $this->ticketService()->searchTickets('road');

        Assert::assertContains($ticket1->id, $results->pluck('id')->toArray());
        Assert::assertNotContains($ticket2->id, $results->pluck('id')->toArray());
    });

    test('_returns_empty_collection_when_no_matches_found', function (): void {
TicketFactory::new()->createOne([
            'name' => 'Road maintenance issue',
        ]);

        $results = $this->ticketService()->searchTickets('nonexistent');

        Assert::assertEmpty($results);
    });

    test('_returns_tickets_with_specific_status', function (): void {
$pendingTicket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $inProgressTicket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
        ]);

        $pendingTickets = $this->ticketService()->getTicketsByStatus(TicketStatusEnum::PENDING->value);

        Assert::assertContains($pendingTicket->id, $pendingTickets->pluck('id')->toArray());
        Assert::assertNotContains($inProgressTicket->id, $pendingTickets->pluck('id')->toArray());
    });

    test('_returns_tickets_with_specific_priority', function (): void {
$highPriorityTicket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::HIGH,
        ]);

        $lowPriorityTicket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::LOW,
        ]);

        $highPriorityTickets = $this->ticketService()->getTicketsByPriority(TicketPriorityEnum::HIGH->value);

        Assert::assertContains($highPriorityTicket->id, $highPriorityTickets->pluck('id')->toArray());
        Assert::assertNotContains($lowPriorityTicket->id, $highPriorityTickets->pluck('id')->toArray());
    });

    test('_returns_tickets_with_specific_type', function (): void {
$roadTicket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        ]);

        $lightingTicket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::PUBLIC_LIGHTING,
        ]);

        $roadTickets = $this->ticketService()->getTicketsByType(TicketTypeEnum::ROAD_MAINTENANCE->value);

        Assert::assertContains($roadTicket->id, $roadTickets->pluck('id')->toArray());
        Assert::assertNotContains($lightingTicket->id, $roadTickets->pluck('id')->toArray());
    });

    test('_returns_tickets_owned_by_specific_user', function (): void {
$user1 = UserFactory::new()->createOne();
        $user2 = UserFactory::new()->createOne();

        $ticket1 = TicketFactory::new()->createOne([
            'owner_id' => $user1->id,
        ]);

        $ticket2 = TicketFactory::new()->createOne([
            'owner_id' => $user2->id,
        ]);

        $user1Tickets = $this->ticketService()->getTicketsByUser($user1);

        Assert::assertContains($ticket1->id, $user1Tickets->pluck('id')->toArray());
        Assert::assertNotContains($ticket2->id, $user1Tickets->pluck('id')->toArray());
    });

    test('_returns_tickets_assigned_to_specific_user', function (): void {
$assignee1 = UserFactory::new()->createOne();
        $assignee2 = UserFactory::new()->createOne();

        $ticket1 = TicketFactory::new()->createOne([
            'responsible_id' => $assignee1->id,
        ]);

        $ticket2 = TicketFactory::new()->createOne([
            'responsible_id' => $assignee2->id,
        ]);

        $assignee1Tickets = $this->ticketService()->getTicketsByAssignee($assignee1);

        Assert::assertContains($ticket1->id, $assignee1Tickets->pluck('id')->toArray());
        Assert::assertNotContains($ticket2->id, $assignee1Tickets->pluck('id')->toArray());
    });

    test('_returns_correct_ticket_statistics', function (): void {
TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);
        TicketFactory::new()->createOne(['status' => TicketStatusEnum::IN_PROGRESS]);
        TicketFactory::new()->createOne(['status' => TicketStatusEnum::RESOLVED]);

        $stats = $this->ticketService()->getTicketStatistics();

        Assert::assertSame(3, $stats['total']);
        Assert::assertSame(1, $stats['pending']);
        Assert::assertSame(1, $stats['in_progress']);
        Assert::assertSame(1, $stats['resolved']);
    });
});
