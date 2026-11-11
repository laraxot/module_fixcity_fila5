<?php

declare(strict_types=1);

use function Pest\Laravel\getJson;
use function Safe\preg_replace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Actions\BuildTicketPublicImageUrlsAction;
use Modules\Fixcity\Actions\ResolveTicketMediaDiskAction;
use Modules\Fixcity\Actions\SignTicketMediaUrlAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\Media\Models\Media;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * SEC-04 / DB-19 / DB-13: allegati dei ticket nelle API pubbliche e sul disco privato.
 * Story: Modules/Fixcity/docs/stories/public-api-media-and-geojson.story.md
 */

/**
 * @param  array<string, mixed>  $attributes
 */
function mediaVisibilityTicket(array $attributes = []): Ticket
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => 'Buca con foto',
        'content' => 'Buca profonda.',
        'status' => TicketStatusEnum::IN_PROGRESS,
        'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        'location' => ['lat' => 45.557, 'lng' => 12.236, 'address' => 'Via Piovan'],
        'latitude' => 45.557,
        'longitude' => 12.236,
    ], $attributes));
}

function mediaVisibilityPhoto(Ticket $ticket): Media
{
    $media = $ticket->addMedia(UploadedFile::fake()->image('camera.jpg'))->toMediaCollection('attachments');
    Assert::assertInstanceOf(Media::class, $media);

    return $media;
}

function mediaVisibilityPdf(Ticket $ticket): Media
{
    $media = $ticket->addMedia(UploadedFile::fake()->createWithContent('documento.pdf', "%PDF-1.4\n"))->toMediaCollection('attachments');
    Assert::assertInstanceOf(Media::class, $media);

    return $media;
}

function mediaVisibilityUrl(Media $media): string
{
    return ResolveTicketMediaDiskAction::URL_PREFIX.'/'.ltrim($media->getPathRelativeToRoot(), '/');
}

it('stores new ticket attachments on the private disk', function (): void {
    $media = mediaVisibilityPhoto(mediaVisibilityTicket(['status' => TicketStatusEnum::PENDING]));

    Assert::assertSame(ResolveTicketMediaDiskAction::DEFAULT_DISK, $media->disk);
    Assert::assertSame(ResolveTicketMediaDiskAction::DEFAULT_DISK, $media->conversions_disk);
});

it('never returns PDFs from /api/tickets/list and /api/ticket-details', function (): void {
    /** @var TestCase $this */
    $ticket = mediaVisibilityTicket();
    mediaVisibilityPhoto($ticket);
    mediaVisibilityPdf($ticket);

    $list = $this->getJson('/api/tickets/list?limit=50')->assertOk()->json('tickets');
    Assert::assertIsArray($list);
    $row = collect($list)->firstWhere('id', $ticket->getKey());
    Assert::assertIsArray($row);
    $images = $row['images'] ?? null;
    Assert::assertIsArray($images);
    Assert::assertCount(1, $images);
    Assert::assertStringEndsWith('camera.jpg', SafeStringCastAction::cast($images[0] ?? null));

    $details = $this->getJson('/api/ticket-details/'.SafeStringCastAction::cast($ticket->getKey()))->assertOk()->json('images');
    Assert::assertIsArray($details);
    Assert::assertCount(1, $details);
    Assert::assertStringNotContainsString('.pdf', implode(' ', array_map(SafeStringCastAction::cast(...), $details)));
});

it('gives no image URL of a non-public ticket to a stranger', function (): void {
    $ticket = mediaVisibilityTicket(['status' => TicketStatusEnum::PENDING]);
    mediaVisibilityPhoto($ticket);

    Assert::assertSame([], app(BuildTicketPublicImageUrlsAction::class)->execute($ticket, null));
    Assert::assertSame([], app(BuildTicketPublicImageUrlsAction::class)->execute($ticket, UserFactory::new()->createOne()));
});

it('serves the photo of a public ticket to a guest', function (): void {
    /** @var TestCase $this */
    $media = mediaVisibilityPhoto(mediaVisibilityTicket());

    $response = $this->get(mediaVisibilityUrl($media))->assertOk();

    Assert::assertStringContainsString('private', SafeStringCastAction::cast($response->headers->get('Cache-Control')));
    Assert::assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
});

it('hides the photo of a pending ticket from guests and other users, not from the owner', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $media = mediaVisibilityPhoto(mediaVisibilityTicket(['status' => TicketStatusEnum::PENDING, 'owner_id' => $owner->getKey()]));
    $url = mediaVisibilityUrl($media);

    $this->get($url)->assertNotFound();
    $this->actingAs(UserFactory::new()->createOne())->get($url)->assertNotFound();
    $this->actingAs($owner)->get($url)->assertOk();
});

it('never serves a PDF of a public ticket to a guest, only to its owner', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $media = mediaVisibilityPdf(mediaVisibilityTicket(['owner_id' => $owner->getKey()]));
    $url = mediaVisibilityUrl($media);

    $this->get($url)->assertNotFound();
    $response = $this->actingAs($owner)->get($url)->assertOk();
    Assert::assertStringStartsWith('attachment', SafeStringCastAction::cast($response->headers->get('Content-Disposition')));
});

it('accepts a valid signature for a pending photo and rejects a forged one', function (): void {
    /** @var TestCase $this */
    $media = mediaVisibilityPhoto(mediaVisibilityTicket(['status' => TicketStatusEnum::PENDING]));
    $signed = app(SignTicketMediaUrlAction::class)->execute(mediaVisibilityUrl($media));

    $this->get($signed)->assertOk();
    $this->get(preg_replace('/signature=[0-9a-f]+/', 'signature='.str_repeat('0', 64), $signed))->assertNotFound();
});

it('rejects paths outside the media folder', function (): void {
    /** @var TestCase $this */
    $media = mediaVisibilityPhoto(mediaVisibilityTicket());
    $id = SafeStringCastAction::cast($media->getKey());

    $this->get(ResolveTicketMediaDiskAction::URL_PREFIX.'/'.$id.'/altro.jpg')->assertNotFound();
    $this->get(ResolveTicketMediaDiskAction::URL_PREFIX.'/'.$id.'/../'.$id.'/camera.jpg')->assertNotFound();
});

it('loads the media of /api/tickets/list with a constant number of queries', function (): void {
    /** @var TestCase $this */
    $count = static function (int $limit): int {
        DB::connection('fixcity')->flushQueryLog();
        DB::connection('media')->flushQueryLog();
        DB::connection('fixcity')->enableQueryLog();
        DB::connection('media')->enableQueryLog();
        getJson('/api/tickets/list?limit='.$limit)->assertOk();

        return count(DB::connection('fixcity')->getQueryLog()) + count(DB::connection('media')->getQueryLog());
    };
    foreach (range(1, 6) as $_) {
        mediaVisibilityPhoto(mediaVisibilityTicket());
    }

    Assert::assertSame($count(2), $count(6));
});
