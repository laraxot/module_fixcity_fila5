<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Filament;

use Modules\Fixcity\Database\Factories\TicketFactory;
use PHPUnit\Framework\Assert;
use Modules\User\Database\Factories\UserFactory;
use Filament\Facades\Filament;
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
use Modules\Fixcity\Tests\TestCase;

class TicketResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = UserFactory::new()->createOne();
        $this->user = UserFactory::new()->createOne();

        // Set admin panel for testing
        Filament::setCurrentPanel('fixcity::admin');
        $this->actingAs($this->authAdmin());
    }

    /** @test */
    public function ticket_resource_has_correct_model_class(): void
    {
        Assert::assertEquals(Ticket::class, TicketResource::getModel());
    }

    /** @test */
    public function ticket_resource_has_correct_slug(): void
    {
        Assert::assertEquals('tickets', TicketResource::getSlug());
    }

    /** @test */
    public function ticket_resource_has_navigation_configuration(): void
    {
        $navigationBadge = TicketResource::getNavigationBadge();
        Assert::assertNotNull($navigationBadge);
    }

    /** @test */
    public function ticket_resource_can_get_navigation_items(): void
    {
        $navigationItems = TicketResource::getNavigationItems();
        Assert::assertNotEmpty($navigationItems);
    }

    /** @test */
    public function list_tickets_page_can_render(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create([
            'owner_id' => $this->authUser()->id,
        ]);

        $lw = Livewire::test(ListTickets::class);
        $lw->assertSuccessful();
        foreach ($tickets as $ticket) {
            $lw->assertSee((string) $ticket->name);
        }
    }

    /** @test */
    public function list_tickets_page_can_search_tickets_by_name(): void
    {
        $searchableTicket = TicketFactory::new()->createOne([
            'name' => 'Searchable Ticket Name',
            'owner_id' => $this->authUser()->id,
        ]);

        $otherTicket = TicketFactory::new()->createOne([
            'name' => 'Other Ticket',
            'owner_id' => $this->authUser()->id,
        ]);

        $lw = Livewire::test(ListTickets::class)->searchTable('Searchable');
        $lw->assertSee((string) $searchableTicket->name);
        $lw->assertDontSee((string) $otherTicket->name);
    }

    /** @test */
    public function list_tickets_page_can_filter_tickets_by_status(): void
    {
        $pendingTicket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
            'owner_id' => $this->authUser()->id,
        ]);

        $resolvedTicket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::RESOLVED,
            'owner_id' => $this->authUser()->id,
        ]);

        $lw = Livewire::test(ListTickets::class)->filterTable('status', TicketStatusEnum::PENDING->value);
        $lw->assertSee((string) $pendingTicket->name);
        $lw->assertDontSee((string) $resolvedTicket->name);
    }

    /** @test */
    public function list_tickets_page_can_filter_tickets_by_priority(): void
    {
        $highPriorityTicket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::HIGH,
            'owner_id' => $this->authUser()->id,
        ]);

        $lowPriorityTicket = TicketFactory::new()->createOne([
            'priority' => TicketPriorityEnum::LOW,
            'owner_id' => $this->authUser()->id,
        ]);

        $lw = Livewire::test(ListTickets::class)->filterTable('priority', TicketPriorityEnum::HIGH->value);
        $lw->assertSee((string) $highPriorityTicket->name);
        $lw->assertDontSee((string) $lowPriorityTicket->name);
    }

    /** @test */
    public function list_tickets_page_can_sort_tickets_by_created_at(): void
    {
        $olderTicket = TicketFactory::new()->createOne([
            'created_at' => now()->subDays(2),
            'owner_id' => $this->authUser()->id,
        ]);

        $newerTicket = TicketFactory::new()->createOne([
            'created_at' => now()->subDay(),
            'owner_id' => $this->authUser()->id,
        ]);

        $lw = Livewire::test(ListTickets::class)->sortTable('created_at', 'desc');
        $lw->assertSee((string) $newerTicket->name);
        $lw->assertSee((string) $olderTicket->name);
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
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
            'owner_id' => $this->authUser()->id,
        ];

        Livewire::test(CreateTicket::class)
            ->fillForm($ticketData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
            'owner_id' => $this->authUser()->id,
        ]);
    }

    /** @test */
    public function create_ticket_page_validates_required_fields(): void
    {
        Livewire::test(CreateTicket::class)
            ->fillForm([
                'name' => '',
                'content' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['title', 'description']);
    }

    /** @test */
    public function edit_ticket_page_can_render(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful();
    }

    /** @test */
    public function edit_ticket_page_can_update_ticket(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        $updatedData = [
            'name' => 'Updated Ticket Title',
            'content' => 'Updated Description',
            'priority' => TicketPriorityEnum::HIGH->value,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ];

        Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'name' => 'Updated Ticket Title',
            'content' => 'Updated Description',
            'priority' => TicketPriorityEnum::HIGH->value,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ]);
    }

    /** @test */
    public function view_ticket_page_can_render(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($ticket->name)
            ->assertSee($ticket->content);
    }

    /** @test */
    public function admin_can_view_ticket_details(): void
    {
        $ticket = TicketFactory::new()->createOne();

        $this->get("/admin/tickets/{$ticket->id}")
            ->assertSuccessful()
            ->assertSee($ticket->name)
            ->assertSee($ticket->content);
    }

    /** @test */
    public function admin_can_assign_ticket_to_user(): void
    {
        $ticket = TicketFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

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
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);

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
        $ticket = TicketFactory::new()->createOne(['priority' => TicketPriorityEnum::LOW]);

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
        $ticket = TicketFactory::new()->createOne();

        $this->delete("/admin/tickets/{$ticket->id}")
            ->assertRedirect('/admin/tickets');

        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_tickets(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create();

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
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create([
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
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create();
        $assignee = UserFactory::new()->createOne();

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
        TicketFactory::new()->count(5)->create();

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
            'name' => 'Test Ticket',
            'content' => 'Test Description',
        ]);
    }

    /** @test */
    public function user_can_view_own_tickets(): void
    {
        $this->actingAs($this->authUser());

        $ownTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        $otherTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
        ]);

        $this->get('/admin/tickets')
            ->assertSuccessful()
            ->assertSee($ownTicket->name)
            ->assertDontSee($otherTicket->name);
    }

    /** @test */
    public function user_can_create_ticket(): void
    {
        $this->actingAs($this->authUser());

        $ticketData = [
            'name' => 'User Ticket',
            'content' => 'User Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'type' => TicketTypeEnum::COMPLAINT->value,
        ];

        $this->post('/admin/tickets', $ticketData)
            ->assertRedirect('/admin/tickets');

        $this->assertDatabaseHas('tickets', [
            'name' => 'User Ticket',
            'content' => 'User Description',
            'owner_id' => $this->authUser()->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_other_tickets(): void
    {
        $this->actingAs($this->authUser());

        $otherTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
        ]);

        $this->delete("/admin/tickets/{$otherTicket->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_assign_tickets(): void
    {
        $this->actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        $this->put("/admin/tickets/{$ticket->id}", [
            'responsible_id' => $this->authAdmin()->id,
        ])->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_ticket_management(): void
    {
        $this->get('/admin/tickets')->assertRedirect('/login');
        $this->get('/admin/tickets/create')->assertRedirect('/login');
    }
}
