<?php

declare(strict_types=1);

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('shows localized labels and retry guidance for an unknown Italian tracking code', function (): void {
    /** @var TestCase $this */
    $this->get('/it/tickets/track?code=TCK-NOTFOUND000000')
        ->assertOk()
        ->assertSee('<title>Traccia la segnalazione</title>', false)
        ->assertSee('Codice segnalazione')
        ->assertSee('Nessuna segnalazione trovata per questo codice.')
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('Inserisci il codice completo: TCK- seguito da 16 caratteri.')
        ->assertDontSee('fixcity::ticket.fields.code.label');
});

it('shows localized labels and retry guidance for an unknown English tracking code', function (): void {
    /** @var TestCase $this */
    $this->get('/en/tickets/track?code=TCK-NOTFOUND000000')
        ->assertOk()
        ->assertSee('<title>Track a report</title>', false)
        ->assertSee('Report code')
        ->assertSee('No report was found for this code.')
        ->assertSee('aria-invalid="true"', false)
        ->assertDontSee('fixcity::ticket.fields.code.label')
        ->assertDontSee('fixcity::ticket.fields.status.label');
});

it('shows the status label for a valid Italian tracking result', function (): void {
    /** @var TestCase $this */
    TicketFactory::new()->createOne([
        'name' => 'Lampione da riparare',
        'status' => TicketStatusEnum::IN_PROGRESS,
        'code' => 'TCK-ITALIANLABEL01',
    ]);

    $this->get('/it/tickets/track?code=TCK-ITALIANLABEL01')
        ->assertOk()
        ->assertSee('<title>Traccia la segnalazione</title>', false)
        ->assertSee('Codice segnalazione')
        ->assertSee('Stato')
        ->assertSee('In lavorazione')
        ->assertSee('Lampione da riparare')
        ->assertDontSee('in_progress')
        ->assertDontSee('<code></code>', false)
        ->assertDontSee('<code>TCK-ITALIANLABEL01</code>', false)
        ->assertDontSee('fixcity::ticket.fields.status.label');
});

it('shows the status label for a valid English tracking result', function (): void {
    /** @var TestCase $this */
    TicketFactory::new()->createOne([
        'name' => 'Street light repair',
        'status' => TicketStatusEnum::IN_PROGRESS,
        'code' => 'TCK-ENGLISHLABEL01',
    ]);

    $this->get('/en/tickets/track?code=TCK-ENGLISHLABEL01')
        ->assertOk()
        ->assertSee('<title>Track a report</title>', false)
        ->assertSee('Report code')
        ->assertSee('Status')
        ->assertSee('In Progress')
        ->assertSee('Street light repair')
        ->assertDontSee('in_progress')
        ->assertDontSee('<code></code>', false)
        ->assertDontSee('<code>TCK-ENGLISHLABEL01</code>', false)
        ->assertDontSee('fixcity::ticket.fields.code.label')
        ->assertDontSee('fixcity::ticket.fields.status.label');
});

it('renders a localized retry page after the public tracking rate limit', function (string $locale, string $title, string $message, string $retry): void {
    /** @var TestCase $this */
    for ($request = 0; $request < 10; $request++) {
        $this->get('/'.$locale.'/tickets/track')->assertOk();
    }

    $this->get('/'.$locale.'/tickets/track')
        ->assertTooManyRequests()
        ->assertSee($title)
        ->assertSee($message)
        ->assertSee($retry)
        ->assertSee('role="status"', false)
        ->assertHeader('Retry-After');
})->with([
    'Italian' => [
        'it',
        'Troppe richieste',
        'Hai effettuato troppe ricerche in poco tempo. Attendi un minuto e riprova.',
        'Torna alla ricerca del codice',
    ],
    'English' => [
        'en',
        'Too many requests',
        'You have searched too many times in a short period. Wait one minute and try again.',
        'Return to tracking search',
    ],
]);

it('shows a tracking code only when the authenticated owner looks up their ticket by id', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'name' => 'Marciapiede accessibile',
        'owner_id' => $owner->getAuthIdentifier(),
        'status' => TicketStatusEnum::IN_PROGRESS,
        'code' => 'TCK-OWNERLOOKUP01',
    ]);
    $this->actingAs($owner);

    $this->get('/it/tickets/track?ticket_id='.$ticket->id)
        ->assertOk()
        ->assertSee('TCK-OWNERLOOKUP01')
        ->assertSee('<code>TCK-OWNERLOOKUP01</code>', false)
        ->assertSee('Marciapiede accessibile')
        ->assertSee('In lavorazione');
});
