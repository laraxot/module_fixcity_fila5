<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\TicketService;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Tests\TestCase;

/**
 * Test TicketService methods.
 *
 * @group TicketService
 */
class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TicketService $service;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TicketService;
        $this->user = UserFactory::new()->createOne();
    }

    public function test_creates_a_new_ticket_with_valid_data(): void
    {
        $ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test content',
            'owner_id' => $this->user->id,
            'status' => TicketStatusEnum::PENDING,
            'priority' => TicketPriorityEnum::MEDIUM,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        ];

        $ticket = $this->service->createTicket($ticketData, $this->user);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertSame('Test Ticket', $ticket->name);
        $this->assertSame('Test content', $ticket->content);
        $this->assertSame($this->user->id, $ticket->owner_id);
        $this->assertSame(TicketStatusEnum::PENDING, $ticket->status);
        $this->assertSame(TicketPriorityEnum::MEDIUM, $ticket->priority);
        $this->assertSame(TicketTypeEnum::ROAD_MAINTENANCE, $ticket->type);
        $this->assertSame($this->user->id, $ticket->created_by);
    }

    public function test_sets_default_status_to_draft_if_not_provided(): void
    {
        $ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test content',
            'owner_id' => $this->user->id,
        ];

        $ticket = $this->service->createTicket($ticketData, $this->user);

        $this->assertSame('draft', $ticket->status?->value);
    }

    public function test_sets_created_by_to_the_user_who_created_the_ticket(): void
    {
        $ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test content',
            'owner_id' => $this->user->id,
        ];

        $ticket = $this->service->createTicket($ticketData, $this->user);

        $this->assertSame($this->user->id, $ticket->created_by);
    }

    public function test_assigns_ticket_to_a_user_when_status_is_pending(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $assignee = UserFactory::new()->createOne();

        $result = $this->service->assignTicket($ticket, $assignee);

        $this->assertTrue($result);
        $freshTicket = $ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame($assignee->id, $freshTicket->getAttribute('assigned_to'));
        $this->assertSame('assigned', $freshTicket->status?->value);
    }

    public function test_throws_exception_when_trying_to_assign_non_pending_ticket(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
        ]);

        $assignee = UserFactory::new()->createOne();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ticket must be in pending status to assign');
        $this->service->assignTicket($ticket, $assignee);
    }

    public function test_updates_ticket_status_with_valid_transition(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $result = $this->service->updateStatus($ticket, TicketStatusEnum::IN_PROGRESS->value);

        $this->assertTrue($result);
        $freshTicket = $ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame(TicketStatusEnum::IN_PROGRESS, $freshTicket->status);
    }

    public function test_throws_exception_with_invalid_status_transition(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::CLOSED,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->updateStatus($ticket, TicketStatusEnum::PENDING->value);
    }

    public function test_updates_ticket_priority_with_valid_value(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::LOW,
        ]);

        $result = $this->service->updatePriority($ticket, 'high');

        $this->assertTrue($result);
        $freshTicket = $ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame('high', $freshTicket->priority?->value);
    }

    public function test_throws_exception_with_invalid_priority_value(): void
    {
        $ticket = TicketFactory::new()->createOne();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid priority value: invalid_priority');
        $this->service->updatePriority($ticket, 'invalid_priority');
    }

    public function test_updates_ticket_category_with_valid_value(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        ]);

        $result = $this->service->updateCategory($ticket, 'technical');

        $this->assertTrue($result);
        $freshTicket = $ticket->fresh();
        $this->assertNotNull($freshTicket);
        $this->assertSame('technical', $freshTicket->type?->value);
    }

    public function test_throws_exception_with_invalid_category_value(): void
    {
        $ticket = TicketFactory::new()->createOne();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid category value: invalid_category');
        $this->service->updateCategory($ticket, 'invalid_category');
    }

    public function test_returns_valid_status_transitions_for_pending_ticket(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $transitions = $this->service->getValidStatusTransitions($ticket->status->value ?? '');

        $this->assertContains(TicketStatusEnum::IN_PROGRESS->value, $transitions);
        $this->assertContains(TicketStatusEnum::ON_HOLD->value, $transitions);
    }

    public function test_returns_valid_status_transitions_for_in_progress_ticket(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
        ]);

        $transitions = $this->service->getValidStatusTransitions($ticket->status->value ?? '');

        $this->assertContains(TicketStatusEnum::RESOLVED->value, $transitions);
        $this->assertContains(TicketStatusEnum::ON_HOLD->value, $transitions);
    }

    public function test_returns_tickets_matching_search_criteria(): void
    {
        $ticket1 = TicketFactory::new()->createOne([
            'name' => 'Road maintenance issue',
            'content' => 'Pothole in via Roma',
        ]);

        $ticket2 = TicketFactory::new()->createOne([
            'name' => 'Lighting problem',
            'content' => 'Street light not working',
        ]);

        $results = $this->service->searchTickets('road');

        $this->assertContains($ticket1->id, $results->pluck('id')->toArray());
        $this->assertNotContains($ticket2->id, $results->pluck('id')->toArray());
    }

    public function test_returns_empty_collection_when_no_matches_found(): void
    {
        TicketFactory::new()->createOne([
            'name' => 'Road maintenance issue',
        ]);

        $results = $this->service->searchTickets('nonexistent');

        $this->assertEmpty($results);
    }

    public function test_returns_tickets_with_specific_status(): void
    {
        $pendingTicket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $inProgressTicket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
        ]);

        $pendingTickets = $this->service->getTicketsByStatus(TicketStatusEnum::PENDING->value);

        $this->assertContains($pendingTicket->id, $pendingTickets->pluck('id')->toArray());
        $this->assertNotContains($inProgressTicket->id, $pendingTickets->pluck('id')->toArray());
    }

    public function test_returns_tickets_with_specific_priority(): void
    {
        $highPriorityTicket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::HIGH,
        ]);

        $lowPriorityTicket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::LOW,
        ]);

        $highPriorityTickets = $this->service->getTicketsByPriority(TicketPriorityEnum::HIGH->value);

        $this->assertContains($highPriorityTicket->id, $highPriorityTickets->pluck('id')->toArray());
        $this->assertNotContains($lowPriorityTicket->id, $highPriorityTickets->pluck('id')->toArray());
    }

    public function test_returns_tickets_with_specific_type(): void
    {
        $roadTicket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        ]);

        $lightingTicket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::PUBLIC_LIGHTING,
        ]);

        $roadTickets = $this->service->getTicketsByType(TicketTypeEnum::ROAD_MAINTENANCE->value);

        $this->assertContains($roadTicket->id, $roadTickets->pluck('id')->toArray());
        $this->assertNotContains($lightingTicket->id, $roadTickets->pluck('id')->toArray());
    }

    public function test_returns_tickets_owned_by_specific_user(): void
    {
        $user1 = UserFactory::new()->createOne();
        $user2 = UserFactory::new()->createOne();

        $ticket1 = TicketFactory::new()->createOne([
            'owner_id' => $user1->id,
        ]);

        $ticket2 = TicketFactory::new()->createOne([
            'owner_id' => $user2->id,
        ]);

        $user1Tickets = $this->service->getTicketsByUser($user1);

        $this->assertContains($ticket1->id, $user1Tickets->pluck('id')->toArray());
        $this->assertNotContains($ticket2->id, $user1Tickets->pluck('id')->toArray());
    }

    public function test_returns_tickets_assigned_to_specific_user(): void
    {
        $assignee1 = UserFactory::new()->createOne();
        $assignee2 = UserFactory::new()->createOne();

        $ticket1 = TicketFactory::new()->createOne([
            'responsible_id' => $assignee1->id,
        ]);

        $ticket2 = TicketFactory::new()->createOne([
            'responsible_id' => $assignee2->id,
        ]);

        $assignee1Tickets = $this->service->getTicketsByAssignee($assignee1);

        $this->assertContains($ticket1->id, $assignee1Tickets->pluck('id')->toArray());
        $this->assertNotContains($ticket2->id, $assignee1Tickets->pluck('id')->toArray());
    }

    public function test_returns_correct_ticket_statistics(): void
    {
        TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);
        TicketFactory::new()->createOne(['status' => TicketStatusEnum::IN_PROGRESS]);
        TicketFactory::new()->createOne(['status' => TicketStatusEnum::RESOLVED]);

        $stats = $this->service->getTicketStatistics();

        $this->assertSame(3, $stats['total']);
        $this->assertSame(1, $stats['pending']);
        $this->assertSame(1, $stats['in_progress']);
        $this->assertSame(1, $stats['resolved']);
    }
}
