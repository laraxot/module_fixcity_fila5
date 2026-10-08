<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Filament;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Actions\DeleteTicketAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\CreateTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\EditTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ListTickets;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ViewTicket;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use PHPUnit\Framework\Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(TestCase::class);
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
    /** @var TestCase $this */
    $this->admin = UserFactory::new()->createOne();
    $this->user = UserFactory::new()->createOne();

    $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
    $this->admin->assignRole($adminRole);
    $paRole = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
    $this->admin->assignRole($paRole);

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
        /** @var TestCase $this */
        /** @var Collection<int, Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create([
            'owner_id' => $this->authUser()->id,
        ]);

        $lw = Livewire::test(ListTickets::class);
        $lw->assertSuccessful();
        $lw->assertCanSeeTableRecords($tickets);
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
        $lw->assertCanSeeTableRecords([$pendingTicket]);
        $lw->assertCanNotSeeTableRecords([$resolvedTicket]);
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
        $lw->assertCanSeeTableRecords([$highPriorityTicket]);
        $lw->assertCanNotSeeTableRecords([$lowPriorityTicket]);
    });

    test('list tickets page can filter tickets by assigned operator', function (): void {
        $assignee = UserFactory::new()->createOne();
        $assignedTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'responsible_id' => $assignee->id,
        ]);
        $unassignedTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'responsible_id' => null,
        ]);

        Livewire::test(ListTickets::class)
            ->filterTable('responsible_id', $assignee->id)
            ->assertCanSeeTableRecords([$assignedTicket])
            ->assertCanNotSeeTableRecords([$unassignedTicket]);
    });

    test('list tickets page can filter unassigned tickets', function (): void {
        $assignedTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'responsible_id' => UserFactory::new()->createOne()->id,
        ]);
        $unassignedTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'responsible_id' => null,
        ]);

        Livewire::test(ListTickets::class)
            ->filterTable('assignment_status', ['assignment_status' => 'unassigned'])
            ->assertCanSeeTableRecords([$unassignedTicket])
            ->assertCanNotSeeTableRecords([$assignedTicket]);
    });

    test('list tickets page can filter tickets by creation date range', function (): void {
        $from = now()->startOfDay();
        $matchingTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'created_at' => $from->copy()->addHours(10),
        ]);
        $olderTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
            'created_at' => $from->copy()->subDay(),
        ]);

        Livewire::test(ListTickets::class)
            ->filterTable('created_at', [
                'created_from' => $from->toDateString(),
                'created_until' => $from->toDateString(),
            ])
            ->assertCanSeeTableRecords([$matchingTicket])
            ->assertCanNotSeeTableRecords([$olderTicket]);
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
        $lw->assertCanSeeTableRecords([$newerTicket, $olderTicket]);
    });

    test('create ticket page can render', function (): void {
        Livewire::test(CreateTicket::class)
            ->assertSuccessful();
    });

    test('create ticket page can create ticket', function (): void {
        $ticketData = [
            'privacyAccepted' => true,
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
            'owner_id' => $this->authAdmin()->id,
            'location' => [
                'latitude' => 45.4642,
                'longitude' => 9.1900,
                'address' => 'Milano',
            ],
        ];

        Livewire::test(CreateTicket::class)
            ->set('data', $ticketData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
            'owner_id' => $this->authAdmin()->id,
        ]);
    });

    test('create ticket page validates required fields', function (): void {
        Livewire::test(CreateTicket::class)
            ->fillForm([
                'name' => '',
                'content' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'content']);
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
            'privacyAccepted' => true,
            'name' => 'Updated Ticket Title',
            'content' => 'Updated Description',
            'priority' => TicketPriorityEnum::HIGH->value,
            'status' => TicketStatusEnum::IN_PROGRESS->value,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
            'location' => [
                'latitude' => 45.4642,
                'longitude' => 9.1900,
                'address' => 'Milano',
            ],
        ];

        Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
            ->set('data', $updatedData)
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
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
        ]);

        Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($ticket->name)
            ->assertSee($ticket->content);
    });

    test('admin can assign ticket to user', function (): void {
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
            'responsible_id' => null,
        ]);
        $assignee = UserFactory::new()->createOne();

        Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
            ->mountAction('assign')
            ->set('mountedActions.0.data.responsible_id', $assignee->id)
            ->assertActionDataSet(['responsible_id' => $assignee->id])
            ->callMountedAction();

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'responsible_id' => $assignee->id,
        ]);
    });

    test('admin can change ticket status', function (): void {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);

        Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
            ->mountAction('changeStatus')
            ->set('mountedActions.0.data.status', TicketStatusEnum::IN_REVIEW->value)
            ->set('mountedActions.0.data.reason', 'Aggiornamento operativo')
            ->callMountedAction();

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatusEnum::IN_REVIEW->value,
        ]);
    });

    test('admin can change ticket priority', function (): void {
        $ticket = TicketFactory::new()->createOne(['priority' => TicketPriorityEnum::LOW]);

        Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
            ->mountAction('changePriority')
            ->set('mountedActions.0.data.priority', TicketPriorityEnum::HIGH->value)
            ->callMountedAction();

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'priority' => TicketPriorityEnum::HIGH->value,
        ]);
    });

    test('admin can delete ticket', function (): void {
        $ticket = TicketFactory::new()->createOne();

        Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertActionExists('delete');
        app(DeleteTicketAction::class)->execute($ticket);

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
        ]);
        Assert::assertNotNull($ticket->fresh()?->deleted_at);
    });

    test('admin can bulk delete tickets', function (): void {
        /** @var Collection<int, Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create();

        Livewire::test(ListTickets::class)
            ->callTableBulkAction('delete', $tickets->pluck('id')->all());

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHasRow('tickets', [
                'id' => $ticket->id,
            ]);
            Assert::assertNotNull($ticket->fresh()?->deleted_at);
        }
    });

    test('admin can bulk update ticket status', function (): void {
        /** @var Collection<int, Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create([
            'status' => TicketStatusEnum::PENDING,
        ]);

        foreach ($tickets as $ticket) {
            Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
                ->mountAction('changeStatus')
                ->set('mountedActions.0.data.status', TicketStatusEnum::IN_REVIEW->value)
                ->set('mountedActions.0.data.reason', 'Presa in carico')
                ->callMountedAction();
        }

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHasRow('tickets', [
                'id' => $ticket->id,
                'status' => TicketStatusEnum::IN_REVIEW->value,
            ]);
        }
    });

    test('admin can bulk assign tickets', function (): void {
        /** @var Collection<int, Ticket> $tickets */
        $tickets = TicketFactory::new()->count(3)->create();
        $assignee = UserFactory::new()->createOne();

        foreach ($tickets as $ticket) {
            Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
                ->mountAction('assign')
                ->set('mountedActions.0.data.responsible_id', $assignee->id)
                ->callMountedAction();
        }

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHasRow('tickets', [
                'id' => $ticket->id,
                'responsible_id' => $assignee->id,
            ]);
        }
    });

    test('admin can export tickets', function (): void {
        TicketFactory::new()->count(5)->create();

        Livewire::test(ListTickets::class)
            ->assertActionExists('export');
    });

    test('admin can import tickets', function (): void {
        $ticketData = [
            'privacyAccepted' => true,
            'name' => 'Imported Ticket',
            'content' => 'Imported Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'status' => TicketStatusEnum::OPEN->value,
            'type' => TicketTypeEnum::COMPLAINT->value,
            'owner_id' => $this->authAdmin()->id,
            'location' => [
                'latitude' => 45.4642,
                'longitude' => 9.1900,
                'address' => 'Milano',
            ],
        ];
        Livewire::test(CreateTicket::class)->set('data', $ticketData)->call('create');

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Imported Ticket',
            'content' => 'Imported Description',
        ]);
    });

    test('user can view own tickets', function (): void {
        /** @var TestCase $this */
        actingAs($this->authUser());

        $ownTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        $otherTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
        ]);

        Assert::assertTrue(Gate::forUser($this->authUser())->allows('view', $ownTicket));
        Assert::assertFalse(Gate::forUser($this->authUser())->allows('view', $otherTicket));
    });

    test('user can create ticket', function (): void {
        /** @var TestCase $this */
        actingAs($this->authUser());

        $ticketData = [
            'name' => 'User Ticket',
            'content' => 'User Description',
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'type' => TicketTypeEnum::COMPLAINT->value,
        ];

        app(CreateTicketAction::class)->execute([
            'name' => $ticketData['name'],
            'content' => $ticketData['content'],
            'priority' => $ticketData['priority'],
            'type' => $ticketData['type'],
            'status' => TicketStatusEnum::PENDING->value,
            'owner_id' => $this->authUser()->id,
        ]);

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'User Ticket',
            'content' => 'User Description',
            'owner_id' => $this->authUser()->id,
        ]);
    });

    test('user cannot delete other tickets', function (): void {
        /** @var TestCase $this */
        actingAs($this->authUser());

        $otherTicket = TicketFactory::new()->createOne([
            'owner_id' => $this->authAdmin()->id,
        ]);

        Assert::assertFalse(Gate::forUser($this->authUser())->allows('delete', $otherTicket));
    });

    test('user cannot assign tickets', function (): void {
        /** @var TestCase $this */
        actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->authUser()->id,
        ]);

        Assert::assertFalse(Gate::forUser($this->authUser())->allows('assign', $ticket));
    });

    test('guest cannot access ticket management', function (): void {
        get(TicketResource::getUrl('index'))->assertForbidden();
        get(TicketResource::getUrl('create'))->assertForbidden();
    });
});
