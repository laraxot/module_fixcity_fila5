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
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;

uses(\Modules\Fixcity\Tests\TestCase::class);
// Laraxot — see module docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.

beforeEach(function (): void {
    /** @var \Modules\Fixcity\Tests\TestCase $this */
    $this->admin = UserFactory::new()->createOne();
    $this->user = UserFactory::new()->createOne();

    // Set admin panel for testing
    Filament::setCurrentPanel('fixcity::admin');
    actingAs($this->authAdmin());
});

describe('Ticket Resource', function (): void {
    test('ticket resource has correct model class', function (): void {
        Assert::assertEquals(Ticket::class, TicketResource::getModel());
    });

    test('ticket resource has correct slug', function (): void {
        Assert::assertEquals('tickets', TicketResource::getSlug());
    });

    test('ticket resource has navigation configuration', function (): void {
        $navigationBadge = TicketResource::getNavigationBadge();
        Assert::assertNotNull($navigationBadge);
    });

    test('ticket resource can get navigation items', function (): void {
        $navigationItems = TicketResource::getNavigationItems();
        Assert::assertNotEmpty($navigationItems);
    });

    test('list tickets page can render', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        /** @var \Illuminate\Database\Eloquent\Collection<int, Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create([
            'owner_id' => $this->authUser()->id,
        ]);

        $lw = Livewire::test(ListTickets::class);
        $lw->assertSuccessful();
        foreach ($tickets as $ticket) {
            $lw->assertSee((string) $ticket->name);
        }
    });

    test('list tickets page can search tickets by name', function (): void {
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
    });

    test('list tickets page can filter tickets by status', function (): void {
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
    });

    test('list tickets page can filter tickets by priority', function (): void {
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
    });

    test('list tickets page can sort tickets by created at', function (): void {
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
    });

    test('create ticket page can render', function (): void {
Livewire::test(CreateTicket::class)
            ->assertSuccessful();
    });

    test('create ticket page can create ticket', function (): void {
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

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
            'owner_id' => $this->authUser()->id,
        ]);
    });

    test('create ticket page validates required fields', function (): void {
Livewire::test(CreateTicket::class)
            ->fillForm([
                'name' => '',
                'content' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['title', 'description']);
    });

    test('edit ticket page can render', function (): void {
$ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful();
    });

    test('edit ticket page can update ticket', function (): void {
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

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'name' => 'Updated Ticket Title',
            'content' => 'Updated Description',
            'priority' => TicketPriorityEnum::HIGH->value,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ]);
    });

    test('view ticket page can render', function (): void {
$ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($ticket->name)
            ->assertSee($ticket->content);
    });

    test('admin can view ticket details', function (): void {
$ticket = TicketFactory::new()->createOne();

        get("/admin/tickets/{$ticket->id}")
            ->assertSuccessful()
            ->assertSee($ticket->name)
            ->assertSee($ticket->content);
    });

    test('admin can assign ticket to user', function (): void {
$ticket = TicketFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();

        put("/admin/tickets/{$ticket->id}", [
            'responsible_id' => $assignee->id,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'responsible_id' => $assignee->id,
        ]);
    });

    test('admin can change ticket status', function (): void {
$ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);

        put("/admin/tickets/{$ticket->id}", [
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ]);
    });

    test('admin can change ticket priority', function (): void {
$ticket = TicketFactory::new()->createOne(['priority' => TicketPriorityEnum::LOW]);

        put("/admin/tickets/{$ticket->id}", [
            'priority' => TicketPriorityEnum::HIGH->value,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'priority' => TicketPriorityEnum::HIGH->value,
        ]);
    });

    test('admin can delete ticket', function (): void {
$ticket = TicketFactory::new()->createOne();

        $this->delete("/admin/tickets/{$ticket->id}")
            ->assertRedirect('/admin/tickets');

        $this->assertDatabaseMissingRow('tickets', [
            'id' => $ticket->id,
        ]);
    });

    test('admin can bulk delete tickets', function (): void {
/** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create();

        post('/admin/tickets/bulk-delete', [
            'ids' => $tickets->pluck('id')->toArray(),
        ])->assertRedirect('/admin/tickets');

        foreach ($tickets as $ticket) {
            $this->assertDatabaseMissingRow('tickets', [
                'id' => $ticket->id,
            ]);
        }
    });

    test('admin can bulk update ticket status', function (): void {
/** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create([
            'status' => TicketStatusEnum::PENDING,
        ]);

        post('/admin/tickets/bulk-update', [
            'ids' => $tickets->pluck('id')->toArray(),
            'status' => TicketStatusEnum::IN_PROGRESS->value,
        ])->assertRedirect('/admin/tickets');

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHasRow('tickets', [
                'id' => $ticket->id,
                'status' => TicketStatusEnum::IN_PROGRESS->value,
            ]);
        }
    });

    test('admin can bulk assign tickets', function (): void {
/** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\Fixcity\Models\Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create();
        $assignee = UserFactory::new()->createOne();

        post('/admin/tickets/bulk-assign', [
            'ids' => $tickets->pluck('id')->toArray(),
            'responsible_id' => $assignee->id,
        ])->assertRedirect('/admin/tickets');

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHasRow('tickets', [
                'id' => $ticket->id,
                'responsible_id' => $assignee->id,
            ]);
        }
    });

    test('admin can export tickets', function (): void {
TicketFactory::new()->count(5)->create();

        get('/admin/tickets/export')
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    });

    test('admin can import tickets', function (): void {
$csvData = "title,description,priority,status,type\nTest Ticket,Test Description,medium,open,technical";

        post('/admin/tickets/import', [
            'file' => $csvData,
        ])->assertRedirect('/admin/tickets');

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
        ]);
    });

    test('user can view own tickets', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        actingAs($this->authUser());

        $ownTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        $otherTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
        ]);

        get('/admin/tickets')
            ->assertSuccessful()
            ->assertSee($ownTicket->name)
            ->assertDontSee($otherTicket->name);
    });

    test('user can create ticket', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        actingAs($this->authUser());

        $ticketData = [
            'name' => 'User Ticket',
            'content' => 'User Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'type' => TicketTypeEnum::COMPLAINT->value,
        ];

        post('/admin/tickets', $ticketData)
            ->assertRedirect('/admin/tickets');

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'User Ticket',
            'content' => 'User Description',
            'owner_id' => $this->authUser()->id,
        ]);
    });

    test('user cannot delete other tickets', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        actingAs($this->authUser());

        $otherTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
        ]);

        delete("/admin/tickets/{$otherTicket->id}")
            ->assertStatus(403);
    });

    test('user cannot assign tickets', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
        actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        put("/admin/tickets/{$ticket->id}", [
            'responsible_id' => $this->authAdmin()->id,
        ])->assertStatus(403);
    });

    test('guest cannot access ticket management', function (): void {
get('/admin/tickets')->assertRedirect('/login');
        get('/admin/tickets/create')->assertRedirect('/login');
    });
});
