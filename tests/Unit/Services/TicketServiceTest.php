<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use InvalidArgumentException;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\TicketService;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\User\Models\User;
use Tests\TestCase;

describe('TicketService', function () {
    beforeEach(function () {
        $this->service = new TicketService();
        $this->user = User::factory()->create();
    });

    describe('createTicket', function () {
        it('creates a new ticket with valid data', function () {
            $ticketData = [
                'name' => 'Test Ticket',
                'content' => 'Test content',
                'owner_id' => $this->user->id,
                'status' => TicketStatusEnum::PENDING,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            ];

            $ticket = $this->service->createTicket($ticketData, $this->user);

            expect($ticket)
                ->toBeInstanceOf(Ticket::class)
                ->name->toBe('Test Ticket')
                ->content->toBe('Test content')
                ->owner_id->toBe($this->user->id)
                ->status->toBe(TicketStatusEnum::PENDING)
                ->priority->toBe(TicketPriorityEnum::MEDIUM)
                ->type->toBe(TicketTypeEnum::ROAD_MAINTENANCE)
                ->created_by->toBe($this->user->id);
        });

        it('sets default status to draft if not provided', function () {
            $ticketData = [
                'name' => 'Test Ticket',
                'content' => 'Test content',
                'owner_id' => $this->user->id,
            ];

            $ticket = $this->service->createTicket($ticketData, $this->user);

            expect($ticket->status)->toBe('draft');
        });

        it('sets created_by to the user who created the ticket', function () {
            $ticketData = [
                'name' => 'Test Ticket',
                'content' => 'Test content',
                'owner_id' => $this->user->id,
            ];

            $ticket = $this->service->createTicket($ticketData, $this->user);

            expect($ticket->created_by)->toBe($this->user->id);
        });
    });

    describe('assignTicket', function () {
        it('assigns ticket to a user when status is pending', function () {
            $ticket = Ticket::factory()->create([
                'status' => TicketStatusEnum::PENDING,
            ]);
            
            $assignee = User::factory()->create();

            $result = $this->service->assignTicket($ticket, $assignee);

            expect($result)->toBeTrue();
            expect($ticket->fresh()->assigned_to)->toBe($assignee->id);
            expect($ticket->fresh()->status)->toBe('assigned');
        });

        it('throws exception when trying to assign non-pending ticket', function () {
            $ticket = Ticket::factory()->create([
                'status' => TicketStatusEnum::IN_PROGRESS,
            ]);
            
            $assignee = User::factory()->create();

            expect(function () use ($ticket, $assignee) {
                $this->service->assignTicket($ticket, $assignee);
            })->toThrow(InvalidArgumentException::class, 'Ticket must be in pending status to assign');
        });
    });

    describe('updateStatus', function () {
        it('updates ticket status with valid transition', function () {
            $ticket = Ticket::factory()->create([
                'status' => TicketStatusEnum::PENDING,
            ]);

            $result = $this->service->updateStatus($ticket, TicketStatusEnum::IN_PROGRESS);

            expect($result)->toBeTrue();
            expect($ticket->fresh()->status)->toBe(TicketStatusEnum::IN_PROGRESS);
        });

        it('throws exception with invalid status transition', function () {
            $ticket = Ticket::factory()->create([
                'status' => TicketStatusEnum::CLOSED,
            ]);

            expect(function () use ($ticket) {
                $this->service->updateStatus($ticket, TicketStatusEnum::PENDING);
            })->toThrow(InvalidArgumentException::class);
        });
    });

    describe('updatePriority', function () {
        it('updates ticket priority with valid value', function () {
            $ticket = Ticket::factory()->create([
                'priority' => TicketPriorityEnum::LOW,
            ]);

            $result = $this->service->updatePriority($ticket, 'high');

            expect($result)->toBeTrue();
            expect($ticket->fresh()->priority)->toBe('high');
        });

        it('throws exception with invalid priority value', function () {
            $ticket = Ticket::factory()->create();

            expect(function () use ($ticket) {
                $this->service->updatePriority($ticket, 'invalid_priority');
            })->toThrow(InvalidArgumentException::class, 'Invalid priority value: invalid_priority');
        });
    });

    describe('updateCategory', function () {
        it('updates ticket category with valid value', function () {
            $ticket = Ticket::factory()->create([
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            ]);

            $result = $this->service->updateCategory($ticket, 'technical');

            expect($result)->toBeTrue();
            expect($ticket->fresh()->type)->toBe('technical');
        });

        it('throws exception with invalid category value', function () {
            $ticket = Ticket::factory()->create();

            expect(function () use ($ticket) {
                $this->service->updateCategory($ticket, 'invalid_category');
            })->toThrow(InvalidArgumentException::class, 'Invalid category value: invalid_category');
        });
    });

    describe('getValidStatusTransitions', function () {
        it('returns valid status transitions for pending ticket', function () {
            $ticket = Ticket::factory()->create([
                'status' => TicketStatusEnum::PENDING,
            ]);

            $transitions = $this->service->getValidStatusTransitions($ticket->status);

            expect($transitions)->toBeArray();
            expect($transitions)->toContain(TicketStatusEnum::IN_PROGRESS);
            expect($transitions)->toContain(TicketStatusEnum::ON_HOLD);
        });

        it('returns valid status transitions for in_progress ticket', function () {
            $ticket = Ticket::factory()->create([
                'status' => TicketStatusEnum::IN_PROGRESS,
            ]);

            $transitions = $this->service->getValidStatusTransitions($ticket->status);

            expect($transitions)->toBeArray();
            expect($transitions)->toContain(TicketStatusEnum::RESOLVED);
            expect($transitions)->toContain(TicketStatusEnum::ON_HOLD);
        });
    });

    describe('searchTickets', function () {
        it('returns tickets matching search criteria', function () {
            $ticket1 = Ticket::factory()->create([
                'name' => 'Road maintenance issue',
                'content' => 'Pothole in via Roma',
            ]);
            
            $ticket2 = Ticket::factory()->create([
                'name' => 'Lighting problem',
                'content' => 'Street light not working',
            ]);

            $results = $this->service->searchTickets('road');

            expect($results)->toContain($ticket1);
            expect($results)->not->toContain($ticket2);
        });

        it('returns empty collection when no matches found', function () {
            Ticket::factory()->create([
                'name' => 'Road maintenance issue',
            ]);

            $results = $this->service->searchTickets('nonexistent');

            expect($results)->toBeEmpty();
        });
    });

    describe('getTicketsByStatus', function () {
        it('returns tickets with specific status', function () {
            $pendingTicket = Ticket::factory()->create([
                'status' => TicketStatusEnum::PENDING,
            ]);
            
            $inProgressTicket = Ticket::factory()->create([
                'status' => TicketStatusEnum::IN_PROGRESS,
            ]);

            $pendingTickets = $this->service->getTicketsByStatus(TicketStatusEnum::PENDING);

            expect($pendingTickets)->toContain($pendingTicket);
            expect($pendingTickets)->not->toContain($inProgressTicket);
        });
    });

    describe('getTicketsByPriority', function () {
        it('returns tickets with specific priority', function () {
            $highPriorityTicket = Ticket::factory()->create([
                'priority' => TicketPriorityEnum::HIGH,
            ]);
            
            $lowPriorityTicket = Ticket::factory()->create([
                'priority' => TicketPriorityEnum::LOW,
            ]);

            $highPriorityTickets = $this->service->getTicketsByPriority(TicketPriorityEnum::HIGH);

            expect($highPriorityTickets)->toContain($highPriorityTicket);
            expect($highPriorityTickets)->not->toContain($lowPriorityTicket);
        });
    });

    describe('getTicketsByType', function () {
        it('returns tickets with specific type', function () {
            $roadTicket = Ticket::factory()->create([
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            ]);
            
            $lightingTicket = Ticket::factory()->create([
                'type' => TicketTypeEnum::PUBLIC_LIGHTING,
            ]);

            $roadTickets = $this->service->getTicketsByType(TicketTypeEnum::ROAD_MAINTENANCE);

            expect($roadTickets)->toContain($roadTicket);
            expect($roadTickets)->not->toContain($lightingTicket);
        });
    });

    describe('getTicketsByUser', function () {
        it('returns tickets owned by specific user', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            $ticket1 = Ticket::factory()->create([
                'owner_id' => $user1->id,
            ]);
            
            $ticket2 = Ticket::factory()->create([
                'owner_id' => $user2->id,
            ]);

            $user1Tickets = $this->service->getTicketsByUser($user1);

            expect($user1Tickets)->toContain($ticket1);
            expect($user1Tickets)->not->toContain($ticket2);
        });
    });

    describe('getTicketsByAssignee', function () {
        it('returns tickets assigned to specific user', function () {
            $assignee1 = User::factory()->create();
            $assignee2 = User::factory()->create();
            
            $ticket1 = Ticket::factory()->create([
                'responsible_id' => $assignee1->id,
            ]);
            
            $ticket2 = Ticket::factory()->create([
                'responsible_id' => $assignee2->id,
            ]);

            $assignee1Tickets = $this->service->getTicketsByAssignee($assignee1);

            expect($assignee1Tickets)->toContain($ticket1);
            expect($assignee1Tickets)->not->toContain($ticket2);
        });
    });

    describe('getTicketStatistics', function () {
        it('returns correct ticket statistics', function () {
            Ticket::factory()->create(['status' => TicketStatusEnum::PENDING]);
            Ticket::factory()->create(['status' => TicketStatusEnum::IN_PROGRESS]);
            Ticket::factory()->create(['status' => TicketStatusEnum::RESOLVED]);

            $stats = $this->service->getTicketStatistics();

            expect($stats)->toBeArray();
            expect($stats['total'])->toBe(3);
            expect($stats['pending'])->toBe(1);
            expect($stats['in_progress'])->toBe(1);
            expect($stats['resolved'])->toBe(1);
        });
    });
});
