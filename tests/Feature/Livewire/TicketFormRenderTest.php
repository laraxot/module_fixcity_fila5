<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Livewire;

use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Pest\Laravel\actingAs;

uses(TestCase::class);
beforeEach(function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });

    $this->user = UserFactory::new()->createOne();
    actingAs($this->authUser());
});

describe('CreateTicketWidget current contract', function (): void {
    test('renders the accessible citizen form', function (): void {
        Livewire::test(CreateTicketWidget::class)
            ->assertSuccessful()
            ->assertSee('accept_terms')
            ->assertSee('name')
            ->assertSee('content');
    });

    test('creates a pending ticket for the authenticated citizen', function (): void {
        // TicketForm SSoT: CoordinatePicker su `location` (lat/lng annidati), non campi piatti.
        Livewire::test(CreateTicketWidget::class)
            ->set('data', [
                'accept_terms' => true,
                'name' => 'Test Ticket',
                'content' => 'Test Description',
                'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
                'priority' => TicketPriorityEnum::MEDIUM->value,
                'location' => [
                    'latitude' => 41.9028,
                    'longitude' => 12.4964,
                    'address' => 'Roma',
                ],
            ])
            ->upload('data.images', [UploadedFile::fake()->image('ticket.jpg')], true)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHasRow('tickets', [
            'name' => 'Test Ticket',
            'content' => 'Test Description',
            'type' => TicketTypeEnum::ROAD_MAINTENANCE->value,
            'priority' => TicketPriorityEnum::MEDIUM->value,
            'owner_id' => $this->authUser()->id,
            'status' => 'pending',
        ]);
    });

    test('keeps the form state available for geolocation UX', function (): void {
        $component = Livewire::test(CreateTicketWidget::class)
            ->set('data.location', [
                'latitude' => 45.4642,
                'longitude' => 9.1900,
                'address' => 'Milano',
            ]);

        /** @var array<string, mixed> $location */
        $location = $component->get('data.location');
        Assert::assertSame(45.4642, $location['latitude'] ?? null);
        Assert::assertSame(9.1900, $location['longitude'] ?? null);
    });

    test('associates a created ticket with the authenticated user', function (): void {
        Livewire::test(CreateTicketWidget::class)
            ->set('data', [
                'accept_terms' => true,
                'name' => 'Owned Ticket',
                'content' => 'A citizen-owned report',
                'type' => TicketTypeEnum::COMPLAINT->value,
                'priority' => TicketPriorityEnum::LOW->value,
                'location' => [
                    'latitude' => 45.4642,
                    'longitude' => 9.1900,
                    'address' => 'Milano',
                ],
            ])
            ->upload('data.images', [UploadedFile::fake()->image('owned-ticket.jpg')], true)
            ->call('submit');

        Assert::assertTrue(
            Ticket::query()->where('name', 'Owned Ticket')
                ->where('owner_id', $this->authUser()->id)
                ->exists(),
        );
    });
});
