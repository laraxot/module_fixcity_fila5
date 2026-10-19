<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Modules\Fixcity\Actions\BuildPersonalAreaFollowedDataAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Safe\json_encode;

uses(TestCase::class);

/*
 * DCP-110: dati della pagina "Segnalazioni che seguo". Il tracciamento e' SEMPRE per codice (`?code=`), mai per id del database;
 * senza codice il link non esiste (la vista dice onestamente che il codice non e' disponibile).
 */
it('lists the followed tickets newest first with the tracking link built from the code', function (): void {
    $citizen = UserFactory::new()->createOne();
    $older = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN, 'code' => 'TCK-FOL-0001', 'created_at' => Carbon::parse('2026-01-10')]);
    $newer = TicketFactory::new()->createOne(['status' => TicketStatusEnum::IN_PROGRESS, 'code' => 'TCK-FOL-0002', 'created_at' => Carbon::parse('2026-02-10')]);
    TicketSubscriber::query()->create(['ticket_id' => $older->getKey(), 'user_id' => $citizen->getKey()]);
    TicketSubscriber::query()->create(['ticket_id' => $newer->getKey(), 'user_id' => $citizen->getKey()]);

    $data = app(BuildPersonalAreaFollowedDataAction::class)->execute($citizen, 'it');

    Assert::assertSame(2, $data['total']);
    Assert::assertFalse($data['truncated']);
    Assert::assertSame([$newer->getKey(), $older->getKey()], array_column($data['rows'], 'id'));
    Assert::assertStringEndsWith('/it/tickets/track?code=TCK-FOL-0002', (string) $data['rows'][0]['track_query_url']);
    Assert::assertStringNotContainsString('ticket_id', json_encode($data));
});

it('does not list tickets followed by other citizens nor private tickets of other owners', function (): void {
    $citizen = UserFactory::new()->createOne();
    $other = UserFactory::new()->createOne();
    $followed = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN, 'code' => 'TCK-FOL-MINE']);
    $notFollowed = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN, 'code' => 'TCK-FOL-NOT']);
    $privateOfOthers = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING, 'code' => 'TCK-FOL-PRIV']);
    TicketSubscriber::query()->create(['ticket_id' => $followed->getKey(), 'user_id' => $citizen->getKey()]);
    TicketSubscriber::query()->create(['ticket_id' => $notFollowed->getKey(), 'user_id' => $other->getKey()]);
    TicketSubscriber::query()->create(['ticket_id' => $privateOfOthers->getKey(), 'user_id' => $citizen->getKey()]);

    $data = app(BuildPersonalAreaFollowedDataAction::class)->execute($citizen, 'it');

    Assert::assertSame([$followed->getKey()], array_column($data['rows'], 'id'));
    $json = json_encode($data);
    Assert::assertStringNotContainsString('TCK-FOL-NOT', $json);
    Assert::assertStringNotContainsString('TCK-FOL-PRIV', $json);
});

it('gives no tracking link to a followed ticket without code', function (): void {
    $citizen = UserFactory::new()->createOne();
    $legacy = TicketFactory::new()->createOne(['status' => TicketStatusEnum::IN_PROGRESS, 'code' => null]);
    TicketSubscriber::query()->create(['ticket_id' => $legacy->getKey(), 'user_id' => $citizen->getKey()]);

    $row = app(BuildPersonalAreaFollowedDataAction::class)->execute($citizen, 'it')['rows'][0];

    Assert::assertNull($row['code']);
    Assert::assertNull($row['track_query_url']);
});

it('returns an empty list for a citizen who follows nothing', function (): void {
    $citizen = UserFactory::new()->createOne();

    $data = app(BuildPersonalAreaFollowedDataAction::class)->execute($citizen, 'en');

    Assert::assertSame(['total' => 0, 'truncated' => false, 'rows' => []], $data);
});

it('keeps the real total and flags the list as truncated when it exceeds the limit', function (): void {
    $citizen = UserFactory::new()->createOne();
    foreach ([1, 2, 3] as $day) {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN, 'created_at' => Carbon::parse('2026-02-0'.$day)]);
        TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $citizen->getKey()]);
    }

    $data = app(BuildPersonalAreaFollowedDataAction::class)->execute($citizen, 'it', 2);

    Assert::assertSame(3, $data['total']);
    Assert::assertTrue($data['truncated']);
    Assert::assertCount(2, $data['rows']);
});
