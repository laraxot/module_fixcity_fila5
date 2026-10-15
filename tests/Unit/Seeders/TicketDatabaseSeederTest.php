<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Modules\Fixcity\Database\Seeders\TicketDatabaseSeeder;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Tests\TestCase;

/**
 * Pest tests for STORY-135 TicketDatabaseSeeder.
 *
 * Verifies that the presentation seeder populates the public FO with a stable
 * demo dataset (15 tickets, Mogliano Veneto) and is idempotent across runs.
 *
 * @see docs/stories/STORY-135-presentation-ticket-seeder.md
 */

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    User::factory()->create();
});

describe('TicketDatabaseSeeder', function () {
    it('creates exactly 15 presentation tickets', function () {
        runTicketSeeder();

        $demoTickets = Ticket::query()->where('ticket_prefix', 'DEMO')->get();

        expect($demoTickets)->toHaveCount(15);
    });

    it('uses canonical codes DEMO-001 .. DEMO-015', function () {
        runTicketSeeder();

        $codes = Ticket::query()
            ->where('ticket_prefix', 'DEMO')
            ->pluck('code')
            ->sort()
            ->values()
            ->all();

        $expected = array_map(
            static fn (int $i) => sprintf('DEMO-%03d', $i),
            range(1, 15),
        );

        expect($codes)->toEqual($expected);
    });

    it('geocodes every demo ticket inside the Mogliano Veneto bounding box', function () {
        runTicketSeeder();

        $demoTickets = Ticket::query()->where('ticket_prefix', 'DEMO')->get();

        foreach ($demoTickets as $ticket) {
            $lat = (float) $ticket->latitude;
            $lng = (float) $ticket->longitude;

            expect($lat)->toBeBetween(45.540, 45.570)
                ->and($lng)->toBeBetween(12.230, 12.270);
        }
    });

    it('stores city metadata in the location payload', function () {
        runTicketSeeder();

        $demoTickets = Ticket::query()->where('ticket_prefix', 'DEMO')->get();

        foreach ($demoTickets as $ticket) {
            $location = $ticket->location;

            expect($location)->toBeArray()
                ->and($location['city'] ?? null)->toBe('Mogliano Veneto')
                ->and($location['province'] ?? null)->toBe('Treviso')
                ->and($location['country_code'] ?? null)->toBe('it');
        }
    });

    it('uses statuses that are publicly visible', function () {
        runTicketSeeder();

        $allowed = array_map(
            static fn (TicketStatusEnum $case) => $case->value,
            TicketStatusEnum::canViewByAll(),
        );

        $statuses = Ticket::query()
            ->where('ticket_prefix', 'DEMO')
            ->get()
            ->map(static fn (Ticket $ticket) => (string) $ticket->getRawOriginal('status'))
            ->all();

        expect($statuses)->not->toBeEmpty();

        foreach ($statuses as $status) {
            expect($status)->toBeIn($allowed);
        }
    });

    it('marks every demo ticket as visible on the public frontoffice', function () {
        runTicketSeeder();

        $demoTickets = Ticket::query()->where('ticket_prefix', 'DEMO')->get();

        foreach ($demoTickets as $ticket) {
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

        expect($secondRun)->toEqual($firstRun);
    });

    it('is idempotent on triple execution', function () {
        runTicketSeeder();
        runTicketSeeder();
        runTicketSeeder();

        expect(Ticket::query()->where('ticket_prefix', 'DEMO')->count())->toBe(15);
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
            ->and(Ticket::query()->where('ticket_prefix', 'DEMO')->count())->toBe(15);
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
