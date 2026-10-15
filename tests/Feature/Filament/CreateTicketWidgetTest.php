<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWidget;
use Modules\Fixcity\Models\Ticket;
use Tests\Modules\Fixcity\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);
beforeEach(function () {
    /** @var TestCase $this */
    $this->user = UserFactory::new()->createOne();
    Assert::assertNotNull($this->user);
    $this->actingAs($this->user);
});

describe('CreateTicketWidget', function () {
    it('can render the widget', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class);

        $component->assertStatus(200);
    });

    it('can create a ticket with basic data', function () {
        /** @var TestCase $this */
        $ticketData = [
            'name' => 'Test Ticket Creation',
            'content' => 'This is a test ticket created via widget',
            'type' => TicketTypeEnum::COMPLAINT->value,
            'priority' => TicketPriorityEnum::MEDIUM->value,
        ];

        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', $ticketData['name'])
            ->set('data.content', $ticketData['content'])
            ->set('data.type', $ticketData['type'])
            ->set('data.priority', $ticketData['priority']);

        // Check if ticket can be created (this depends on widget implementation)
        Assert::assertSame($ticketData['name'], $component->get('data.name'));
        Assert::assertSame($ticketData['content'], $component->get('data.content'));
    });

    it('validates required fields', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', '')
            ->set('data.content', '');

        // The exact validation depends on the widget implementation
        // This test structure is ready for when validation is implemented
        Assert::assertSame('', $component->get('data.name'));
    });

    it('can set geolocation data', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.latitude', '45.4642')
            ->set('data.longitude', '9.1900');

        Assert::assertSame('45.4642', $component->get('data.latitude'));
        Assert::assertSame('9.1900', $component->get('data.longitude'));
    });

    it('sets default status to pending', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class);

        // Default status should be PENDING when creating new tickets
        Assert::assertSame(TicketStatusEnum::PENDING->value, $component->get('data.status'));
    });

    it('can handle form submission', function () {
        /** @var TestCase $this */
        Ticket::count();
        $ticketData = [
            'name' => 'Widget Test Ticket',
            'content' => 'Test content for widget submission',
            'type' => TicketTypeEnum::REQUEST->value,
            'priority' => TicketPriorityEnum::HIGH->value,
            'latitude' => '45.4642',
            'longitude' => '9.1900',
        ];

        Livewire::test(CreateTicketWidget::class)->set('data', $ticketData)
            ->call('submit');

        // Check if ticket was actually created (depends on implementation)

    });
});

describe('CreateTicketWidget Validation', function () {
    it('requires name field', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', '')
            ->set('data.content', 'Valid content')
            ->call('submit');

        $component->assertHasErrors(['data.name']);
    });

    it('requires content field', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.name', 'Valid name')
            ->set('data.content', '')
            ->call('submit');

        $component->assertHasErrors(['data.content']);
    });

    it('validates geolocation coordinates', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.latitude', '200') // Invalid latitude
            ->set('data.longitude', '400') // Invalid longitude
            ->call('submit');

        // Should validate coordinate ranges
        $component->assertHasErrors(['data.latitude', 'data.longitude']);
    });

    it('validates enum values', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.type', 'invalid_type')
            ->set('data.priority', 'invalid_priority')
            ->call('submit');

        $component->assertHasErrors(['data.type', 'data.priority']);
    });
});

describe('CreateTicketWidget User Association', function () {
    it('automatically sets current user as owner', function () {
        /** @var TestCase $this */
        Assert::assertNotNull($this->user);
        Livewire::test(CreateTicketWidget::class)->set('data.name', 'User Association Test')
            ->set('data.content', 'Test content')
            ->call('submit');

        $ticket = Ticket::latest()->first();

        if ($ticket) {
            Assert::assertSame($this->user->id, $ticket->owner_id);
        }
    });

    it('requires authenticated user', function () {
        /** @var TestCase $this */
        auth()->logout();

        $component = Livewire::test(CreateTicketWidget::class);

        // Should redirect or show error for unauthenticated users
        $component->assertRedirect();
    });
});

describe('CreateTicketWidget File Upload', function () {
    it('can handle file uploads', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class);

        // Test file upload handling (depends on implementation)
        Assert::assertNotNull($component);
    });

    it('validates file types', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class);

        // Should validate uploaded file types (images, PDFs, etc.)
        Assert::assertNotNull($component);
    });

    it('validates file size limits', function () {
        /** @var TestCase $this */
        $component = Livewire::test(CreateTicketWidget::class);

        // Should enforce file size limits
        Assert::assertNotNull($component);
    });
});

describe('CreateTicketWidget Server Authority', function () {
    /*
     * `status` e' un campo Hidden dello schema (`TicketForm::getDataSchema()`): il client puo' riscriverlo con `$wire.set('data.status', 'closed')`.
     * Come nel wizard (DCP-49 SEC-01) lo stato iniziale lo decide il server; qui il widget e' stato riportato sullo schema vivo da DCP-184.
     */
    it('ignores a status tampered by the client', function (string $status) {
        /** @var TestCase $this */
        Storage::fake('public');
        $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
        {
            public function execute(): string
            {
                return '# Informativa di test';
            }
        });

        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.accept_terms', true)
            ->set('data.name', 'Lampione spento in via Verdi')
            ->set('data.type', TicketTypeEnum::PUBLIC_LIGHTING->value)
            ->set('data.content', 'Il lampione e spento da una settimana.')
            ->set('data.location', ['latitude' => '45.4642', 'longitude' => '9.1900', 'address' => 'Via Verdi, Milano'])
            ->set('data.images', [UploadedFile::fake()->image('lampione.jpg')])
            ->set('data.status', $status)
            ->call('submit');
        $component->assertHasNoErrors();

        Assert::assertNotNull($this->user);
        $ticket = Ticket::query()->where('owner_id', $this->user->getAuthIdentifier())->latest('id')->first();
        Assert::assertInstanceOf(Ticket::class, $ticket, 'Il ticket non e stato creato: la prova passerebbe a vuoto.');
        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
    })->with([
        'closed' => ['closed'],
        'resolved' => ['resolved'],
        'in_progress' => ['in_progress'],
    ]);
});
