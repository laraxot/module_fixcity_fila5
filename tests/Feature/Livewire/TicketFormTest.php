<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Livewire;

use Illuminate\Http\UploadedFile;
use Modules\Tenant\Models\Tenant;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class TicketFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_render_ticket_form()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->assertSee('Create Ticket')
                ->assertSee('Title')
                ->assertSee('Description')
                ->assertSee('Type')
                ->assertSee('Priority')
                ->assertSee('Location');
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

        Livewire::test('ticket-form')
                ->set('title', $ticketData['title'])
                ->set('description', $ticketData['description'])
                ->set('type', $ticketData['type'])
                ->set('priority', $ticketData['priority'])
                ->set('location', $ticketData['location'])
                ->set('latitude', $ticketData['latitude'])
                ->set('longitude', $ticketData['longitude'])
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'type' => 'road_maintenance',
            'priority' => 'medium',
            'owner_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', '')
                ->set('description', '')
                ->set('type', '')
                ->call('save')
                ->assertHasErrors([
                    'title' => 'required',
                    'description' => 'required',
                    'type' => 'required',
                ]);
    }

    /** @test */
    public function it_validates_title_length()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', str_repeat('a', 256))
                ->call('save')
                ->assertHasErrors([
                    'title' => 'max',
                ]);
    }

    /** @test */
    public function it_validates_description_length()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('description', str_repeat('a', 1001))
                ->call('save')
                ->assertHasErrors([
                    'description' => 'max',
                ]);
    }

    /** @test */
    public function it_validates_type_enum_values()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('type', 'invalid_type')
                ->call('save')
                ->assertHasErrors([
                    'type' => 'in',
                ]);
    }

    /** @test */
    public function it_validates_priority_enum_values()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('priority', 'invalid_priority')
                ->call('save')
                ->assertHasErrors([
                    'priority' => 'in',
                ]);
    }

    /** @test */
    public function it_validates_coordinates_range()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('latitude', 91.0)
                ->set('longitude', 181.0)
                ->call('save')
                ->assertHasErrors([
                    'latitude' => 'between',
                    'longitude' => 'between',
                ]);
    }

    /** @test */
    public function it_can_edit_existing_ticket()
    {
        $this->actingAs($this->user);
        
        $ticket = Ticket::factory()->create(['owner_id' => $this->user->id]);

        Livewire::test('ticket-form', ['ticket' => $ticket])
                ->set('title', 'Updated Title')
                ->set('description', 'Updated Description')
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);
    }

    /** @test */
    public function it_can_upload_attachments()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('photo.jpg');

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('attachments', [$file])
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        // Verify file was uploaded
        $this->assertDatabaseHas('media', [
            'file_name' => 'photo.jpg',
        ]);
    }

    /** @test */
    public function it_validates_file_types()
    {
        $this->actingAs($this->user);

        $invalidFile = UploadedFile::fake()->create('document.exe', 100);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('attachments', [$invalidFile])
                ->call('save')
                ->assertHasErrors([
                    'attachments.*' => 'mimes',
                ]);
    }

    /** @test */
    public function it_validates_file_size()
    {
        $this->actingAs($this->user);

        $largeFile = UploadedFile::fake()->create('large.jpg', 10241); // 10MB + 1KB

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('attachments', [$largeFile])
                ->call('save')
                ->assertHasErrors([
                    'attachments.*' => 'max',
                ]);
    }

    /** @test */
    public function it_can_set_due_date()
    {
        $this->actingAs($this->user);

        $dueDate = now()->addDays(7)->toDateString();

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('due_date', $dueDate)
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'due_date' => $dueDate,
        ]);
    }

    /** @test */
    public function it_validates_due_date_is_in_future()
    {
        $this->actingAs($this->user);

        $pastDate = now()->subDays(1)->toDateString();

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('due_date', $pastDate)
                ->call('save')
                ->assertHasErrors([
                    'due_date' => 'after',
                ]);
    }

    /** @test */
    public function it_can_set_estimated_hours()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('estimated_hours', 4.5)
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'estimated_hours' => 4.5,
        ]);
    }

    /** @test */
    public function it_validates_estimated_hours_is_positive()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('estimated_hours', -1)
                ->call('save')
                ->assertHasErrors([
                    'estimated_hours' => 'min',
                ]);
    }

    /** @test */
    public function it_can_set_contact_information()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('contact_name', 'John Doe')
                ->set('contact_phone', '+39 123 456 7890')
                ->set('contact_email', 'john@example.com')
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'contact_name' => 'John Doe',
            'contact_phone' => '+39 123 456 7890',
            'contact_email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function it_validates_contact_email_format()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('contact_email', 'invalid-email')
                ->call('save')
                ->assertHasErrors([
                    'contact_email' => 'email',
                ]);
    }

    /** @test */
    public function it_can_set_custom_fields()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('custom_fields', [
                    'severity' => 'high',
                    'area' => 'downtown',
                ])
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'custom_fields' => json_encode([
                'severity' => 'high',
                'area' => 'downtown',
            ]),
        ]);
    }

    /** @test */
    public function it_can_set_tags()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('tags', ['urgent', 'infrastructure'])
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
        ]);

        // Verify tags were attached
        $ticket = Ticket::where('title', 'Test Ticket')->first();
        $this->assertTrue($ticket->tags->pluck('name')->contains('urgent'));
        $this->assertTrue($ticket->tags->pluck('name')->contains('infrastructure'));
    }

    /** @test */
    public function it_can_set_related_tickets()
    {
        $this->actingAs($this->user);
        
        $relatedTicket = Ticket::factory()->create(['owner_id' => $this->user->id]);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('related_ticket_ids', [$relatedTicket->id])
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
        ]);

        // Verify relation was created
        $ticket = Ticket::where('title', 'Test Ticket')->first();
        $this->assertTrue($ticket->relations->contains($relatedTicket));
    }

    /** @test */
    public function it_can_set_team_assignment()
    {
        $this->actingAs($this->user);
        
        $team = $this->user->teams()->create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('team_id', $team->id)
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function it_can_set_tenant_assignment()
    {
        $this->actingAs($this->user);
        
        $tenant = Tenant::factory()->create();

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('tenant_id', $tenant->id)
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'tenant_id' => $tenant->id,
        ]);
    }

    /** @test */
    public function it_can_set_visibility()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('is_public', true)
                ->set('is_featured', true)
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'is_public' => true,
            'is_featured' => true,
        ]);
    }

    /** @test */
    public function it_can_set_notification_preferences()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->set('notify_on_update', true)
                ->set('notify_on_comment', true)
                ->set('notify_on_resolution', true)
                ->call('save')
                ->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'notify_on_update' => true,
            'notify_on_comment' => true,
            'notify_on_resolution' => true,
        ]);
    }

    /** @test */
    public function it_can_reset_form()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->call('resetForm')
                ->assertSet('title', '')
                ->assertSet('description', '')
                ->assertSet('type', '');
    }

    /** @test */
    public function it_can_cancel_form()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->call('cancel')
                ->assertRedirect();
    }

    /** @test */
    public function it_requires_authentication()
    {
        Livewire::test('ticket-form')
                ->assertRedirect('/login');
    }

    /** @test */
    public function it_can_preview_ticket_before_saving()
    {
        $this->actingAs($this->user);

        Livewire::test('ticket-form')
                ->set('title', 'Test Ticket')
                ->set('description', 'Test Description')
                ->set('type', 'road_maintenance')
                ->call('preview')
                ->assertSee('Test Ticket')
                ->assertSee('Test Description')
                ->assertSee('road_maintenance');
    }
}
