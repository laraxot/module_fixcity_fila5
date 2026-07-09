<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Livewire;

use function Safe\json_encode;
use Modules\Fixcity\Database\Factories\TicketFactory;
use PHPUnit\Framework\Assert;
use Modules\Tenant\Database\Factories\TenantFactory;
use Modules\User\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Modules\Fixcity\Models\Ticket;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\User;
use Modules\Fixcity\Tests\TestCase;
use function Pest\Laravel\actingAs;

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
$this->user = UserFactory::new()->createOne();
});

    test(' validates due date is in future', function (): void {
actingAs($this->authUser());

        $pastDate = now()->subDays(1)->toDateString();

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('due_date', $pastDate)
            ->call('save')
            ->assertHasErrors([
                'due_date' => 'after',
            ]);
    });

    test(' can set estimated hours', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('estimated_hours', 4.5)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'estimated_hours' => 4.5,
        ]);
    });

    test(' validates estimated hours is positive', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('estimated_hours', -1)
            ->call('save')
            ->assertHasErrors([
                'estimated_hours' => 'min',
            ]);
    });

    test(' can set contact information', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('contact_name', 'John Doe')
            ->set('contact_phone', '+39 123 456 7890')
            ->set('contact_email', 'john@example.com')
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'contact_name' => 'John Doe',
            'contact_phone' => '+39 123 456 7890',
            'contact_email' => 'john@example.com',
        ]);
    });

    test(' validates contact email format', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('contact_email', 'invalid-email')
            ->call('save')
            ->assertHasErrors([
                'contact_email' => 'email',
            ]);
    });

    test(' can set custom fields', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('custom_fields', [
                'severity' => 'high',
                'area' => 'downtown',
            ])
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'custom_fields' => json_encode([
                'severity' => 'high',
                'area' => 'downtown',
            ]),
        ]);
    });

    test(' can set tags', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('tags', ['urgent', 'infrastructure'])
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
        ]);

        $ticket = Ticket::query()->where('name', 'Test Ticket')->first();
        Assert::assertNotNull($ticket);
    });

    test(' can set related tickets', function (): void {
actingAs($this->authUser());

        $relatedTicket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('related_ticket_ids', [$relatedTicket->id])
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
        ]);

        $ticket = Ticket::query()->where('name', 'Test Ticket')->first();
        Assert::assertNotNull($ticket);
    });

    test(' can set team assignment', function (): void {
actingAs($this->authUser());

        /** @var \Modules\User\Models\Team $team */
        $team = $this->authUser()->teams()->create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('team_id', $team->id)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
        ]);
    });

    test(' can set tenant assignment', function (): void {
actingAs($this->authUser());

        $tenant = TenantFactory::new()->createOne();

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('tenant_id', $tenant->id)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'tenant_id' => $tenant->id,
        ]);
    });

    test(' can set visibility', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('is_public', true)
            ->set('is_featured', true)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'is_public' => true,
            'is_featured' => true,
        ]);
    });

    test(' can set notification preferences', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('notify_on_update', true)
            ->set('notify_on_comment', true)
            ->set('notify_on_resolution', true)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'notify_on_update' => true,
            'notify_on_comment' => true,
            'notify_on_resolution' => true,
        ]);
    });

    test(' can reset form', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->call('resetForm')
            ->assertSet('title', '')
            ->assertSet('description', '')
            ->assertSet('type', '');
    });

    test(' can cancel form', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->call('cancel')
            ->assertRedirect();
    });

    test(' requires authentication', function (): void {
Livewire::test('ticket-form')
            ->assertRedirect('/login');
    });

test(' can preview ticket before saving', function (): void {
        actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->call('preview')
            ->assertSee('Test Ticket')
            ->assertSee('Test Description')
            ->assertSee('road_maintenance');
    });
