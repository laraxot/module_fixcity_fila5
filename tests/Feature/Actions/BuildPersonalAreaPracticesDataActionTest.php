<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Modules\Fixcity\Actions\BuildPersonalAreaPracticesDataAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Safe\json_encode;

uses(TestCase::class);

/*
 * DCP-110: dati della pagina "Le mie segnalazioni". Solo le segnalazioni di cui l'utente e' PROPRIETARIO (owner_id),
 * divise per stato reale del workflow (TicketStatusEnum): concluse = risolta o chiusa, tutto il resto e' in corso.
 */
it('splits the citizen own tickets into in progress and concluded using the real workflow states', function (): void {
    $citizen = UserFactory::new()->createOne();
    $inProgress = [
        TicketStatusEnum::DRAFT,
        TicketStatusEnum::PENDING,
        TicketStatusEnum::OPEN,
        TicketStatusEnum::IN_REVIEW,
        TicketStatusEnum::IN_PROGRESS,
        TicketStatusEnum::ON_HOLD,
        TicketStatusEnum::REOPENED,
    ];
    $concluded = [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED];

    foreach ([...$inProgress, ...$concluded] as $status) {
        TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'status' => $status]);
    }

    $data = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, 'it');

    Assert::assertSame(9, $data['total']);
    Assert::assertFalse($data['truncated']);
    Assert::assertEqualsCanonicalizing(
        array_map(static fn (TicketStatusEnum $status): string => $status->value, $inProgress),
        array_column($data['in_progress'], 'status'),
    );
    Assert::assertEqualsCanonicalizing(
        array_map(static fn (TicketStatusEnum $status): string => $status->value, $concluded),
        array_column($data['concluded'], 'status'),
    );
    Assert::assertSame([false], array_values(array_unique(array_column($data['in_progress'], 'is_concluded'))));
    Assert::assertSame([true], array_values(array_unique(array_column($data['concluded'], 'is_concluded'))));
});

it('exposes title, code, status label, date and links of each ticket', function (): void {
    $citizen = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'name' => 'Buca in via Roma',
        'owner_id' => $citizen->getKey(),
        'status' => TicketStatusEnum::IN_PROGRESS,
        'code' => 'TCK-ROW-0001',
        'created_at' => Carbon::parse('2026-03-05 10:00:00'),
    ]);

    $row = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, 'it')['in_progress'][0];

    Assert::assertSame($ticket->id, $row['id']);
    Assert::assertSame('Buca in via Roma', $row['title']);
    Assert::assertSame('TCK-ROW-0001', $row['code']);
    Assert::assertSame('in_progress', $row['status']);
    Assert::assertSame('In lavorazione', $row['status_label']);
    Assert::assertSame('5 marzo 2026', $row['date']);
    Assert::assertSame('2026-03-05', $row['date_iso']);
    Assert::assertStringEndsWith('/it/tickets/'.$ticket->id, $row['detail_url']);
    Assert::assertStringEndsWith('/it/tickets/track/TCK-ROW-0001', (string) $row['track_url']);
});

it('localizes status label, date and links for every served language', function (string $locale, string $label, string $date): void {
    $citizen = UserFactory::new()->createOne();
    TicketFactory::new()->createOne([
        'owner_id' => $citizen->getKey(),
        'status' => TicketStatusEnum::RESOLVED,
        'code' => 'TCK-LOC-0001',
        'created_at' => Carbon::parse('2026-03-05 10:00:00'),
    ]);

    $row = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, $locale)['concluded'][0];

    Assert::assertSame($label, $row['status_label']);
    Assert::assertSame($date, $row['date']);
    Assert::assertStringContainsString('/'.$locale.'/tickets/', $row['detail_url']);
    Assert::assertStringContainsString('/'.$locale.'/tickets/track/TCK-LOC-0001', (string) $row['track_url']);
})->with([
    'it' => ['it', 'Risolta', '5 marzo 2026'],
    'en' => ['en', 'Resolved', '5 March 2026'],
    'de' => ['de', 'Gelöst', '5. März 2026'],
    'es' => ['es', 'Resuelta', '5 de marzo de 2026'],
]);

it('orders each group newest first', function (): void {
    $citizen = UserFactory::new()->createOne();
    $oldest = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'status' => TicketStatusEnum::PENDING, 'created_at' => Carbon::parse('2026-01-01')]);
    $newest = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'status' => TicketStatusEnum::PENDING, 'created_at' => Carbon::parse('2026-03-01')]);
    $middle = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'status' => TicketStatusEnum::PENDING, 'created_at' => Carbon::parse('2026-02-01')]);

    $data = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, 'it');

    Assert::assertSame(
        [$newest->getKey(), $middle->getKey(), $oldest->getKey()],
        array_column($data['in_progress'], 'id'),
    );
});

it('never includes tickets owned or followed by someone else, nor their codes', function (): void {
    $citizen = UserFactory::new()->createOne();
    $other = UserFactory::new()->createOne();
    $mine = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'code' => 'TCK-MINE-0001']);
    $theirs = TicketFactory::new()->createOne(['owner_id' => $other->getKey(), 'code' => 'TCK-THEIRS-0001']);
    TicketSubscriber::query()->create(['ticket_id' => $theirs->getKey(), 'user_id' => $citizen->getKey()]);

    $data = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, 'it');

    Assert::assertSame([$mine->getKey()], array_column($data['in_progress'], 'id'));
    Assert::assertStringNotContainsString('TCK-THEIRS-0001', json_encode($data));
});

it('returns empty groups for a citizen without tickets', function (): void {
    $citizen = UserFactory::new()->createOne();

    $data = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, 'it');

    Assert::assertSame(0, $data['total']);
    Assert::assertSame([], $data['in_progress']);
    Assert::assertSame([], $data['concluded']);
    Assert::assertFalse($data['truncated']);
});

it('omits the tracking links when a legacy ticket has no code', function (): void {
    $citizen = UserFactory::new()->createOne();
    TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'code' => null]);

    $row = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, 'it')['in_progress'][0];

    Assert::assertNull($row['code']);
    Assert::assertNull($row['track_url']);
    Assert::assertNull($row['track_query_url']);
});

it('keeps the real total and flags the list as truncated when it exceeds the limit', function (): void {
    $citizen = UserFactory::new()->createOne();
    foreach ([1, 2, 3] as $day) {
        TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'created_at' => Carbon::parse('2026-02-0'.$day)]);
    }

    $data = app(BuildPersonalAreaPracticesDataAction::class)->execute($citizen, 'it', 2);

    Assert::assertSame(3, $data['total']);
    Assert::assertTrue($data['truncated']);
    Assert::assertCount(2, $data['in_progress']);
});
