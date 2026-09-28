<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Database;

use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Comment\Models\Comment;
use Modules\Fixcity\Database\Seeders\CategorySeeder;
use Modules\Fixcity\Database\Seeders\Support\DemoCategoryCatalog;
use Modules\Fixcity\Database\Seeders\Support\DemoText;
use Modules\Fixcity\Database\Seeders\Support\DemoWeightedChoice;
use Modules\Fixcity\Database\Seeders\TicketCommentSeeder;
use Modules\Fixcity\Database\Seeders\TicketHourSeeder;
use Modules\Fixcity\Database\Seeders\TicketSeeder;
use Modules\Fixcity\Models\Category;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

beforeEach(function (): void {
    if (! Schema::connection('fixcity')->hasTable('tickets') || ! Schema::connection('fixcity')->hasTable('categories')) {
        Assert::markTestSkipped('Fixcity migrations required on connection fixcity');
    }

    app(CategorySeeder::class)->run();
});

it('upserts the category catalogue without touching the legacy "strade" row', function (): void {
    $catalogue = DemoCategoryCatalog::all();

    Assert::assertCount(21, Category::query()->whereIn('id', array_column($catalogue, 'id'))->get());
    Assert::assertTrue(Category::query()->whereKey('strade')->exists());

    $roots = array_filter($catalogue, static fn (array $row): bool => $row['parent_id'] === null);
    Assert::assertCount(12, $roots);
});

it('writes DMO tickets with a unique code, a timeline and an assignment', function (): void {
    $ids = app(TicketSeeder::class)->seedTickets(5, 4242);

    Assert::assertCount(5, $ids);
    Assert::assertSame(5, Ticket::query()->where('code', 'like', 'DMO-%')->count());

    foreach (Ticket::query()->where('code', 'like', 'DMO-%')->get() as $ticket) {
        Assert::assertNotNull($ticket->created_at);
        Assert::assertGreaterThanOrEqual($ticket->created_at, $ticket->updated_at);
        Assert::assertNotNull($ticket->location['city'] ?? null);
        Assert::assertNotNull($ticket->owner_id);
        Assert::assertGreaterThanOrEqual(1, TicketActivity::query()->where('ticket_id', $ticket->getKey())->count());

        $sla = TicketActivity::query()
            ->where('ticket_id', $ticket->getKey())
            ->where('event_type', 'status_change')
            ->first();
        Assert::assertNotNull($sla);
        Assert::assertIsArray($sla->payload);
        Assert::assertIsArray($sla->payload['sla'] ?? null);
        Assert::assertArrayHasKey('resolve_due_at', $sla->payload['sla']);
        Assert::assertArrayHasKey('breached', $sla->payload['sla']);
    }
});

it('is idempotent: a second run updates the same rows instead of adding new ones', function (): void {
    app(TicketSeeder::class)->seedTickets(5, 4242);
    $before = Ticket::query()->where('code', 'like', 'DMO-%')->count();
    $activities = TicketActivity::query()->whereIn('ticket_id', demoTicketIds())->count();

    app(TicketSeeder::class)->seedTickets(5, 4242);

    Assert::assertSame($before, Ticket::query()->where('code', 'like', 'DMO-%')->count());
    Assert::assertSame($activities, TicketActivity::query()->whereIn('ticket_id', demoTicketIds())->count());
});

it('attaches the office to every ticket that left the triage states', function (): void {
    app(TicketSeeder::class)->seedTickets(20, 777);

    foreach (Ticket::query()->where('code', 'like', 'DMO-%')->get() as $ticket) {
        $status = $ticket->status?->value;
        $expected = ! in_array($status, ['draft', 'open', 'pending'], true);

        Assert::assertSame($expected, $ticket->responsible_id !== null, 'Ticket '.$ticket->code.' stato '.$status);
    }
});

it('writes discussion messages and work hours only for DMO tickets', function (): void {
    $ids = app(TicketSeeder::class)->seedTickets(10, 909);
    $comments = app(TicketCommentSeeder::class)->seedDatasetComments($ids);
    $hours = app(TicketHourSeeder::class)->seedDatasetHours($ids);

    Assert::assertGreaterThan(0, $comments);
    Assert::assertGreaterThan(0, $hours);

    $db = DB::connection('fixcity');
    // Il modello Comment vive su una connessione propria: leggere dalla
    // connessione di Fixcity darebbe sempre zero righe.
    $commentDb = (new \Modules\Comment\Models\Comment)->getConnection();
    $touched = $commentDb->table('comments')
        ->where('commentable_type', Ticket::class)
        ->whereIn('commentable_id', $ids)
        ->count();
    Assert::assertSame($comments, $touched);

    foreach ($db->table('ticket_hours')->whereIn('ticket_id', $ids)->get() as $row) {
        Assert::assertContains($row->value, [0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0, 5.5, 6.0, 6.5, 7.0, 7.5]);
    }
});

it('keeps logged hours within the estimate declared on the ticket', function (): void {
    $ids = app(TicketSeeder::class)->seedTickets(15, 5150);
    app(TicketHourSeeder::class)->seedDatasetHours($ids);

    $over = DB::connection('fixcity')->table('ticket_hours as h')
        ->join('tickets as t', 't.id', '=', 'h.ticket_id')
        ->whereIn('h.ticket_id', $ids)
        ->select('h.ticket_id', DB::raw('SUM(h.value) as total'), DB::raw('t.estimation as estimate'))
        ->groupBy('h.ticket_id', 't.estimation')
        ->havingRaw('SUM(h.value) > t.estimation')
        ->get();

    Assert::assertCount(0, $over);
});

it('extracts only non empty strings from a catalogue', function (): void {
    $faker = Factory::create('it_IT');
    $faker->seed(1);

    for ($attempt = 0; $attempt < 50; $attempt++) {
        $picked = DemoText::pick($faker, ['Via Roma', 'Piazza del Duomo']);
        Assert::assertContains($picked, ['Via Roma', 'Piazza del Duomo']);
    }

    Assert::assertSame('via_roma_12', DemoText::slugify('Via Roma 12', 20));
    Assert::assertSame('segnalazione', DemoText::slugify('...', 20));
    Assert::assertSame('pu', DemoText::slugify('può è', 5));
});

it('never returns an index outside the weighted list', function (): void {
    $faker = Factory::create('it_IT');
    $faker->seed(2);

    for ($attempt = 0; $attempt < 50; $attempt++) {
        $index = DemoWeightedChoice::index($faker, [0, 0, 7]);

        Assert::assertSame(2, $index);
    }
});

/**
 * @return list<int|string>
 */
function demoTicketIds(): array
{
    $ids = Ticket::query()->where('code', 'like', 'DMO-%')->pluck('id')->all();

    return array_values(array_map(
        static fn (mixed $id): int|string => is_int($id) ? $id : SafeStringCastAction::cast($id),
        $ids,
    ));
}
