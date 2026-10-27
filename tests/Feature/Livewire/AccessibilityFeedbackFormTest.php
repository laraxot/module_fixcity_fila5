<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetAccessibilityDeclarationAction;
use Modules\Fixcity\Emails\AccessibilityFeedbackMail;
use Modules\Fixcity\Livewire\AccessibilityFeedbackForm;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/*
 * DCP-132: il modulo di feedback manda un'email alla casella che l'ente indica nella dichiarazione, letta lato server. Casella inventata
 * SOLO per il test. Senza casella non invia nulla; un client non puo' scegliere il destinatario; 5 invii all'ora per IP.
 */
function declareFeedbackMailbox(?string $mailbox): void
{
    app()->instance(GetAccessibilityDeclarationAction::class, new class($mailbox) extends GetAccessibilityDeclarationAction
    {
        public function __construct(private readonly ?string $mailbox) {}

        public function execute(): array
        {
            return ['feedback_email' => $this->mailbox];
        }
    });
}

beforeEach(function (): void {
    Mail::fake();
    RateLimiter::clear('accessibility-feedback:127.0.0.1');
});

it('sends the report to the mailbox declared by the municipality and confirms to the visitor', function (): void {
    declareFeedbackMailbox('segnalazioni@ente.test');

    $form = Livewire::test(AccessibilityFeedbackForm::class)
        ->set('problem', 'Il pulsante di invio non si raggiunge con la tastiera.')
        ->set('page', '/it/tickets')
        ->set('contact', 'cittadino@esempio.test')
        ->call('submit');
    $form->assertHasNoErrors();
    $form->assertSet('outcome', 'sent');
    $form->assertSet('problem', '');
    $form->assertSee(__('fixcity::accessibility.form.sent'));

    Mail::assertSent(AccessibilityFeedbackMail::class, static fn (AccessibilityFeedbackMail $mail): bool => $mail->hasTo('segnalazioni@ente.test')
        && $mail->hasReplyTo('cittadino@esempio.test')
        && $mail->problem === 'Il pulsante di invio non si raggiunge con la tastiera.'
        && $mail->page === '/it/tickets');
    Mail::assertSentCount(1);
});

it('validates the report before sending anything', function (): void {
    declareFeedbackMailbox('segnalazioni@ente.test');

    Livewire::test(AccessibilityFeedbackForm::class)
        ->set('problem', 'corto')
        ->set('contact', 'non-una-email')
        ->call('submit')
        ->assertHasErrors(['problem', 'contact']);

    Mail::assertNothingSent();
});

it('sends nothing and says so when the municipality has not declared a mailbox', function (?string $mailbox): void {
    declareFeedbackMailbox($mailbox);

    Livewire::test(AccessibilityFeedbackForm::class)
        ->assertSee(__('fixcity::accessibility.form.inactive'))
        ->set('problem', 'Un problema descritto con abbastanza parole.')
        ->call('submit')
        ->assertSet('outcome', 'unavailable')
        ->assertSee(__('fixcity::accessibility.form.unavailable'));

    Mail::assertNothingSent();
})->with(['no mailbox' => [null], 'invalid mailbox' => ['non-una-email']]);

it('does not let the client choose the recipient', function (): void {
    declareFeedbackMailbox('segnalazioni@ente.test');

    expect(fn () => Livewire::test(AccessibilityFeedbackForm::class)->set('recipient', 'altro@esempio.test'))->toThrow(Exception::class);
    expect(property_exists(AccessibilityFeedbackForm::class, 'recipient'))->toBeFalse();
});

it('stops after five reports an hour from the same address', function (): void {
    declareFeedbackMailbox('segnalazioni@ente.test');
    $form = Livewire::test(AccessibilityFeedbackForm::class);

    foreach (range(1, AccessibilityFeedbackForm::MAX_PER_HOUR) as $attempt) {
        $form->set('problem', "Problema numero {$attempt} descritto con parole.")->call('submit')->assertSet('outcome', 'sent');
    }
    $form->set('problem', 'Un altro problema descritto con parole.')->call('submit')->assertSet('outcome', 'throttled');

    Mail::assertSentCount(AccessibilityFeedbackForm::MAX_PER_HOUR);
});
