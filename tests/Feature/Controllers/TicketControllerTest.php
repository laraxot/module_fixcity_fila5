<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Controllers;

use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create()->assignRole('admin');
    }

    /** @test */
    public function it_can_list_tickets_for_authenticated_user()
    {
        $this->actingAs($this->user);
        
        Ticket::factory()->count(3)->create(['owner_id' => $this->user->id]);

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
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_show_ticket_details()
    {
        $this->actingAs($this->user);
        
        $ticket = Ticket::factory()->create(['owner_id' => $this->user->id]);

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $ticket->id,
                        'title' => $ticket->title,
                        'description' => $ticket->description,
                    ]
                ]);
    }

    /** @test */
    public function it_can_create_new_ticket()
    {
        $this->actingAs($this->user);
        
        $ticketData = [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
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
                        'title' => 'Test Ticket',
                        'description' => 'Test Description',
                        'type' => 'road_maintenance',
                        'priority' => 'medium',
                    ]
                ]);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'owner_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_ticket()
    {
        $this->actingAs($this->user);
        
        $response = $this->postJson('/api/tickets', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'description', 'type']);
    }

    /** @test */
    public function it_can_update_ticket()
    {
        $this->actingAs($this->user);
        
        $ticket = Ticket::factory()->create(['owner_id' => $this->user->id]);
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ];

        $response = $this->putJson("/api/tickets/{$ticket->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'title' => 'Updated Title',
                        'description' => 'Updated Description',
                    ]
                ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function it_can_delete_ticket()
    {
        $this->actingAs($this->user);
        
        $ticket = Ticket::factory()->create(['owner_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(204);
        
        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    /** @test */
    public function it_can_assign_ticket_to_user()
    {
        $this->actingAs($this->admin);
        
        $ticket = Ticket::factory()->create();
        $assignee = User::factory()->create();

        $response = $this->postJson("/api/tickets/{$ticket->id}/assign", [
            'user_id' => $assignee->id,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'responsible_id' => $assignee->id,
                    ]
                ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'responsible_id' => $assignee->id,
        ]);
    }

    /** @test */
    public function it_can_change_ticket_status()
    {
        $this->actingAs($this->admin);
        
        $ticket = Ticket::factory()->create(['status' => 'pending']);

        $response = $this->postJson("/api/tickets/{$ticket->id}/status", [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'status' => 'in_progress',
                    ]
                ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function it_can_add_comment_to_ticket()
    {
        $this->actingAs($this->user);
        
        $ticket = Ticket::factory()->create(['owner_id' => $this->user->id]);

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
                        'user_id' => $this->user->id,
                    ]
                ]);

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
            'content' => 'Test comment',
        ]);
    }

    /** @test */
    public function it_can_list_ticket_comments()
    {
        $this->actingAs($this->user);
        
        $ticket = Ticket::factory()->create(['owner_id' => $this->user->id]);
        $ticket->comments()->create([
            'user_id' => $this->user->id,
            'content' => 'Test comment 1',
        ]);
        $ticket->comments()->create([
            'user_id' => $this->user->id,
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
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_search_tickets()
    {
        $this->actingAs($this->user);
        
        Ticket::factory()->create([
            'title' => 'Road pothole',
            'owner_id' => $this->user->id,
        ]);
        Ticket::factory()->create([
            'title' => 'Street light broken',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tickets?search=road');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJson([
                    'data' => [
                        ['title' => 'Road pothole']
                    ]
                ]);
    }

    /** @test */
    public function it_can_filter_tickets_by_status()
    {
        $this->actingAs($this->user);
        
        Ticket::factory()->create([
            'status' => 'pending',
            'owner_id' => $this->user->id,
        ]);
        Ticket::factory()->create([
            'status' => 'in_progress',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tickets?status=pending');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJson([
                    'data' => [
                        ['status' => 'pending']
                    ]
                ]);
    }

    /** @test */
    public function it_can_filter_tickets_by_priority()
    {
        $this->actingAs($this->user);
        
        Ticket::factory()->create([
            'priority' => 'high',
            'owner_id' => $this->user->id,
        ]);
        Ticket::factory()->create([
            'priority' => 'medium',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tickets?priority=high');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJson([
                    'data' => [
                        ['priority' => 'high']
                    ]
                ]);
    }

    /** @test */
    public function it_can_filter_tickets_by_type()
    {
        $this->actingAs($this->user);
        
        Ticket::factory()->create([
            'type' => 'road_maintenance',
            'owner_id' => $this->user->id,
        ]);
        Ticket::factory()->create([
            'type' => 'public_lighting',
            'owner_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tickets?type=road_maintenance');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJson([
                    'data' => [
                        ['type' => 'road_maintenance']
                    ]
                ]);
    }

    /** @test */
    public function it_can_sort_tickets_by_creation_date()
    {
        $this->actingAs($this->user);
        
        $oldTicket = Ticket::factory()->create([
            'created_at' => now()->subDays(2),
            'owner_id' => $this->user->id,
        ]);
        $newTicket = Ticket::factory()->create([
            'created_at' => now(),
            'owner_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tickets?sort=created_at&order=desc');

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        ['id' => $newTicket->id],
                        ['id' => $oldTicket->id],
                    ]
                ]);
    }

    /** @test */
    public function it_can_paginate_tickets()
    {
        $this->actingAs($this->user);
        
        Ticket::factory()->count(25)->create(['owner_id' => $this->user->id]);

        $response = $this->getJson('/api/tickets?per_page=10');

        $response->assertStatus(200)
                ->assertJsonCount(10, 'data')
                ->assertJson([
                    'meta' => [
                        'per_page' => 10,
                        'total' => 25,
                    ]
                ]);
    }

    /** @test */
    public function it_requires_authentication_for_ticket_operations()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->getJson('/api/tickets');
        $response->assertStatus(401);

        $response = $this->postJson('/api/tickets', []);
        $response->assertStatus(401);

        $response = $this->getJson("/api/tickets/{$ticket->id}");
        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_only_access_own_tickets_unless_admin()
    {
        $otherUser = User::factory()->create();
        $otherTicket = Ticket::factory()->create(['owner_id' => $otherUser->id]);

        $this->actingAs($this->user);

        $response = $this->getJson("/api/tickets/{$otherTicket->id}");
        $response->assertStatus(403);

        $this->actingAs($this->admin);

        $response = $this->getJson("/api/tickets/{$otherTicket->id}");
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_export_tickets()
    {
        $this->actingAs($this->admin);
        
        Ticket::factory()->count(5)->create();

        $response = $this->getJson('/api/tickets/export?format=csv');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function it_can_get_ticket_statistics()
    {
        $this->actingAs($this->admin);
        
        Ticket::factory()->count(3)->create(['status' => 'pending']);
        Ticket::factory()->count(2)->create(['status' => 'in_progress']);
        Ticket::factory()->count(1)->create(['status' => 'resolved']);

        $response = $this->getJson('/api/tickets/statistics');

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'total' => 6,
                        'pending' => 3,
                        'in_progress' => 2,
                        'resolved' => 1,
                    ]
                ]);
    }
}
