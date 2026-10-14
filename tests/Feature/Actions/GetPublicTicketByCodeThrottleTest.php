<?php

declare(strict_types=1);

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Fixcity\Actions\GetPublicTicketByCodeAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/**
 * DCP-149: il codice di tracciamento e' un capability token (chi lo conosce vede stato e cronologia anche di una segnalazione non ancora pubblica).
 * Il limite di 10 richieste al minuto stava nella pagina Folio del modulo (`tickets/track.blade.php`), oscurata dalla pagina del tema: la ricerca
 * per codice non aveva piu' freni (14 richieste di fila, 14 risposte 200). Il freno vive ora nell'unico punto per cui passano la pagina di
 * tracciamento e quella di conferma: i TENTATIVI A VUOTO (codice sconosciuto) sono limitati per indirizzo, quelli riusciti no.
 */
beforeEach(function (): void {
    RateLimiter::clear(GetPublicTicketByCodeAction::throttleKey('127.0.0.1'));
});

test('a lookup with a known code is never throttled', function (): void {
    /** @var TestCase $this */
    $ticket = TicketFactory::new()->createOne();
    $action = app(GetPublicTicketByCodeAction::class);

    for ($i = 0; $i < 25; $i++) {
        $found = $action->execute((string) $ticket->code);
        expect($found)->toBeInstanceOf(Ticket::class);
    }
});

test('guessing codes is throttled after ten misses and answers 429 with a retry delay', function (): void {
    /** @var TestCase $this */
    $action = app(GetPublicTicketByCodeAction::class);

    for ($i = 0; $i < GetPublicTicketByCodeAction::MAX_MISSES; $i++) {
        expect($action->execute('TCK-NONESISTENTE'.$i))->toBeNull();
    }

    try {
        $action->execute('TCK-NONESISTENTE-ANCORA');
        $this->fail('Dopo dieci tentativi a vuoto la ricerca deve essere bloccata.');
    } catch (ThrottleRequestsException $exception) {
        expect($exception->getStatusCode())->toBe(429);
        expect($exception->getHeaders())->toHaveKey('Retry-After');
    }
});

test('a blocked address cannot even use a valid code until the window ends', function (): void {
    /** @var TestCase $this */
    $ticket = TicketFactory::new()->createOne();
    $action = app(GetPublicTicketByCodeAction::class);

    for ($i = 0; $i < GetPublicTicketByCodeAction::MAX_MISSES; $i++) {
        $action->execute('TCK-SBAGLIATO'.$i);
    }

    expect(fn () => $action->execute((string) $ticket->code))->toThrow(ThrottleRequestsException::class);
});

test('an empty code is not a lookup and does not use the budget', function (): void {
    /** @var TestCase $this */
    $action = app(GetPublicTicketByCodeAction::class);

    for ($i = 0; $i < 30; $i++) {
        expect($action->execute(null))->toBeNull();
        expect($action->execute('   '))->toBeNull();
    }
});

test('the tracking page answers 429 after repeated wrong codes and keeps answering 200 before that', function (): void {
    /** @var TestCase $this */
    for ($i = 0; $i < GetPublicTicketByCodeAction::MAX_MISSES; $i++) {
        $this->get('/it/tickets/track?code=TCK-ZZZZZZZZZZZZ'.$i)->assertOk();
    }

    $this->get('/it/tickets/track?code=TCK-ZZZZZZZZZZZZ99')->assertStatus(429);
});

test('the 429 shows the themed page with the message in the language of the visitor', function (string $locale, string $title): void {
    /** @var TestCase $this */
    for ($i = 0; $i < GetPublicTicketByCodeAction::MAX_MISSES; $i++) {
        $this->get('/'.$locale.'/tickets/track?code=TCK-QQQQQQQQQQQQ'.$i);
    }

    $this->get('/'.$locale.'/tickets/track?code=TCK-QQQQQQQQQQQQ99')
        ->assertStatus(429)
        ->assertSee($title)
        ->assertSee('it-header-wrapper', false)
        ->assertDontSee('fixcity::ticket.track.rate_limited_title');
})->with([
    'it' => ['it', 'Troppe richieste'],
    'en' => ['en', 'Too many requests'],
    'de' => ['de', 'Zu viele Anfragen'],
    'es' => ['es', 'Demasiadas solicitudes'],
]);

