<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Dom\Node;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Modules\Fixcity\Tests\TestCase;
use Modules\Gdpr\Filament\Widgets\Auth\RegisterWidget;
use Modules\User\Filament\Widgets\Auth\LoginWidget;
use PHPUnit\Framework\Assert;

use function Safe\preg_replace;

uses(TestCase::class);

/*
 * DCP-290 (FO-5-10, FO-5-11, FO-5-30): la registrazione non marcava i campi con errore (nessun aria-invalid ne' aria-describedby) e i
 * messaggi usavano il nome tecnico dell'attributo ("first name e' richiesto"); il login mostrava solo il primo errore nell'avviso; nessuna
 * pagina sposta il focus dopo un invio fallito (lo fa lo script condiviso, provato nel browser) e login e recupero avevano `autofocus`.
 */

function dcp290AuthFragment(string $html): HTMLDocument
{
    return HTMLDocument::createFromString('<!doctype html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);
}

function dcp290AuthText(?Node $node): string
{
    return $node === null ? '' : trim((string) preg_replace('/\s+/u', ' ', (string) $node->textContent));
}

/**
 * Il campo `$id` e' marcato non valido e punta, con aria-describedby, a un elemento di errore con testo.
 */
function dcp290AssertFieldPointsToItsError(HTMLDocument $document, string $id): string
{
    $field = $document->getElementById($id);
    Assert::assertNotNull($field, "Campo #{$id} assente.");
    Assert::assertSame('true', $field->getAttribute('aria-invalid'), "#{$id} senza aria-invalid.");
    $ids = explode(' ', trim((string) $field->getAttribute('aria-describedby')));
    $messages = [];
    foreach ($ids as $describedBy) {
        $element = $document->getElementById($describedBy);
        if ($element !== null && str_contains($describedBy, 'error')) {
            $messages[] = dcp290AuthText($element);
        }
    }
    Assert::assertNotSame([], $messages, "#{$id}: aria-describedby non punta a nessun errore ({$field->getAttribute('aria-describedby')}).");
    Assert::assertNotSame('', $messages[0], "#{$id}: errore vuoto.");

    return $messages[0];
}

beforeEach(function (): void {
    // `Password::defaults()` ha `uncompromised`: nessuna richiesta a HaveIBeenPwned dal test. Il registro dei tentativi e' su file: azzerato.
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('', 200)]);
    RateLimiter::clear('register|127.0.0.1');
});

it('marks every invalid registration field and explains the errors in plain words in every language', function (string $locale): void {
    /** @var TestCase $this */
    app()->setLocale($locale);
    $component = Livewire::test(RegisterWidget::class)->call('submit');
    $document = dcp290AuthFragment($component->html());

    $messages = [];
    foreach (['first_name', 'last_name', 'email', 'password', 'password_confirmation', 'privacy_accepted', 'terms_accepted'] as $id) {
        $messages[$id] = dcp290AssertFieldPointsToItsError($document, $id);
    }

    foreach (['first_name' => 'first name', 'last_name' => 'last name', 'email' => 'email', 'password_confirmation' => 'password confirmation'] as $field => $technical) {
        $label = trans("gdpr::register.fields.{$field}.label", [], $locale);
        Assert::assertStringContainsStringIgnoringCase($label, $messages[$field], "{$locale}/{$field}: il messaggio non nomina il campo con la sua etichetta '{$label}': {$messages[$field]}");
        // Tolta l'etichetta (che puo' contenere la parola, es. "Indirizzo email") non resta il nome tecnico dell'attributo.
        $withoutLabel = str_ireplace($label, '', $messages[$field]);
        Assert::assertStringNotContainsStringIgnoringCase($technical.' ', $withoutLabel.' ', "{$locale}/{$field}: nome tecnico '{$technical}' nel messaggio: {$messages[$field]}");
    }

    $alert = $document->querySelector('[role="alert"]');
    Assert::assertNotNull($alert, 'Riepilogo role=alert assente.');
    Assert::assertStringContainsString((string) count($messages), dcp290AuthText($alert), 'Il riepilogo non riporta il conteggio degli errori.');
    foreach ($messages as $message) {
        Assert::assertStringContainsString($message, dcp290AuthText($alert), 'Il riepilogo non elenca tutti gli errori.');
    }
})->with(['it', 'en', 'de', 'es']);

it('summarises all the login errors with a count and flags both invalid fields', function (string $locale): void {
    /** @var TestCase $this */
    app()->setLocale($locale);
    $document = dcp290AuthFragment(Livewire::test(LoginWidget::class)->call('login')->html());

    $messages = [
        dcp290AssertFieldPointsToItsError($document, 'form.email'),
        dcp290AssertFieldPointsToItsError($document, 'form.password'),
    ];
    $alert = $document->querySelector('[role="alert"]');
    Assert::assertNotNull($alert);
    Assert::assertStringContainsString('2', dcp290AuthText($alert), 'Conteggio nel riepilogo.');
    foreach ($messages as $message) {
        Assert::assertStringContainsString($message, dcp290AuthText($alert), 'Il riepilogo del login mostra solo il primo errore.');
    }
})->with(['it', 'en', 'de', 'es']);

it('has no autofocus on login, registration, forgot password or new password pages and loads the focus script once', function (string $path): void {
    /** @var TestCase $this */
    $document = HTMLDocument::createFromString((string) $this->get($path)->assertOk()->getContent(), LIBXML_NOERROR);

    Assert::assertSame(0, $document->querySelectorAll('[autofocus]')->length, "Autofocus su {$path}.");
    Assert::assertSame(1, $document->querySelectorAll('script[data-auth-focus-invalid]')->length, "Script del focus su {$path}.");
})->with([
    'login' => '/it/auth/login',
    'register' => '/it/auth/register',
    'forgot' => '/it/auth/password/reset',
    'token' => '/it/auth/password/un-token',
]);
