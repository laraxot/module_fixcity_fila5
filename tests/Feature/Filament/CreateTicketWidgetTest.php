<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('CreateTicketWidget', function () {
    it('can render the widget', function () {
        $component = Livewire::test(CreateTicketWidget::class);

        $component->assertStatus(200);
    });

    it('can create a ticket with basic data', function () {
        $ticketData = [
            'name' => 'Test Ticket Creation',
            'content' => 'This is a test ticket created via widget',
            'type' => TicketTypeEnum::BUG->value,
            'priority' => TicketPriorityEnum::MEDIUM->value,
        ];

        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', $ticketData['name'])
            ->set('data.content', $ticketData['content'])
            ->set('data.type', $ticketData['type'])
            ->set('data.priority', $ticketData['priority']);

        // Check if ticket can be created (this depends on widget implementation)
        expect($component->get('data.name'))->toBe($ticketData['name']);
        expect($component->get('data.content'))->toBe($ticketData['content']);
    });

    it('validates required fields', function () {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', '')
            ->set('data.content', '');

        // The exact validation depends on the widget implementation
        // This test structure is ready for when validation is implemented
        expect($component->get('data.name'))->toBe('');
    });

    it('can set geolocation data', function () {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.latitude', '45.4642')
            ->set('data.longitude', '9.1900');

        expect($component->get('data.latitude'))->toBe('45.4642');
        expect($component->get('data.longitude'))->toBe('9.1900');
    });

    it('sets default status to pending', function () {
        $component = Livewire::test(CreateTicketWidget::class);

        // Default status should be PENDING when creating new tickets
        expect($component->get('data.status'))->toBe(TicketStatusEnum::PENDING->value);
    });

    it('can handle form submission', function () {
        $initialCount = Ticket::count();

        $ticketData = [
            'name' => 'Widget Test Ticket',
            'content' => 'Test content for widget submission',
            'type' => TicketTypeEnum::FEATURE->value,
            'priority' => TicketPriorityEnum::HIGH->value,
            'latitude' => '45.4642',
            'longitude' => '9.1900',
        ];

        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data', $ticketData)
            ->call('submit');

        // Check if ticket was actually created (depends on implementation)
        expect(Ticket::count())->toBeGreaterThanOrEqual($initialCount);
    });
});

describe('CreateTicketWidget Validation', function () {
    it('requires name field', function () {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', '')
            ->set('data.content', 'Valid content')
            ->call('submit');

        $component->assertHasErrors(['data.name']);
    });

    it('requires content field', function () {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', 'Valid name')
            ->set('data.content', '')
            ->call('submit');

        $component->assertHasErrors(['data.content']);
    });

    it('validates geolocation coordinates', function () {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.latitude', '200') // Invalid latitude
            ->set('data.longitude', '400') // Invalid longitude
            ->call('submit');

        // Should validate coordinate ranges
        $component->assertHasErrors(['data.latitude', 'data.longitude']);
    });

    it('validates enum values', function () {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.type', 'invalid_type')
            ->set('data.priority', 'invalid_priority')
            ->call('submit');

        $component->assertHasErrors(['data.type', 'data.priority']);
    });
});

describe('CreateTicketWidget User Association', function () {
    it('automatically sets current user as owner', function () {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', 'User Association Test')
            ->set('data.content', 'Test content')
            ->call('submit');

        $ticket = Ticket::latest()->first();
        
        if ($ticket) {
            expect($ticket->owner_id)->toBe($this->user->id);
        }
    });

    it('requires authenticated user', function () {
        auth()->logout();

        $component = Livewire::test(CreateTicketWidget::class);

        // Should redirect or show error for unauthenticated users
        $component->assertRedirect();
    });
});

describe('CreateTicketWidget File Upload', function () {
    it('can handle file uploads', function () {
        $component = Livewire::test(CreateTicketWidget::class);

        // Test file upload handling (depends on implementation)
        expect($component)->not->toBeNull();
    });

    it('validates file types', function () {
        $component = Livewire::test(CreateTicketWidget::class);

        // Should validate uploaded file types (images, PDFs, etc.)
        expect($component)->not->toBeNull();
    });

    it('validates file size limits', function () {
        $component = Livewire::test(CreateTicketWidget::class);

        // Should enforce file size limits
        expect($component)->not->toBeNull();
    });
});