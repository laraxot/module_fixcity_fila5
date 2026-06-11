<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Controllers;

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Modules\Fixcity\Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->createOne();
        $this->admin = UserFactory::new()->createOne()->assignRole('admin');
    }

    /** @test */
    public function it_can_list_tickets_for_authenticated_user(): void {
        $this->actingAs($this->authUser());

        TicketFactory::new()->count(3)->create(['owner_id' => $this->authUser()->id]);

        $response = $this->getJson('/api/tickets');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'type',
                        'created_at',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_show_ticket_details(): void {
        $this->actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $ticket->id,
                    'name' => $ticket->name,
                    'content' => $ticket->content,
                ],
            ]);
    }

    /** @test */
    public function it_can_create_new_ticket(): void {
        $this->actingAs($this->authUser());

        $ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'type' => 'road_maintenance',
            'priority' => 'medium',
            'location' => 'Via Roma 123',
            'latitude' => 41.9028,
            'longitude' => 12.4964,
        ];

        $response = $this->postJson('/api/tickets', $ticketData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Test Ticket',
                    'content' => 'Test Description',
                    'type' => 'road_maintenance',
                    'priority' => 'medium',
                ],
            ]);

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'owner_id' => $this->authUser()->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_ticket(): void {
        $this->actingAs($this->authUser());

        $response = $this->postJson('/api/tickets', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'type']);
    }

    /** @test */
    public function it_can_update_ticket(): void {
        $this->actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        $updateData = [
            'name' => 'Updated Title',
            'content' => 'Updated Description',
        ];

        $response = $this->putJson("/api/tickets/{$ticket->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Title',
                    'content' => 'Updated Description',
                ],
            ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'name' => 'Updated Title',
        ]);
    }

    /** @test */
    public function it_can_delete_ticket(): void {
        $this->actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        $response = $this->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    /** @test */
    public function it_can_assign_ticket_to_user(): void {
        $this->actingAs($this->authAdmin());

        $ticket = TicketFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        $response = $this->postJson("/api/tickets/{$ticket->id}/assign", [
            'user_id' => $assignee->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'responsible_id' => $assignee->id,
                ],
            ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'responsible_id' => $assignee->id,
        ]);
    }

    /** @test */
    public function it_can_change_ticket_status(): void {
        $this->actingAs($this->authAdmin());

        $ticket = TicketFactory::new()->createOne(['status' => 'pending']);

        $response = $this->postJson("/api/tickets/{$ticket->id}/status", [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'in_progress',
                ],
            ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function it_can_add_comment_to_ticket(): void {
        $this->actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        $commentData = [
            'content' => 'Test comment',
            'is_internal' => false,
            'is_private' => false,
        ];

        $response = $this->postJson("/api/tickets/{$ticket->id}/comments", $commentData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'content' => 'Test comment',
                    'user_id' => $this->authUser()->id,
                ],
            ]);

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->authUser()->id,
            'content' => 'Test comment',
        ]);
    }

    /** @test */
    public function it_can_list_ticket_comments(): void {
        $this->actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);
        $ticket->ticketComments()->create([
            'user_id' => $this->authUser()->id,
            'content' => 'Test comment 1',
        ]);
        $ticket->ticketComments()->create([
            'user_id' => $this->authUser()->id,
            'content' => 'Test comment 2',
        ]);

        $response = $this->getJson("/api/tickets/{$ticket->id}/comments");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'content',
                        'user_id',
                        'created_at',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_search_tickets(): void {
        $this->actingAs($this->authUser());

        TicketFactory::new()->createOne([
            'name' => 'Road pothole',
            'owner_id' => $this->authUser()->id,
        ]);
        TicketFactory::new()->createOne([
            'name' => 'Street light broken',
            'owner_id' => $this->authUser()->id,
        ]);

        $response = $this->getJson('/api/tickets?search=road');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['name' => 'Road pothole'],
                ],
            ]);
    }

    /** @test */
    public function it_can_filter_tickets_by_status(): void {
        $this->actingAs($this->authUser());

        TicketFactory::new()->createOne([
            'status' => 'pending',
            'owner_id' => $this->authUser()->id,
        ]);
        TicketFactory::new()->createOne([
            'status' => 'in_progress',
            'owner_id' => $this->authUser()->id,
        ]);

        $response = $this->getJson('/api/tickets?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['status' => 'pending'],
                ],
            ]);
    }

    /** @test */
    public function it_can_filter_tickets_by_priority(): void {
        $this->actingAs($this->authUser());

        TicketFactory::new()->createOne([
            'priority' => 'high',
            'owner_id' => $this->authUser()->id,
        ]);
        TicketFactory::new()->createOne([
            'priority' => 'medium',
            'owner_id' => $this->authUser()->id,
        ]);

        $response = $this->getJson('/api/tickets?priority=high');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['priority' => 'high'],
                ],
            ]);
    }

    /** @test */
    public function it_can_filter_tickets_by_type(): void {
        $this->actingAs($this->authUser());

        TicketFactory::new()->createOne([
            'type' => 'road_maintenance',
            'owner_id' => $this->authUser()->id,
        ]);
        TicketFactory::new()->createOne([
            'type' => 'public_lighting',
            'owner_id' => $this->authUser()->id,
        ]);

        $response = $this->getJson('/api/tickets?type=road_maintenance');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['type' => 'road_maintenance'],
                ],
            ]);
    }

    /** @test */
    public function it_can_sort_tickets_by_creation_date(): void {
        $this->actingAs($this->authUser());

        $oldTicket = TicketFactory::new()->createOne([
            'created_at' => now()->subDays(2),
            'owner_id' => $this->authUser()->id,
        ]);
        $newTicket = TicketFactory::new()->createOne([
            'created_at' => now(),
            'owner_id' => $this->authUser()->id,
        ]);

        $response = $this->getJson('/api/tickets?sort=created_at&order=desc');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => $newTicket->id],
                    ['id' => $oldTicket->id],
                ],
            ]);
    }

    /** @test */
    public function it_can_paginate_tickets(): void {
        $this->actingAs($this->authUser());

        TicketFactory::new()->count(25)->create(['owner_id' => $this->authUser()->id]);

        $response = $this->getJson('/api/tickets?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJson([
                'meta' => [
                    'per_page' => 10,
                    'total' => 25,
                ],
            ]);
    }

    /** @test */
    public function it_requires_authentication_for_ticket_operations(): void {
        $ticket = TicketFactory::new()->createOne();

        $response = $this->getJson('/api/tickets');
        $response->assertStatus(401);

        $response = $this->postJson('/api/tickets', []);
        $response->assertStatus(401);

        $response = $this->getJson("/api/tickets/{$ticket->id}");
        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_only_access_own_tickets_unless_admin(): void {
        $otherUser = UserFactory::new()->createOne();
        $otherTicket = TicketFactory::new()->createOne(['owner_id' => $otherUser->id]);

        $this->actingAs($this->authUser());

        $response = $this->getJson("/api/tickets/{$otherTicket->id}");
        $response->assertStatus(403);

        $this->actingAs($this->authAdmin());

        $response = $this->getJson("/api/tickets/{$otherTicket->id}");
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_export_tickets(): void {
        $this->actingAs($this->authAdmin());

        TicketFactory::new()->count(5)->create();

        $response = $this->getJson('/api/tickets/export?format=csv');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function it_can_get_ticket_statistics(): void {
        $this->actingAs($this->authAdmin());

        TicketFactory::new()->count(3)->create(['status' => 'pending']);
        TicketFactory::new()->count(2)->create(['status' => 'in_progress']);
        TicketFactory::new()->count(1)->create(['status' => 'resolved']);

        $response = $this->getJson('/api/tickets/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total' => 6,
                    'pending' => 3,
                    'in_progress' => 2,
                    'resolved' => 1,
                ],
            ]);
    }
}
