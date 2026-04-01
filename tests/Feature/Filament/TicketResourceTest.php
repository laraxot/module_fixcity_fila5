<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Filament;

use Livewire\Livewire;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\CreateTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\EditTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ListTickets;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ViewTicket;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Tests\TestCase;

class TicketResourceTest extends TestCase
{
    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->user = User::factory()->create();
        
        // Set admin panel for testing
        \Filament\Facades\Filament::setCurrentPanel('fixcity::admin');
        $this->actingAs($this->admin);
    }

    /** @test */
    public function ticket_resource_has_correct_model_class(): void
    {
        $this->assertEquals(Ticket::class, TicketResource::getModel());
    }

    /** @test */
    public function ticket_resource_has_correct_slug(): void
    {
        $this->assertEquals('tickets', TicketResource::getSlug());
    }

    /** @test */
    public function ticket_resource_has_navigation_configuration(): void
    {
        $navigationBadge = TicketResource::getNavigationBadge();
        $this->assertNotNull($navigationBadge);
    }

    /** @test */
    public function ticket_resource_can_get_navigation_items(): void
    {
        $navigationItems = TicketResource::getNavigationItems();
        $this->assertIsArray($navigationItems);
    }

    /** @test */
    public function list_tickets_page_can_render(): void
    {
        $tickets = Ticket::factory()->count(3)->create([
            'owner_id' => $this->user->id,
        ]);

        Livewire::test(ListTickets::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($tickets);
    }

    /** @test */
    public function list_tickets_page_can_search_tickets_by_name(): void
    {
        $searchableTicket = Ticket::factory()->create([
            'title' => 'Searchable Ticket Name',
            'owner_id' => $this->user->id,
        ]);

        $otherTicket = Ticket::factory()->create([
            'title' => 'Other Ticket',
            'owner_id' => $this->user->id,
        ]);

        Livewire::test(ListTickets::class)
            ->searchTable('Searchable')
            ->assertCanSeeTableRecords([$searchableTicket])
            ->assertCanNotSeeTableRecords([$otherTicket]);
    }

    /** @test */
    public function list_tickets_page_can_filter_tickets_by_status(): void
    {
        $pendingTicket = Ticket::factory()->create([
            'status' => TicketStatusEnum::PENDING,
            'owner_id' => $this->user->id,
        ]);

        $resolvedTicket = Ticket::factory()->create([
            'status' => TicketStatusEnum::RESOLVED,
            'owner_id' => $this->user->id,
        ]);

        Livewire::test(ListTickets::class)
            ->filterTable('status', TicketStatusEnum::PENDING->value)
            ->assertCanSeeTableRecords([$pendingTicket])
            ->assertCanNotSeeTableRecords([$resolvedTicket]);
    }

    /** @test */
    public function list_tickets_page_can_filter_tickets_by_priority(): void
    {
        $highPriorityTicket = Ticket::factory()->create([
            'priority' => TicketPriorityEnum::HIGH,
            'owner_id' => $this->user->id,
        ]);

        $lowPriorityTicket = Ticket::factory()->create([
            'priority' => TicketPriorityEnum::LOW,
            'owner_id' => $this->user->id,
        ]);

        Livewire::test(ListTickets::class)
            ->filterTable('priority', TicketPriorityEnum::HIGH->value)
            ->assertCanSeeTableRecords([$highPriorityTicket])
            ->assertCanNotSeeTableRecords([$lowPriorityTicket]);
    }

    /** @test */
    public function list_tickets_page_can_sort_tickets_by_created_at(): void
    {
        $olderTicket = Ticket::factory()->create([
            'created_at' => now()->subDays(2),
            'owner_id' => $this->user->id,
        ]);

        $newerTicket = Ticket::factory()->create([
            'created_at' => now()->subDay(),
            'owner_id' => $this->user->id,
        ]);

        Livewire::test(ListTickets::class)
            ->sortTable('created_at', 'desc')
            ->assertCanSeeTableRecords([$newerTicket, $olderTicket]);
    }

    /** @test */
    public function create_ticket_page_can_render(): void
    {
        Livewire::test(CreateTicket::class)
            ->assertSuccessful();
    }

    /** @test */
    public function create_ticket_page_can_create_ticket(): void
    {
        $ticketData = [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::TECHNICAL->value,
            'owner_id' => $this->user->id,
        ];

        Livewire::test(CreateTicket::class)
            ->fillForm($ticketData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::TECHNICAL->value,
            'owner_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function create_ticket_page_validates_required_fields(): void
    {
        Livewire::test(CreateTicket::class)
            ->fillForm([
                'title' => '',
                'description' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['title', 'description']);
    }

    /** @test */
    public function edit_ticket_page_can_render(): void
    {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful();
    }

    /** @test */
    public function edit_ticket_page_can_update_ticket(): void
    {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $updatedData = [
            'title' => 'Updated Ticket Title',
            'description' => 'Updated Description',
            'priority' => TicketPriorityEnum::HIGH->value,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ];

        Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated Ticket Title',
            'description' => 'Updated Description',
            'priority' => TicketPriorityEnum::HIGH->value,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ]);
    }

    /** @test */
    public function view_ticket_page_can_render(): void
    {
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($ticket->title)
            ->assertSee($ticket->description);
    }

    /** @test */
    public function admin_can_view_ticket_details(): void
    {
        $ticket = Ticket::factory()->create();

        $this->get("/admin/tickets/{$ticket->id}")
             ->assertSuccessful()
             ->assertSee($ticket->title)
             ->assertSee($ticket->description);
    }

    /** @test */
    public function admin_can_assign_ticket_to_user(): void
    {
        $ticket = Ticket::factory()->create();
        $assignee = User::factory()->create();

        $this->put("/admin/tickets/{$ticket->id}", [
            'responsible_id' => $assignee->id,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'responsible_id' => $assignee->id,
        ]);
    }

    /** @test */
    public function admin_can_change_ticket_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatusEnum::PENDING]);

        $this->put("/admin/tickets/{$ticket->id}", [
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ]);
    }

    /** @test */
    public function admin_can_change_ticket_priority(): void
    {
        $ticket = Ticket::factory()->create(['priority' => TicketPriorityEnum::LOW]);

        $this->put("/admin/tickets/{$ticket->id}", [
            'priority' => TicketPriorityEnum::HIGH->value,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'priority' => TicketPriorityEnum::HIGH->value,
        ]);
    }

    /** @test */
    public function admin_can_delete_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->delete("/admin/tickets/{$ticket->id}")
             ->assertRedirect('/admin/tickets');

        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_tickets(): void
    {
        $tickets = Ticket::factory()->count(3)->create();

        $this->post('/admin/tickets/bulk-delete', [
            'ids' => $tickets->pluck('id')->toArray(),
        ])->assertRedirect('/admin/tickets');

        foreach ($tickets as $ticket) {
            $this->assertDatabaseMissing('tickets', [
                'id' => $ticket->id,
            ]);
        }
    }

    /** @test */
    public function admin_can_bulk_update_ticket_status(): void
    {
        $tickets = Ticket::factory()->count(3)->create([
            'status' => TicketStatusEnum::PENDING,
        ]);

        $this->post('/admin/tickets/bulk-update', [
            'ids' => $tickets->pluck('id')->toArray(),
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ])->assertRedirect('/admin/tickets');

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('tickets', [
                'id' => $ticket->id,
                'status' => TicketStatusEnum::IN_PROGRESS->value,
            ]);
        }
    }

    /** @test */
    public function admin_can_bulk_assign_tickets(): void
    {
        $tickets = Ticket::factory()->count(3)->create();
        $assignee = User::factory()->create();

        $this->post('/admin/tickets/bulk-assign', [
            'ids' => $tickets->pluck('id')->toArray(),
            'responsible_id' => $assignee->id,
        ])->assertRedirect('/admin/tickets');

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('tickets', [
                'id' => $ticket->id,
                'responsible_id' => $assignee->id,
            ]);
        }
    }

    /** @test */
    public function admin_can_export_tickets(): void
    {
        Ticket::factory()->count(5)->create();

        $this->get('/admin/tickets/export')
             ->assertSuccessful()
             ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function admin_can_import_tickets(): void
    {
        $csvData = "title,description,priority,status,type\nTest Ticket,Test Description,medium,open,technical";

        $this->post('/admin/tickets/import', [
            'file' => $csvData,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
        ]);
    }

    /** @test */
    public function user_can_view_own_tickets(): void
    {
        $this->actingAs($this->user);
        
        $ownTicket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);
        
        $otherTicket = Ticket::factory()->create([
            'owner_id' => $this->admin->id,
        ]);

        $this->get('/admin/tickets')
             ->assertSuccessful()
             ->assertSee($ownTicket->title)
             ->assertDontSee($otherTicket->title);
    }

    /** @test */
    public function user_can_create_ticket(): void
    {
        $this->actingAs($this->user);

        $ticketData = [
            'title' => 'User Ticket',
            'description' => 'User Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'type' => TicketTypeEnum::SUPPORT->value,
        ];

        $this->post('/admin/tickets', $ticketData)
             ->assertRedirect('/admin/tickets');

        $this->assertDatabaseHas('tickets', [
            'title' => 'User Ticket',
            'description' => 'User Description',
            'owner_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_other_tickets(): void
    {
        $this->actingAs($this->user);
        
        $otherTicket = Ticket::factory()->create([
            'owner_id' => $this->admin->id,
        ]);

        $this->delete("/admin/tickets/{$otherTicket->id}")
             ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_assign_tickets(): void
    {
        $this->actingAs($this->user);
        
        $ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);

        $this->put("/admin/tickets/{$ticket->id}", [
            'responsible_id' => $this->admin->id,
        ])->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_ticket_management(): void
    {
        $this->get('/admin/tickets')->assertRedirect('/login');
        $this->get('/admin/tickets/create')->assertRedirect('/login');
    }
}