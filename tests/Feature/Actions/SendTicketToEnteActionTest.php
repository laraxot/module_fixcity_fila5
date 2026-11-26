<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Mail;
use Modules\Fixcity\Actions\SendTicketToEnteAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Emails\TicketCommunicationMail;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

uses(TestCase::class);

/** @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md */
it('does not email a public contact or PEC address without an explicit intake mailbox', function (): void {
    Mail::fake();
    config()->set('ente.ticket_reports_email', '');
    config()->set('ente.email', 'contact@example.test');
    config()->set('ente.pec', 'pec@example.test');

    $ticket = TicketFactory::new()->createOne();

    expect(app(SendTicketToEnteAction::class)->execute($ticket))->toBeFalse();
    Mail::assertNothingSent();
})->group('TICKET-ENTE-MAIL-INV-1');

/** @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md */
it('rejects malformed authority mailbox addresses', function (): void {
    Mail::fake();
    config()->set('ente.ticket_reports_email', "authority@example.test\r\nBcc:invalid@example.test");

    $ticket = TicketFactory::new()->createOne();

    expect(app(SendTicketToEnteAction::class)->execute($ticket))->toBeFalse();
    Mail::assertNothingSent();
})->group('TICKET-ENTE-MAIL-INV-2');

/** @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md */
it('sends a minimal report email once and audits SMTP transport success', function (): void {
    Mail::fake();
    config()->set('ente.ticket_reports_email', 'reports@example.test');

    $ticket = TicketFactory::new()->createOne([
        'name' => 'Dati privati nella descrizione',
        'content' => 'Non inviare contenuti personali via email.',
    ]);

    $action = app(SendTicketToEnteAction::class);

    expect($action->execute($ticket))->toBeTrue();
    expect($action->execute($ticket))->toBeTrue();

    Mail::assertSent(TicketCommunicationMail::class, 1);
    Mail::assertSent(TicketCommunicationMail::class, function (TicketCommunicationMail $mail) use ($ticket): bool {
        return str_contains($mail->body, '#'.SafeStringCastAction::cast($ticket->getKey()))
            && ! str_contains($mail->body, 'Dati privati')
            && ! str_contains($mail->body, 'Non inviare')
            && ($ticket->code === null || ! str_contains($mail->body, $ticket->code));
    });

    $events = TicketActivity::query()
        ->where('ticket_id', $ticket->getKey())
        ->where('event_type', TicketActivityEventTypeEnum::MailSent->value)
        ->get();

    expect($events)->toHaveCount(1)
        ->and($events->first()?->visibility)->toBe('internal')
        ->and(($events->first()->payload ?? [])['source'] ?? null)->toBe('ente_ticket_intake');
})->group('TICKET-ENTE-MAIL-INV-3', 'TICKET-ENTE-MAIL-INV-4', 'TICKET-ENTE-MAIL-INV-5');

/** @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md */
it('records failed SMTP handoff without interrupting ticket creation', function (): void {
    config()->set('ente.ticket_reports_email', 'reports@example.test');
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Transport unavailable'));

    $ticket = TicketFactory::new()->createOne();

    expect(app(SendTicketToEnteAction::class)->execute($ticket))->toBeFalse();

    expect(TicketActivity::query()
        ->where('ticket_id', $ticket->getKey())
        ->where('event_type', TicketActivityEventTypeEnum::MailFailed->value)
        ->count())->toBe(1);
})->group('TICKET-ENTE-MAIL-INV-5', 'TICKET-ENTE-MAIL-INV-6');
