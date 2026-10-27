<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * DCP-34: /{locale}/tickets/confirmation e' la pagina JSON `tickets.confirmation` (layout design-comuni) resa dal tema:
 * il blocco adattatore sostituisce :code, :email, :receipt_url, :area_url con i dati del flash o, in ripiego, del `?code=`.
 */
dataset('confirmation locales', [
    'it' => ['it', 'Grazie, abbiamo ricevuto la tua', 'Non troviamo i dati dell’ultima segnalazione inviata.', 'Ti invieremo il riepilogo', 'Non ti scriviamo per email'],
    'en' => ['en', 'Thank you, we have received your', 'We cannot find the details of the last report sent.', 'We will send the summary', 'We are not sending you an email'],
    'de' => ['de', 'Vielen Dank, wir haben Ihre', 'Wir finden die Angaben zur zuletzt gesendeten Meldung nicht.', 'Wir senden Ihnen die Zusammenfassung', 'Wir senden Ihnen keine E-Mail'],
    'es' => ['es', 'Gracias, hemos recibido tu', 'No encontramos los datos de la última incidencia enviada.', 'Te enviaremos el resumen', 'No te enviamos un correo'],
]);

describe('ticket confirmation page', function (): void {
    test('shows code, email and receipt link of the ticket just created', function (
        string $locale,
        string $intro,
        string $fallback,
        string $emailLine,
        string $inAppLine,
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
            ->assertDontSee($inAppLine)
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
        string $emailLine,
        string $inAppLine,
    ): void {
        /** @var TestCase $this */
        $this->get("/{$locale}/tickets/confirmation")
            ->assertOk()
            ->assertSee($fallback)
            ->assertDontSee($intro)
            ->assertDontSee($emailLine)
            ->assertDontSee($inAppLine)
            ->assertDontSee(':code', false)
            ->assertDontSee(':email', false);
    })->with('confirmation locales');

    test('shows the ticket of the code query but never the owner email', function (
        string $locale,
        string $intro,
        string $fallback,
        string $emailLine,
        string $inAppLine,
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
            ->assertDontSee($inAppLine)
            ->assertDontSee($fallback)
            ->assertDontSee(':email', false);
    })->with('confirmation locales');

    /*
     * DCP-286 (FO-2-25, FO-3-03): la mail di ricezione parte solo con email verificata. Con l'email non verificata la conferma non
     * promette una mail e non mostra l'indirizzo: manda all'area personale, dove c'e' sempre la notifica.
     */
    test('does not promise an email to an owner whose address is not verified', function (
        string $locale,
        string $intro,
        string $fallback,
        string $emailLine,
        string $inAppLine,
    ): void {
        /** @var TestCase $this */
        $owner = UserFactory::new()->unverified()->createOne(['email' => 'non.verificata@example.test']);
        $ticket = TicketFactory::new()->createOne(['code' => 'TCK-PAGEUNVER1', 'owner_id' => $owner->id]);
        $bag = app(BuildTicketConfirmationDataAction::class)->execute($ticket, $locale);

        $response = $this->actingAs($owner)
            ->withSession([BuildTicketConfirmationDataAction::SESSION_KEY => $bag])
            ->get("/{$locale}/tickets/confirmation")
            ->assertOk()
            ->assertSee($intro)
            ->assertSee('TCK-PAGEUNVER1')
            ->assertSee($inAppLine)
            ->assertDontSee($emailLine)
            ->assertDontSee('non.verificata@example.test')
            ->assertDontSee(':email', false);

        // la riga c'e' una volta sola (le copie del JSON non devono ripeterla)
        Assert::assertSame(1, substr_count((string) $response->getContent(), $inAppLine));
    })->with('confirmation locales');

    /*
     * DCP-286 (FO-2-26, FO-3-24, FO-6-02): niente servizio inventato ("Richiesta appuntamento"), niente prenotazione, niente sezione
     * "Servizi correlati" vuota; la FAQ porta alla pagina vera, nella lingua della pagina.
     */
    test('shows no invented service and links the real FAQ page', function (string $locale): void {
        /** @var TestCase $this */
        $owner = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne(['code' => 'TCK-PAGEHONEST1', 'owner_id' => $owner->id]);
        $bag = app(BuildTicketConfirmationDataAction::class)->execute($ticket, $locale);

        $this->actingAs($owner)
            ->withSession([BuildTicketConfirmationDataAction::SESSION_KEY => $bag])
            ->get("/{$locale}/tickets/confirmation")
            ->assertOk()
            ->assertDontSee('Richiesta appuntamento')
            ->assertDontSee('Appointment request')
            ->assertDontSee('Terminanfrage')
            ->assertDontSee('Solicitud de cita')
            ->assertDontSee('Prenota appuntamento')
            ->assertDontSee('Book an appointment')
            ->assertDontSee('Termin vereinbaren')
            ->assertDontSee('Reserva una cita')
            ->assertDontSee('data-element="appointment-booking"', false)
            ->assertDontSee('id="related-service"', false)
            ->assertSee("href=\"/{$locale}/domande-frequenti\"", false);
    })->with(['it', 'en', 'de', 'es']);

    /*
     * DCP-286 (FO-3-25): la valutazione a stelle della conferma non salvava nulla (solo un evento nel browser) ma rispondeva
     * "Grazie, il tuo parere ci aiuterà". Finche' non c'e' dove raccogliere il parere, il blocco non si mostra.
     */
    test('does not show a rating form that saves nothing', function (string $locale): void {
        /** @var TestCase $this */
        $owner = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne(['code' => 'TCK-PAGERATING1', 'owner_id' => $owner->id]);
        $bag = app(BuildTicketConfirmationDataAction::class)->execute($ticket, $locale);

        $this->actingAs($owner)
            ->withSession([BuildTicketConfirmationDataAction::SESSION_KEY => $bag])
            ->get("/{$locale}/tickets/confirmation")
            ->assertOk()
            ->assertDontSee('data-element="feedback"', false)
            ->assertDontSee('data-element="feedback-input-text"', false)
            ->assertDontSee('rating-feedback', false);
    })->with(['it', 'en', 'de', 'es']);

    test('points the follow-up to the personal area, not to a "reserved area" that does not exist', function (): void {
        /** @var TestCase $this */
        $owner = UserFactory::new()->createOne();
        $ticket = TicketFactory::new()->createOne(['code' => 'TCK-PAGEAREA01', 'owner_id' => $owner->id]);
        $bag = app(BuildTicketConfirmationDataAction::class)->execute($ticket, 'it');

        $this->actingAs($owner)
            ->withSession([BuildTicketConfirmationDataAction::SESSION_KEY => $bag])
            ->get('/it/tickets/confirmation')
            ->assertOk()
            ->assertSee('nella tua area personale')
            ->assertDontSee('area riservata');
    });

    test('shows the fallback text for an unknown code', function (): void {
        /** @var TestCase $this */
        $this->get('/it/tickets/confirmation?code=TCK-NOPE0000')
            ->assertOk()
            ->assertSee('Non troviamo i dati dell’ultima segnalazione inviata.')
            ->assertDontSee('TCK-NOPE0000');
    });
});
