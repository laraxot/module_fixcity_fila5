<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Pagina /{lang}/tickets/track: il cittadino rivede le proprie foto (miniature accessibili, link all'immagine intera) e,
 * se la segnalazione e' risolta o chiusa, trova la richiesta di valutazione (TicketCitizenRatingPromptWidget).
 */

/**
 * @param  array<string, mixed>  $attributes
 */
function trackedTicket(array $attributes = []): Ticket
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => 'Buca in via Solferino',
        'content' => 'Una buca profonda davanti al civico.',
        'status' => TicketStatusEnum::IN_PROGRESS,
        'code' => 'TCK-TRACKPHOTO0001',
    ], $attributes));
}

/**
 * @return list<array{href: string, src: string, alt: string}>
 */
function trackPhotos(string $html): array
{
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8"?>'.$html);
    $photos = [];
    foreach ((new DOMXPath($dom))->query('//*[@data-element="ticket-track-photos"]//a[img]') ?: [] as $link) {
        if (! $link instanceof DOMElement) {
            continue;
        }
        $img = $link->getElementsByTagName('img')->item(0);
        $photos[] = [
            'href' => $link->getAttribute('href'),
            'src' => $img instanceof DOMElement ? $img->getAttribute('src') : '',
            'alt' => $img instanceof DOMElement ? $img->getAttribute('alt') : '',
        ];
    }

    return $photos;
}

it('shows the citizen photos as linked thumbnails with alt text', function (): void {
    /** @var TestCase $this */
    Storage::fake('public');
    $ticket = trackedTicket();
    $ticket->addMedia(UploadedFile::fake()->image('buca.jpg'))->toMediaCollection('attachments');
    $ticket->addMedia(UploadedFile::fake()->image('buca-vicino.jpg'))->toMediaCollection('attachments');

    $html = $this->get('/it/tickets/track?code=TCK-TRACKPHOTO0001')->assertOk()->getContent();
    Assert::assertIsString($html);

    $photos = trackPhotos($html);
    Assert::assertCount(2, $photos);
    foreach ($photos as $index => $photo) {
        Assert::assertNotSame('', $photo['href']);
        Assert::assertSame($photo['href'], $photo['src']);
        Assert::assertSame(__('fixcity::ticket.track.photo_alt', [
            'number' => $index + 1,
            'title' => 'Buca in via Solferino',
        ]), $photo['alt']);
    }
    Assert::assertStringContainsString(__('fixcity::ticket.track.photos_title'), $html);
});

it('renders no photo section when the ticket has no images', function (): void {
    /** @var TestCase $this */
    trackedTicket();

    $html = $this->get('/it/tickets/track?code=TCK-TRACKPHOTO0001')->assertOk()->getContent();
    Assert::assertIsString($html);

    Assert::assertStringNotContainsString('data-element="ticket-track-photos"', $html);
});

it('asks the owner to rate a resolved ticket on the track page', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $ticket = trackedTicket(['status' => TicketStatusEnum::RESOLVED, 'owner_id' => $owner->id]);

    $html = $this->actingAs($owner)->get('/it/tickets/track?ticket_id='.$ticket->id)->assertOk()->getContent();
    Assert::assertIsString($html);

    Assert::assertStringContainsString('id="ticket-rating-prompt-title"', $html);
});

it('does not ask for a rating while the ticket is still open or to a guest', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $open = trackedTicket(['owner_id' => $owner->id]);
    trackedTicket(['status' => TicketStatusEnum::RESOLVED, 'owner_id' => $owner->id, 'code' => 'TCK-TRACKRESOLVED01']);

    $guestHtml = $this->get('/it/tickets/track?code=TCK-TRACKRESOLVED01')->assertOk()->getContent();
    $openHtml = $this->actingAs($owner)->get('/it/tickets/track?ticket_id='.$open->id)->assertOk()->getContent();

    Assert::assertIsString($guestHtml);
    Assert::assertIsString($openHtml);
    Assert::assertStringNotContainsString('ticket-rating-prompt-title', $guestHtml);
    Assert::assertStringNotContainsString('ticket-rating-prompt-title', $openHtml);
});
