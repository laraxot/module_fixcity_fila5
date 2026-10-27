<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use PHPUnit\Framework\Assert;

use function Safe\preg_match;

uses(TestCase::class);

/*
 * DCP-287 (FO-6-43, FO-2-36, FO-2-17, FO-2-18): chi puo' aprire /{lang}/tickets/{id} e che cosa vede.
 *
 * Regola: ospite e altri utenti vedono solo le segnalazioni pubbliche (stato visibile e posizione, come mappa ed elenco);
 * il proprietario vede la propria in qualunque stato e anche senza posizione; seguire una segnalazione non da' accesso a
 * quelle non pubbliche; il codice di tracciamento (capability) lo vede solo il proprietario.
 */
function ownerAccessUser(): User
{
    $user = UserFactory::new()->createOne();
    Assert::assertInstanceOf(User::class, $user);

    return $user;
}

/**
 * @param  array<string, mixed>  $attributes
 */
function ownerAccessTicket(User $owner, array $attributes = []): Ticket
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => 'Buca davanti alla scuola',
        'content' => 'Una buca profonda sul marciapiede.',
        'status' => TicketStatusEnum::OPEN,
        'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        'owner_id' => $owner->getKey(),
        'code' => 'TCK-OWNER-CODE-0001',
        'location' => ['lat' => 43.7696, 'lng' => 11.2558, 'address' => 'Via Solferino - 50100 Firenze'],
    ], $attributes));
}

/**
 * @return array{viewer: User|null, expected: list<int>}
 */
function ownerAccessViewer(string $role, User $owner, User $other, User $follower): array
{
    return match ($role) {
        'guest' => ['viewer' => null, 'expected' => [200, 404, 404, 404]],
        'other citizen' => ['viewer' => $other, 'expected' => [200, 404, 404, 404]],
        'follower' => ['viewer' => $follower, 'expected' => [200, 404, 404, 404]],
        default => ['viewer' => $owner, 'expected' => [200, 200, 200, 200]],
    };
}

function ownerAccessDocument(string $html): HTMLDocument
{
    return HTMLDocument::createFromString($html, LIBXML_NOERROR);
}

it('opens the detail only to who may see it, for every combination of status and position', function (string $role): void {
    /** @var TestCase $this */
    $owner = ownerAccessUser();
    $other = ownerAccessUser();
    $follower = ownerAccessUser();
    $tickets = [
        ownerAccessTicket($owner),
        ownerAccessTicket($owner, ['location' => null]),
        ownerAccessTicket($owner, ['status' => TicketStatusEnum::PENDING]),
        ownerAccessTicket($owner, ['status' => TicketStatusEnum::PENDING, 'location' => null]),
    ];
    foreach ($tickets as $ticket) {
        TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $follower->getKey()]);
    }
    ['viewer' => $viewer, 'expected' => $expected] = ownerAccessViewer($role, $owner, $other, $follower);

    if ($viewer instanceof User) {
        $this->actingAs($viewer);
    }
    $actual = array_map(fn (Ticket $ticket): int => $this->get('/it/tickets/'.$ticket->id)->getStatusCode(), $tickets);

    // ordine: pubblica con posizione, pubblica senza posizione, non pubblica con posizione, non pubblica senza posizione
    Assert::assertSame($expected, $actual, $role);
})->with(['guest', 'other citizen', 'follower', 'owner']);

it('shows the owner a non public report without position, with its title, status and a notice that it is not public yet', function (): void {
    /** @var TestCase $this */
    $owner = ownerAccessUser();
    $ticket = ownerAccessTicket($owner, ['location' => null, 'status' => TicketStatusEnum::PENDING, 'name' => 'Buca in via Roma']);

    $this->actingAs($owner)->get('/it/tickets/'.$ticket->id)
        ->assertOk()
        ->assertSee('Buca in via Roma')
        ->assertSee('Indirizzo non indicato')
        ->assertSee('Per ora la vedi solo tu')
        ->assertDontSee('fixcity::ticket_detail.');
});

it('does not announce a public report as private to its owner', function (): void {
    /** @var TestCase $this */
    $owner = ownerAccessUser();
    $ticket = ownerAccessTicket($owner);

    $this->actingAs($owner)->get('/it/tickets/'.$ticket->id)
        ->assertOk()
        ->assertSee('La tua segnalazione')
        ->assertDontSee('Per ora la vedi solo tu');
});

it('shows the tracking code only to the owner and never to guests, other citizens or followers', function (string $role): void {
    /** @var TestCase $this */
    $owner = ownerAccessUser();
    $other = ownerAccessUser();
    $follower = ownerAccessUser();
    $ticket = ownerAccessTicket($owner);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $follower->getKey()]);
    ['viewer' => $viewer] = ownerAccessViewer($role, $owner, $other, $follower);

    if ($viewer instanceof User) {
        $this->actingAs($viewer);
    }
    $html = (string) $this->get('/it/tickets/'.$ticket->id)->assertOk()->getContent();

    if ($role === 'owner') {
        Assert::assertSame(1, substr_count($html, 'TCK-OWNER-CODE-0001'), 'il proprietario vede il proprio codice una volta');

        return;
    }
    Assert::assertStringNotContainsString('TCK-OWNER-CODE-0001', $html, $role);
    Assert::assertSame(0, preg_match('#tickets/track[?/]#', $html), $role.': nessun link di tracciamento con il codice');
    Assert::assertStringNotContainsString('La tua segnalazione', $html, $role);
})->with(['guest', 'other citizen', 'follower', 'owner']);

it('does not offer the owner to follow the own report, while others still can', function (): void {
    /** @var TestCase $this */
    $owner = ownerAccessUser();
    $other = ownerAccessUser();
    $ticket = ownerAccessTicket($owner);
    $followForms = static fn (string $html): int => ownerAccessDocument($html)->querySelectorAll('form[action$="/follow"]')->length;

    $ownerHtml = (string) $this->actingAs($owner)->get('/it/tickets/'.$ticket->id)->assertOk()->assertDontSee('Segui questa segnalazione')->getContent();
    Assert::assertSame(0, $followForms($ownerHtml));

    $otherHtml = (string) $this->actingAs($other)->get('/it/tickets/'.$ticket->id)->assertOk()->assertSee('Segui questa segnalazione')->getContent();
    Assert::assertSame(1, $followForms($otherHtml));
});

it('never shows the data of other citizens in the owner view', function (): void {
    /** @var TestCase $this */
    $owner = ownerAccessUser();
    $other = ownerAccessUser();
    $follower = ownerAccessUser();
    $ticket = ownerAccessTicket($owner, ['status' => TicketStatusEnum::PENDING]);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $follower->getKey()]);

    $html = (string) $this->actingAs($owner)->get('/it/tickets/'.$ticket->id)->assertOk()->getContent();

    Assert::assertStringNotContainsString($other->email, $html);
    Assert::assertStringNotContainsString($follower->email, $html);
    Assert::assertStringNotContainsString('owner_id', $html);
});
