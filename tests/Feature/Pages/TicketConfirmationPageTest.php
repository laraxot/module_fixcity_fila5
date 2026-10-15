<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

/**
 * DCP-34: /{locale}/tickets/confirmation e' la pagina JSON `tickets.confirmation` (layout design-comuni) resa dal tema:
 * il blocco adattatore sostituisce :code, :email, :receipt_url, :area_url con i dati del flash o, in ripiego, del `?code=`.
 */
dataset('confirmation locales', [
    'it' => ['it', 'Grazie, abbiamo ricevuto la tua', 'Non troviamo i dati dell’ultima segnalazione inviata.', 'Abbiamo inviato il riepilogo'],
    'en' => ['en', 'Thank you, we have received your', 'We cannot find the details of the last report sent.', 'We have sent the summary'],
]);

describe('ticket confirmation page', function (): void {
    test('shows code, email and receipt link of the ticket just created', function (
        string $locale,
        string $intro,
        string $fallback,
        string $emailLine,
    ): void {
        /** @var TestCase $this */
        $owner = UserFactory::new()->createOne(['email' => 'proprietario.flash@example.test']);
        $ticket = TicketFactory::new()->createOne(['code' => 'TCK-PAGEFLASH1', 'owner_id' => $owner->id]);
        $bag = app(BuildTicketConfirmationDataAction::class)->execute($ticket, $locale);

        $this->actingAs($owner)
            ->withSession([BuildTicketConfirmationDataAction::SESSION_KEY => $bag])
            ->get("/{$locale}/tickets/confirmation")
            ->assertOk()
            ->assertSee($intro)
            ->assertSee('TCK-PAGEFLASH1')
            ->assertSee('proprietario.flash@example.test')
            ->assertSee($emailLine)
            ->assertSee("/{$locale}/tickets/track/TCK-PAGEFLASH1", false)
            ->assertDontSee($fallback)
            ->assertDontSee('report :code', false)
            ->assertDontSee('segnalazione :code', false)
            ->assertDontSee(':receipt_url', false)
            ->assertDontSee(':area_url', false);
    })->with('confirmation locales');

    test('shows the fallback text without flash and without code, with no server error', function (
        string $locale,
        string $intro,
        string $fallback,
    ): void {
        /** @var TestCase $this */
        $this->get("/{$locale}/tickets/confirmation")
            ->assertOk()
            ->assertSee($fallback)
            ->assertDontSee($intro)
            ->assertDontSee(':code', false)
            ->assertDontSee(':email', false);
    })->with('confirmation locales');

    test('shows the ticket of the code query but never the owner email', function (
        string $locale,
        string $intro,
        string $fallback,
        string $emailLine,
    ): void {
        /** @var TestCase $this */
        $owner = UserFactory::new()->createOne(['email' => 'proprietario.codice@example.test']);
        TicketFactory::new()->createOne(['code' => 'TCK-PAGECODE1', 'owner_id' => $owner->id]);

        $this->get("/{$locale}/tickets/confirmation?code=TCK-PAGECODE1")
            ->assertOk()
            ->assertSee($intro)
            ->assertSee('TCK-PAGECODE1')
            ->assertSee("/{$locale}/tickets/track/TCK-PAGECODE1", false)
            ->assertDontSee('proprietario.codice@example.test')
            ->assertDontSee($emailLine)
            ->assertDontSee($fallback)
            ->assertDontSee(':email', false);
    })->with('confirmation locales');

    test('shows the fallback text for an unknown code', function (): void {
        /** @var TestCase $this */
        $this->get('/it/tickets/confirmation?code=TCK-NOPE0000')
            ->assertOk()
            ->assertSee('Non troviamo i dati dell’ultima segnalazione inviata.')
            ->assertDontSee('TCK-NOPE0000');
    });
});
