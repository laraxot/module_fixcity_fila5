<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

beforeEach(function (): void {
    /** @var TestCase $this */
    $this->user = UserFactory::new()->createOne();
});

describe('canonical ticket creation page', function (): void {
    test('renders for an authenticated citizen', function (): void {
        /** @var TestCase $this */
        $this->actingAs($this->authUser());

        $this->get('/it/tickets/create')
            ->assertOk()
            ->assertSee('Segnalazione disservizio');
    });

    test('exposes the canonical accessible Livewire widget', function (): void {
        /** @var TestCase $this */
        $this->actingAs($this->authUser());

        Livewire::test(CreateTicketWidget::class)
            ->assertSuccessful()
            ->assertSee('accept_terms')
            ->assertSee('name')
            ->assertSee('content');
    });

    test('keeps the page response within the expected performance budget', function (): void {
        /** @var TestCase $this */
        $this->actingAs($this->authUser());

        $startedAt = microtime(true);
        $response = $this->get('/it/tickets/create');

        $response->assertOk();
        Assert::assertLessThan(2.0, microtime(true) - $startedAt);
    });
});

describe('public ticket listing page', function (): void {
    test('guest tracking page renders localized labels', function (): void {
        /** @var TestCase $this */
        $this->get('/it/tickets/track')
            ->assertOk()
            ->assertSee('Traccia la segnalazione')
            ->assertSee('Inserisci il codice ricevuto alla conferma per vedere lo stato.')
            ->assertDontSee('fixcity::ticket.track.title');
    });

    test('renders the empty state without an empty filter rail', function (): void {
        /** @var TestCase $this */
        $this->get('/it/tickets')
            ->assertOk()
            ->assertSee('Nessuna segnalazione trovata.')
            ->assertDontSee('Nessuna categoria disponibile al momento.');
    });

    test('renders live map and list without serializing capability codes', function (): void {
        /** @var TestCase $this */
        TicketFactory::new()->createOne([            'name' => 'Lampione da riparare',
            'status' => TicketStatusEnum::IN_PROGRESS,
            'code' => 'TCK-PRIVATE-CAPABILITY-9981',
            'location' => ['lat' => 45.4642, 'lng' => 9.1900],
        ]);

        $this->get('/it/tickets')
            ->assertOk()
            ->assertSee('Lampione da riparare')
            ->assertSee('map-lit', false)
            ->assertSee('/api/tickets/geojson')
            ->assertSee('data-reference-total="1"', false)
            ->assertSee('data-count="1"', false)
            ->assertDontSee('TCK-PRIVATE-CAPABILITY-9981')
            ->assertDontSee('Mappa interattiva delle segnalazioni');
    });

    test('applies status filters to live list results', function (): void {
        /** @var TestCase $this */
        TicketFactory::new()->createOne([
            'name' => 'Stato in lavorazione visibile',
            'status' => TicketStatusEnum::IN_PROGRESS,
            'location' => ['lat' => 45.4642, 'lng' => 9.1900],
        ]);
        TicketFactory::new()->createOne([
            'name' => 'Stato aperto non selezionato',
            'status' => TicketStatusEnum::OPEN,
            'location' => ['lat' => 45.4742, 'lng' => 9.1800],
        ]);

        $this->get('/it/tickets?statuses%5B%5D=in_progress')
            ->assertOk()
            ->assertSee('Stato in lavorazione visibile')
            ->assertDontSee('Stato aperto non selezionato');
    });

    test('paginates the live list and keeps the current view on later pages', function (): void {
        /** @var TestCase $this */
        $oldest = TicketFactory::new()->createOne([
            'name' => 'Segnalazione paginata più vecchia',
            'status' => TicketStatusEnum::IN_PROGRESS,
            'location' => ['lat' => 45.4642, 'lng' => 9.1900],
            'created_at' => now()->subMinute(),
        ]);
        for ($index = 0; $index < 20; $index++) {
            TicketFactory::new()->createOne([
                'name' => 'Segnalazione pagina uno '.$index,
                'status' => TicketStatusEnum::IN_PROGRESS,
                'location' => ['lat' => 45.4642 + ($index / 1000), 'lng' => 9.1900],
            ]);
        }

        $this->get('/it/tickets?page=2')
            ->assertOk()
            ->assertSee('Segnalazione paginata più vecchia')
            ->assertSee('Pagina 2')
            ->assertSee('data-ex-disservizio2')
            ->assertDontSee('Segnalazione pagina uno 19');

        $this->assertNotNull($oldest->id);
    });
});
