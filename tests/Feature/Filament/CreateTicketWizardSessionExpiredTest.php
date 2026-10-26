<?php

declare(strict_types=1);

use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-286 (FO-3-06): con la sessione scaduta o dopo l'uscita da un'altra scheda "Invia" rispondeva 200 senza dire nulla (misurato su :8091:
 * nessun avviso visibile, nessuna segnalazione). Il codice scriveva un errore `global` che nessuna vista mostra, con una chiave lang inesistente.
 * Ora la vista mostra l'avviso con "Accedi di nuovo", che porta al login e poi riporta al wizard (`?redirect=`, come "Accedi per commentare").
 */

it('tells a guest who presses Send that the session expired and offers to sign in again', function (string $locale, string $loginAgain): void {
    /** @var TestCase $this */
    app()->setLocale($locale);
    LaravelLocalization::setLocale($locale);

    $component = Livewire::test(CreateTicketWizardWidget::class)->call('submit');

    Assert::assertSame(0, Ticket::query()->count());
    $component
        ->assertSeeHtml('data-element="session-expired"')
        ->assertSeeHtml('role="alert"')
        ->assertSee($loginAgain)
        ->assertSeeHtml('/'.$locale.'/auth/login?redirect='.rawurlencode('/'.$locale.'/tickets/create'))
        ->assertSeeHtml('target="_blank"');
})->with([
    'italiano' => ['it', 'Accedi di nuovo'],
    'inglese' => ['en', 'Sign in again'],
    'tedesco' => ['de', 'Erneut anmelden'],
    'spagnolo' => ['es', 'Iniciar sesión de nuevo'],
]);

it('does not show the notice before anyone presses Send, nor to a signed-in user', function (): void {
    /** @var TestCase $this */
    $user = UserFactory::new()->createOne();

    Livewire::test(CreateTicketWizardWidget::class)->assertDontSeeHtml('data-element="session-expired"');

    $this->actingAs($user);
    Livewire::test(CreateTicketWizardWidget::class)->call('submit')->assertDontSeeHtml('data-element="session-expired"');
});

it('clears the notice once the user is signed in again and presses Send', function (): void {
    /** @var TestCase $this */
    $component = Livewire::test(CreateTicketWizardWidget::class)->call('submit')->assertSeeHtml('data-element="session-expired"');

    $this->actingAs(UserFactory::new()->createOne());
    $component->call('submit')->assertDontSeeHtml('data-element="session-expired"');
});

it('does not let the client switch the notice on or off', function (): void {
    Livewire::test(CreateTicketWizardWidget::class)->set('sessionExpired', true);
})->throws(CannotUpdateLockedPropertyException::class);
