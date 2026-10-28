<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Fixcity\Actions\BuildPublicTicketDetailAction;
use Modules\Fixcity\Actions\BuildTicketTimelineAction;
use Modules\Fixcity\Database\Factories\TicketActivityFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Safe\json_encode;

uses(TestCase::class);

/**
 * DCP-148: l'insieme di campi che il dettaglio pubblico espone e' definito da una sola Action. Il test dell'insieme esatto di chiavi
 * e' la guardia contro le fughe: aggiungere un campo (code, owner_id, email, immagini) deve rompere questo test, non passare inosservato.
 *
 * @param  array<string, mixed>  $attributes
 */
function detailActionTicket(array $attributes = []): Ticket
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => 'Cestino rotto',
        'content' => "Il cestino è staccato.\nNon chiude più.",
        'status' => TicketStatusEnum::RESOLVED,
        'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        'created_at' => '2026-03-14 15:42:10',
        'code' => 'TCK-SEGRETO-0001',
        'location' => ['lat' => 43.7696, 'lng' => 11.2558, 'address' => 'Piazza Dalmazia, Firenze'],
    ], $attributes));
}

test('exposes exactly the public fields and nothing else', function (): void {
    /** @var TestCase $this */
    $ticket = detailActionTicket();

    $detail = app(BuildPublicTicketDetailAction::class)->execute($ticket->id);

    Assert::assertIsArray($detail);
    $keys = array_keys($detail);
    sort($keys);
    Assert::assertSame(
        ['address', 'created_date', 'description', 'id', 'images', 'is_following', 'is_owner', 'is_public', 'latitude', 'longitude', 'meta_description', 'status', 'status_label', 'timeline', 'title', 'tracking_code', 'type', 'type_label'],
        $keys,
    );
    Assert::assertStringNotContainsString('TCK-SEGRETO-0001', (string) json_encode($detail));
    Assert::assertNull($detail['tracking_code']);
    Assert::assertFalse($detail['is_owner']);
    Assert::assertTrue($detail['is_public']);
    Assert::assertSame('Cestino rotto', $detail['title']);
    Assert::assertSame('resolved', $detail['status']);
    Assert::assertSame('2026-03-14', $detail['created_date']);
    Assert::assertSame('Piazza Dalmazia, Firenze', $detail['address']);
    Assert::assertSame(43.7696, $detail['latitude']);
    Assert::assertSame(11.2558, $detail['longitude']);
    Assert::assertSame("Il cestino è staccato.\nNon chiude più.", $detail['description']);
    Assert::assertSame('Il cestino è staccato. Non chiude più.', $detail['meta_description']);
});

test('exposes image URLs and excludes non-image attachments', function (): void {
    /** @var TestCase $this */
    Storage::fake('public');
    $ticket = detailActionTicket();
    $ticket->addMedia(UploadedFile::fake()->image('camera.jpg'))->toMediaCollection('attachments');
    $ticket->addMedia(UploadedFile::fake()->createWithContent('document.pdf', "%PDF-1.4\n"))->toMediaCollection('attachments');

    $detail = app(BuildPublicTicketDetailAction::class)->execute($ticket->id);

    Assert::assertIsArray($detail);
    Assert::assertCount(1, $detail['images']);
    Assert::assertStringContainsString('/storage/', $detail['images'][0]);
});

test('returns null when the report is not visible to the public or does not exist', function (): void {
    /** @var TestCase $this */
    $action = app(BuildPublicTicketDetailAction::class);

    Assert::assertNull($action->execute(detailActionTicket(['status' => TicketStatusEnum::PENDING])->id));
    Assert::assertNull($action->execute(detailActionTicket(['status' => TicketStatusEnum::DRAFT])->id));
    Assert::assertNull($action->execute(999999));
});

test('lets the owner open the own report even when it is not public yet', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $ticket = detailActionTicket(['status' => TicketStatusEnum::PENDING, 'owner_id' => $owner->getKey()]);

    $this->actingAs($owner);

    Assert::assertIsArray(app(BuildPublicTicketDetailAction::class)->execute($ticket->id));
});

test('lets the owner open the own report even without a position, and tells that it is not public', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $ticket = detailActionTicket(['status' => TicketStatusEnum::PENDING, 'owner_id' => $owner->getKey(), 'location' => null, 'latitude' => null, 'longitude' => null]);
    $action = app(BuildPublicTicketDetailAction::class);

    Assert::assertNull($action->execute($ticket->id));
    Assert::assertNull($action->execute($ticket->id, UserFactory::new()->createOne()));

    $detail = $action->execute($ticket->id, $owner);

    Assert::assertIsArray($detail);
    Assert::assertTrue($detail['is_owner']);
    Assert::assertFalse($detail['is_public']);
    Assert::assertSame([], $detail['images']);
    Assert::assertSame('TCK-SEGRETO-0001', $detail['tracking_code']);
    Assert::assertSame('', $detail['address']);
    Assert::assertNull($detail['latitude']);
});

test('gives the tracking code to the owner only, never to a follower or another citizen', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $follower = UserFactory::new()->createOne();
    $ticket = detailActionTicket(['owner_id' => $owner->getKey()]);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->id, 'user_id' => $follower->getKey()]);
    $action = app(BuildPublicTicketDetailAction::class);

    $forOwner = $action->execute($ticket->id, $owner);
    $forFollower = $action->execute($ticket->id, $follower);
    $forGuest = $action->execute($ticket->id);

    Assert::assertIsArray($forOwner);
    Assert::assertIsArray($forFollower);
    Assert::assertIsArray($forGuest);
    Assert::assertSame('TCK-SEGRETO-0001', $forOwner['tracking_code']);
    Assert::assertTrue($forOwner['is_owner']);
    Assert::assertNull($forFollower['tracking_code']);
    Assert::assertFalse($forFollower['is_owner']);
    Assert::assertTrue($forFollower['is_following']);
    Assert::assertNull($forGuest['tracking_code']);
    Assert::assertStringNotContainsString('TCK-SEGRETO-0001', (string) json_encode([$forFollower, $forGuest]));
});

test('gives no tracking code when the owner report has none', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $ticket = detailActionTicket(['owner_id' => $owner->getKey(), 'code' => null]);

    $detail = app(BuildPublicTicketDetailAction::class)->execute($ticket->id, $owner);

    Assert::assertIsArray($detail);
    Assert::assertTrue($detail['is_owner']);
    Assert::assertNull($detail['tracking_code']);
});

test('tells whether the viewer follows the report', function (): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $ticket = detailActionTicket();
    $isFollowing = static function (int $ticketId, ?Authenticatable $viewer = null): bool {
        $detail = app(BuildPublicTicketDetailAction::class)->execute($ticketId, $viewer);
        Assert::assertIsArray($detail);

        return $detail['is_following'];
    };

    Assert::assertFalse($isFollowing($ticket->id));
    Assert::assertFalse($isFollowing($ticket->id, $citizen));

    TicketSubscriber::query()->create(['ticket_id' => $ticket->id, 'user_id' => $citizen->getKey()]);

    Assert::assertTrue($isFollowing($ticket->id, $citizen));
    Assert::assertFalse($isFollowing($ticket->id));
});

test('limits the meta description to 160 characters', function (): void {
    /** @var TestCase $this */
    $ticket = detailActionTicket(['content' => str_repeat('parola ', 100)]);

    $detail = app(BuildPublicTicketDetailAction::class)->execute($ticket->id);

    Assert::assertIsArray($detail);
    Assert::assertLessThanOrEqual(160, mb_strlen($detail['meta_description']));
    Assert::assertStringEndsWith('...', $detail['meta_description']);
});

test('has no map point when the coordinates are missing or zero', function (): void {
    /** @var TestCase $this */
    $ticket = detailActionTicket(['location' => ['lat' => 0, 'lng' => 0, 'address' => 'Indirizzo senza punto']]);

    $detail = app(BuildPublicTicketDetailAction::class)->execute($ticket->id);

    Assert::assertIsArray($detail);
    Assert::assertNull($detail['latitude']);
    Assert::assertNull($detail['longitude']);
    Assert::assertSame('Indirizzo senza punto', $detail['address']);
});

test('adds the day-only date to the timeline without changing the existing keys', function (): void {
    /** @var TestCase $this */
    $ticket = detailActionTicket();
    TicketActivityFactory::new()->createOne([
        'ticket_id' => $ticket->id,
        'event_type' => 'status_change',
        'payload' => ['from' => 'pending', 'to' => 'in_review'],
        'visibility' => 'public',
        'reason' => 'Presa in carico',
        'created_at' => '2026-03-15 09:30:00',
    ]);

    $timeline = app(BuildTicketTimelineAction::class)->execute($ticket);

    Assert::assertCount(1, $timeline);
    Assert::assertSame(['occurred_at', 'occurred_date', 'occurred_on', 'from_label', 'to_label', 'reason'], array_keys($timeline[0]));
    Assert::assertSame('2026-03-15', $timeline[0]['occurred_on']);
    Assert::assertStringContainsString('09:30', $timeline[0]['occurred_at']);
    Assert::assertStringNotContainsString('09:30', $timeline[0]['occurred_date']);
    Assert::assertStringContainsString('15', $timeline[0]['occurred_date']);
});

test('reads an added attachment as an event, without the status and without the file name', function (): void {
    /** @var TestCase $this */
    $ticket = detailActionTicket();
    TicketActivityFactory::new()->createOne([
        'ticket_id' => $ticket->id,
        'event_type' => 'media_added',
        'payload' => ['from' => 'resolved', 'to' => 'resolved', 'file_name' => 'grcpCsCaeLkrP6SFPChfDkrqeQaXYZAoVsSG2siH.jpg'],
        'visibility' => 'public',
        'created_at' => '2026-03-15 09:30:00',
    ]);

    $timeline = app(BuildTicketTimelineAction::class)->execute($ticket);

    Assert::assertCount(1, $timeline);
    Assert::assertSame(__('fixcity::ticket_detail.timeline.media_added'), $timeline[0]['to_label']);
    Assert::assertNull($timeline[0]['from_label']);
    Assert::assertNull($timeline[0]['reason']);
    Assert::assertStringNotContainsString('grcpCs', (string) json_encode($timeline));
});

/*
 * DCP-379: su una segnalazione non ancora pubblica (es. `pending`, il caso del ticket 2) le foto le vede il proprietario e nessun altro.
 */
test('gives the photos of a report not yet public to the owner only', function (): void {
    /** @var TestCase $this */
    Storage::fake('public');
    $owner = UserFactory::new()->createOne();
    $ticket = detailActionTicket(['status' => TicketStatusEnum::PENDING, 'owner_id' => $owner->getKey()]);
    $ticket->addMedia(UploadedFile::fake()->image('camera.jpg'))->toMediaCollection('attachments');
    $action = app(BuildPublicTicketDetailAction::class);

    $forOwner = $action->execute($ticket->id, $owner);

    Assert::assertIsArray($forOwner);
    Assert::assertCount(1, $forOwner['images']);
    // Percorso relativo: con `APP_URL` sbagliato (in produzione era `http://fixcity.local`) un URL assoluto rompe la foto.
    Assert::assertStringStartsWith('/storage/', $forOwner['images'][0]);
    Assert::assertNull($action->execute($ticket->id, UserFactory::new()->createOne()));
    Assert::assertNull($action->execute($ticket->id));
});
