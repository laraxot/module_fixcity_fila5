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

beforeEach(function (): void {
    /** @var \Modules\Fixcity\Tests\TestCase $this */
$this->user = UserFactory::new()->createOne();
});

describe('Ticket Form', function (): void {
    test(' can render ticket form', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->assertSee('Create Ticket')
            ->assertSee('Title')
            ->assertSee('Description')
            ->assertSee('Type')
            ->assertSee('Priority')
            ->assertSee('Location');
    });

    test(' can create new ticket', function (): void {
actingAs($this->authUser());

        $ticketData = [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'type' => 'road_maintenance',
            'priority' => 'medium',
            'location' => 'Via Roma 123',
            'latitude' => 41.9028,
            'longitude' => 12.4964,
        ];

        Livewire::test('ticket-form')
            ->set('name', $ticketData['name'])
            ->set('content', $ticketData['content'])
            ->set('type', $ticketData['type'])
            ->set('priority', $ticketData['priority'])
            ->set('location', $ticketData['location'])
            ->set('latitude', $ticketData['latitude'])
            ->set('longitude', $ticketData['longitude'])
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'type' => 'road_maintenance',
            'priority' => 'medium',
            'owner_id' => $this->authUser()->id,
            'status' => 'pending',
        ]);
    });

    test(' validates required fields', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', '')
            ->set('content', '')
            ->set('type', '')
            ->call('save')
            ->assertHasErrors([
                'name' => 'required',
                'content' => 'required',
                'type' => 'required',
            ]);
    });

    test(' validates title length', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors([
                'name' => 'max',
            ]);
    });

    test(' validates description length', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('content', str_repeat('a', 1001))
            ->call('save')
            ->assertHasErrors([
                'content' => 'max',
            ]);
    });

    test(' validates type enum values', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('type', 'invalid_type')
            ->call('save')
            ->assertHasErrors([
                'type' => 'in',
            ]);
    });

    test(' validates priority enum values', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('priority', 'invalid_priority')
            ->call('save')
            ->assertHasErrors([
                'priority' => 'in',
            ]);
    });

    test(' validates coordinates range', function (): void {
actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('latitude', 91.0)
            ->set('longitude', 181.0)
            ->call('save')
            ->assertHasErrors([
                'latitude' => 'between',
                'longitude' => 'between',
            ]);
    });

    test(' can edit existing ticket', function (): void {
actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        Livewire::test('ticket-form', ['ticket' => $ticket])
            ->set('name', 'Updated Title')
            ->set('content', 'Updated Description')
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'id' => $ticket->id,
            'name' => 'Updated Title',
            'content' => 'Updated Description',
        ]);
    });

    test(' can upload attachments', function (): void {
actingAs($this->authUser());

        $file = UploadedFile::fake()->image('photo.jpg');

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('attachments', [$file])
            ->call('save')
            ->assertRedirect();

        // Verify file was uploaded
        $this->assertDatabaseHasRow('media', [
            'file_name' => 'photo.jpg',
        ]);
    });

    test(' validates file types', function (): void {
actingAs($this->authUser());

        $invalidFile = UploadedFile::fake()->create('document.exe', 100);

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('attachments', [$invalidFile])
            ->call('save')
            ->assertHasErrors([
                'attachments.*' => 'mimes',
            ]);
    });

    test(' validates file size', function (): void {
actingAs($this->authUser());

        $largeFile = UploadedFile::fake()->create('large.jpg', 10241); // 10MB + 1KB

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('attachments', [$largeFile])
            ->call('save')
            ->assertHasErrors([
                'attachments.*' => 'max',
            ]);
    });

    test(' can set due date', function (): void {
actingAs($this->authUser());

        $dueDate = now()->addDays(7)->toDateString();

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('due_date', $dueDate)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'due_date' => $dueDate,
        ]);
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
});
