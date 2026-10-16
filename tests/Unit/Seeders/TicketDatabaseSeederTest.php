<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Modules\Fixcity\Actions\GenerateTicketsJsonAction;
use Modules\Fixcity\Database\Seeders\DemoUsersSeeder;
use Modules\Fixcity\Database\Seeders\TicketDatabaseSeeder;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;

/**
 * Scopo: il seeder di presentazione popola il front office pubblico con un
 * dataset demo stabile (20 ticket, Mogliano Veneto) ed e' idempotente.
 *
 * Contratto reale (non piu' "15 ticket"): `TicketDatabaseSeeder::presentationRecords()`
 * ha DEMO-001..DEMO-020, assegnati al cittadino `DemoUsersSeeder::CITIZEN_EMAIL`
 * (se manca, il seeder salta tutto: STORY-521).
 *
 * Isolamento: TestCase di modulo con DatabaseTransactions (mai RefreshDatabase,
 * che farebbe migrate:fresh sul database configurato) e GenerateTicketsJsonAction
 * sostituita, perche' il seeder altrimenti riscrive `public_html/data/tickets.json`.
 *
 * @see docs/bmad/stories/STORY-521-demo-ticket-owner-assignment.md
 * @see docs/stories/dcp-241-test-geocodifica-ticket-contro-job-inesistente.story.md
 */

uses(TestCase::class);

beforeEach(function (): void {
    UserFactory::new()->create(['email' => DemoUsersSeeder::CITIZEN_EMAIL]);

    Storage::fake('public');

    $geoJsonAction = Mockery::mock(GenerateTicketsJsonAction::class);
    $geoJsonAction->shouldReceive('execute')->atLeast()->once()->andReturn('/tmp/fixcity-ticket-seeder-test.json');
    app()->instance(GenerateTicketsJsonAction::class, $geoJsonAction);
});

describe('TicketDatabaseSeeder', function () {
    it('creates exactly 20 presentation tickets', function () {
        runTicketSeeder();

        $demoTickets = Ticket::query()->where('ticket_prefix', 'DEMO')->get();

        expect($demoTickets)->toHaveCount(20);
    });

    it('uses canonical codes DEMO-001 .. DEMO-020', function () {
        runTicketSeeder();

        $codes = Ticket::query()
            ->where('ticket_prefix', 'DEMO')
            ->pluck('code')
            ->sort()
            ->values()
            ->all();

        $expected = array_map(
            static fn (int $i) => sprintf('DEMO-%03d', $i),
            range(1, 20),
        );

        expect($codes)->toEqual($expected);
    });

    it('geocodes every demo ticket inside the Mogliano Veneto bounding box', function () {
        foreach (seededDemoTickets() as $ticket) {
            $lat = (float) $ticket->latitude;
            $lng = (float) $ticket->longitude;

            expect($lat)->toBeBetween(45.540, 45.570)
                ->and($lng)->toBeBetween(12.230, 12.270);
        }
    });

    it('stores city metadata in the location payload', function () {
        foreach (seededDemoTickets() as $ticket) {
            $location = $ticket->location;

            expect($location)->toBeArray()
                ->and($location['city'] ?? null)->toBe('Mogliano Veneto')
                ->and($location['province'] ?? null)->toBe('Treviso')
                ->and($location['country_code'] ?? null)->toBe('it');
        }
    });

    it('uses statuses that are publicly visible', function () {
        $allowed = array_map(
            static fn (TicketStatusEnum $case) => $case->value,
            TicketStatusEnum::canViewByAll(),
        );

        $statuses = seededDemoTickets()
            ->map(static fn (Ticket $ticket): string => $ticket->resolveTicketStatusValue())
            ->all();

        foreach ($statuses as $status) {
            expect($status)->toBeIn($allowed);
        }
    });

    it('marks every demo ticket as visible on the public frontoffice', function () {
        foreach (seededDemoTickets() as $ticket) {
            expect($ticket->isVisibleOnPublicFrontoffice())->toBeTrue();
        }
    });

    it('keeps slugs stable across multiple runs', function () {
        runTicketSeeder();

        $firstRun = Ticket::query()
            ->where('ticket_prefix', 'DEMO')
            ->pluck('slug', 'code')
            ->all();

        runTicketSeeder();

        $secondRun = Ticket::query()
            ->where('ticket_prefix', 'DEMO')
            ->pluck('slug', 'code')
            ->all();

        expect($firstRun)->toHaveCount(20)
            ->and($secondRun)->toEqual($firstRun);
    });

    it('is idempotent on triple execution', function () {
        runTicketSeeder();
        runTicketSeeder();
        runTicketSeeder();

        expect(Ticket::query()->where('ticket_prefix', 'DEMO')->count())->toBe(20);
    });

    it('does not touch legacy pending tickets already in the database', function () {
        $legacyOwnerId = User::query()->orderBy('id')->value('id');
        Ticket::query()->create([
            'name' => 'Legacy pending ticket',
            'slug' => 'legacy-pending-ticket',
            'content' => 'Legacy Bologna fixture, kept untouched by the demo seeder.',
            'code' => 'LEGACY-001',
            'ticket_prefix' => 'BOLOGNA',
            'owner_id' => $legacyOwnerId,
            'status' => TicketStatusEnum::PENDING->value,
            'latitude' => '44.4949',
            'longitude' => '11.3426',
        ]);

        runTicketSeeder();

        expect(Ticket::query()->where('code', 'LEGACY-001')->count())->toBe(1)
            ->and(Ticket::query()->where('ticket_prefix', 'DEMO')->count())->toBe(20);
    });
});

function runTicketSeeder(): void
{
    $exit = Artisan::call('db:seed', [
        '--class' => TicketDatabaseSeeder::class,
        '--force' => true,
    ]);

    expect($exit)->toBe(0);
}

/**
 * Esegue il seeder e restituisce i ticket demo; non vuota per costruzione, cosi'
 * i test che iterano non passano a vuoto se il seeder salta i ticket.
 *
 * @return Collection<int, Ticket>
 */
function seededDemoTickets(): Collection
{
    runTicketSeeder();

    $demoTickets = Ticket::query()->where('ticket_prefix', 'DEMO')->get();

    expect($demoTickets)->not->toBeEmpty();

    return $demoTickets;
}
