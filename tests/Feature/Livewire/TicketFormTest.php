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

class TicketFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->createOne();
    }

    /** @test */
    public function it_can_render_ticket_form(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->assertSee('Create Ticket')
            ->assertSee('Title')
            ->assertSee('Description')
            ->assertSee('Type')
            ->assertSee('Priority')
            ->assertSee('Location');
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

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'type' => 'road_maintenance',
            'priority' => 'medium',
            'owner_id' => $this->authUser()->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_validates_required_fields(): void {
        $this->actingAs($this->authUser());

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
    }

    /** @test */
    public function it_validates_title_length(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors([
                'name' => 'max',
            ]);
    }

    /** @test */
    public function it_validates_description_length(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('content', str_repeat('a', 1001))
            ->call('save')
            ->assertHasErrors([
                'content' => 'max',
            ]);
    }

    /** @test */
    public function it_validates_type_enum_values(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('type', 'invalid_type')
            ->call('save')
            ->assertHasErrors([
                'type' => 'in',
            ]);
    }

    /** @test */
    public function it_validates_priority_enum_values(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('priority', 'invalid_priority')
            ->call('save')
            ->assertHasErrors([
                'priority' => 'in',
            ]);
    }

    /** @test */
    public function it_validates_coordinates_range(): void {
        $this->actingAs($this->authUser());

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
    public function it_can_edit_existing_ticket(): void {
        $this->actingAs($this->authUser());

        $ticket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        Livewire::test('ticket-form', ['ticket' => $ticket])
            ->set('name', 'Updated Title')
            ->set('content', 'Updated Description')
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'name' => 'Updated Title',
            'content' => 'Updated Description',
        ]);
    }

    /** @test */
    public function it_can_upload_attachments(): void {
        $this->actingAs($this->authUser());

        $file = UploadedFile::fake()->image('photo.jpg');

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('attachments', [$file])
            ->call('save')
            ->assertRedirect();

        // Verify file was uploaded
        $this->assertDatabaseHas('media', [
            'file_name' => 'photo.jpg',
        ]);
    }

    /** @test */
    public function it_validates_file_types(): void {
        $this->actingAs($this->authUser());

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
    }

    /** @test */
    public function it_validates_file_size(): void {
        $this->actingAs($this->authUser());

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
    }

    /** @test */
    public function it_can_set_due_date(): void {
        $this->actingAs($this->authUser());

        $dueDate = now()->addDays(7)->toDateString();

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('due_date', $dueDate)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'due_date' => $dueDate,
        ]);
    }

    /** @test */
    public function it_validates_due_date_is_in_future(): void {
        $this->actingAs($this->authUser());

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
    }

    /** @test */
    public function it_can_set_estimated_hours(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('estimated_hours', 4.5)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'estimated_hours' => 4.5,
        ]);
    }

    /** @test */
    public function it_validates_estimated_hours_is_positive(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('estimated_hours', -1)
            ->call('save')
            ->assertHasErrors([
                'estimated_hours' => 'min',
            ]);
    }

    /** @test */
    public function it_can_set_contact_information(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('contact_name', 'John Doe')
            ->set('contact_phone', '+39 123 456 7890')
            ->set('contact_email', 'john@example.com')
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'contact_name' => 'John Doe',
            'contact_phone' => '+39 123 456 7890',
            'contact_email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function it_validates_contact_email_format(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('contact_email', 'invalid-email')
            ->call('save')
            ->assertHasErrors([
                'contact_email' => 'email',
            ]);
    }

    /** @test */
    public function it_can_set_custom_fields(): void {
        $this->actingAs($this->authUser());

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

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'custom_fields' => json_encode([
                'severity' => 'high',
                'area' => 'downtown',
            ]),
        ]);
    }

    /** @test */
    public function it_can_set_tags(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('tags', ['urgent', 'infrastructure'])
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
        ]);

        $ticket = Ticket::query()->where('name', 'Test Ticket')->first();
        Assert::assertNotNull($ticket);
    }

    /** @test */
    public function it_can_set_related_tickets(): void {
        $this->actingAs($this->authUser());

        $relatedTicket = TicketFactory::new()->createOne(['owner_id' => $this->authUser()->id]);

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('related_ticket_ids', [$relatedTicket->id])
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
        ]);

        $ticket = Ticket::query()->where('name', 'Test Ticket')->first();
        Assert::assertNotNull($ticket);
    }

    /** @test */
    public function it_can_set_team_assignment(): void {
        $this->actingAs($this->authUser());

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

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
        ]);
    }

    /** @test */
    public function it_can_set_tenant_assignment(): void {
        $this->actingAs($this->authUser());

        $tenant = TenantFactory::new()->createOne();

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('tenant_id', $tenant->id)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'tenant_id' => $tenant->id,
        ]);
    }

    /** @test */
    public function it_can_set_visibility(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('is_public', true)
            ->set('is_featured', true)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'is_public' => true,
            'is_featured' => true,
        ]);
    }

    /** @test */
    public function it_can_set_notification_preferences(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->set('notify_on_update', true)
            ->set('notify_on_comment', true)
            ->set('notify_on_resolution', true)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'name' => 'Test Ticket',
            'notify_on_update' => true,
            'notify_on_comment' => true,
            'notify_on_resolution' => true,
        ]);
    }

    /** @test */
    public function it_can_reset_form(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->call('resetForm')
            ->assertSet('title', '')
            ->assertSet('description', '')
            ->assertSet('type', '');
    }

    /** @test */
    public function it_can_cancel_form(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->call('cancel')
            ->assertRedirect();
    }

    /** @test */
    public function it_requires_authentication(): void {
        Livewire::test('ticket-form')
            ->assertRedirect('/login');
    }

    /** @test */
    public function it_can_preview_ticket_before_saving(): void {
        $this->actingAs($this->authUser());

        Livewire::test('ticket-form')
            ->set('name', 'Test Ticket')
            ->set('content', 'Test Description')
            ->set('type', 'road_maintenance')
            ->call('preview')
            ->assertSee('Test Ticket')
            ->assertSee('Test Description')
            ->assertSee('road_maintenance');
    }
}
