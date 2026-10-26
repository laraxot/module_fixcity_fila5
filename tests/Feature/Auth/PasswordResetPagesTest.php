<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Dom\Node;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Filament\Widgets\Auth\ForgotPasswordWidget;
use Modules\User\Filament\Widgets\Auth\ResetPasswordWidget;
use Modules\User\Models\User;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;
use function Safe\preg_replace;

uses(TestCase::class);

/*
 * DCP-290 (FO-5-02, FO-3-01): "Password dimenticata" e "nuova password" erano lo scaffold grezzo di Laravel: senza header, footer, h1 e
 * main, in inglese in tutte le lingue, con la chiave `passwords.sent` come messaggio e un 500 all'invio del form con token.
 */

function dcp290Document(TestCase $test, string $path): HTMLDocument
{
    return HTMLDocument::createFromString((string) $test->get($path)->assertOk()->getContent(), LIBXML_NOERROR);
}

function dcp290Fragment(string $html): HTMLDocument
{
    return HTMLDocument::createFromString('<!doctype html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);
}

function dcp290Text(?Node $node): string
{
    return $node === null ? '' : trim((string) preg_replace('/\s+/u', ' ', (string) $node->textContent));
}

/**
 * @return array{user: User, email: string}
 */
function dcp290Citizen(string $password = 'Vecchia-Password-2026!'): array
{
    $email = 'recupero.'.uniqid().'@example.test';
    $user = UserFactory::new()->createOne(['email' => $email, 'password' => Hash::make($password)]);

    return ['user' => $user, 'email' => $email];
}

function dcp290Token(string $email): string
{
    $broker = Password::broker();
    $user = $broker->getUser(['email' => $email]);
    Assert::assertNotNull($user, 'Utente di prova non trovato dal broker.');

    return $broker->createToken($user);
}

it('serves the forgot password page inside the site shell in every language', function (string $locale): void {
    /** @var TestCase $this */
    $document = dcp290Document($this, "/{$locale}/auth/password/reset");
    $home = dcp290Document($this, "/{$locale}");

    Assert::assertSame($locale, $document->documentElement?->getAttribute('lang'));
    Assert::assertSame(1, $document->querySelectorAll('h1')->length, 'Un solo h1.');
    Assert::assertSame(trans('user::password_recovery.title', [], $locale), dcp290Text($document->querySelector('h1')));
    Assert::assertSame(1, $document->querySelectorAll('main')->length, 'Un solo main.');
    Assert::assertGreaterThanOrEqual(1, $document->querySelectorAll('header')->length, 'Header del sito.');
    Assert::assertGreaterThanOrEqual(1, $document->querySelectorAll('footer')->length, 'Footer del sito.');
    Assert::assertSame(0, $document->querySelectorAll('[autofocus]')->length, 'Nessun autofocus.');
    Assert::assertSame(1, $document->querySelectorAll('script[data-auth-focus-invalid]')->length, 'Script del focus sul primo campo non valido.');

    $title = dcp290Text($document->querySelector('title'));
    Assert::assertNotSame('', $title);
    Assert::assertNotSame(dcp290Text($home->querySelector('title')), $title, 'Titolo diverso dalla home.');

    $main = $document->querySelector('main');
    Assert::assertNotNull($main);
    foreach ($main->querySelectorAll('script, style, svg') as $skip) {
        $skip->remove();
    }
    $text = dcp290Text($main);
    Assert::assertSame(0, preg_match_all('/\b[a-z][a-z0-9_-]*::[a-z][a-z0-9_.-]+\b|\bpasswords\.[a-z]+\b/u', $text), "Chiave grezza visibile: {$text}");
    if ($locale !== 'en') {
        foreach (['Reset password', 'Email address', 'Send password reset link', 'return to login'] as $english) {
            Assert::assertStringNotContainsString($english, $text, "Testo inglese fisso in {$locale}.");
        }
    }
})->with(['it', 'en', 'de', 'es']);

it('serves the new password page inside the site shell with the email taken from the link', function (string $locale): void {
    /** @var TestCase $this */
    $email = 'qualcuno@example.test';
    $document = dcp290Document($this, "/{$locale}/auth/password/un-token-qualsiasi?email=".urlencode($email));

    Assert::assertSame($locale, $document->documentElement?->getAttribute('lang'));
    Assert::assertSame(1, $document->querySelectorAll('h1')->length);
    Assert::assertSame(trans('user::password_recovery.reset_title', [], $locale), dcp290Text($document->querySelector('h1')));
    Assert::assertSame(1, $document->querySelectorAll('main')->length);
    Assert::assertGreaterThanOrEqual(1, $document->querySelectorAll('header')->length);
    Assert::assertGreaterThanOrEqual(1, $document->querySelectorAll('footer')->length);
    Assert::assertSame(0, $document->querySelectorAll('[autofocus]')->length);
    Assert::assertSame(1, $document->querySelectorAll('script[data-auth-focus-invalid]')->length);
    // Il valore dei campi Filament non sta nell'attributo `value` (lo scrive Livewire dallo stato): si legge lo stato del widget.
    $snapshots = '';
    foreach ($document->querySelectorAll('main [wire\\:snapshot]') as $widget) {
        $snapshots .= (string) $widget->getAttribute('wire:snapshot');
    }
    Assert::assertStringContainsString('"email":"'.$email.'"', $snapshots, 'Email precompilata da ?email=.');
    Assert::assertSame(2, $document->querySelectorAll('main input[type="password"]')->length);
    if ($locale !== 'en') {
        foreach (['Reset password', 'Email address', 'Confirm Password'] as $english) {
            Assert::assertStringNotContainsString($english, dcp290Text($document->querySelector('main')));
        }
    }
})->with(['it', 'en', 'de', 'es']);

it('answers the same way for a known and an unknown email, in a status region', function (string $locale): void {
    /** @var TestCase $this */
    app()->setLocale($locale);
    Notification::fake();
    $citizen = dcp290Citizen();
    $expected = trans('user::password_recovery.sent', [], $locale);

    $known = Livewire::test(ForgotPasswordWidget::class)->set('data.email', $citizen['email'])->call('sendResetLink');
    $unknown = Livewire::test(ForgotPasswordWidget::class)->set('data.email', 'nessuno.'.uniqid().'@example.test')->call('sendResetLink');

    foreach ([$known, $unknown] as $component) {
        $component->assertHasNoErrors();
        $status = dcp290Fragment($component->html())->querySelector('[role="status"]');
        Assert::assertNotNull($status, 'Regione role=status assente.');
        Assert::assertSame($expected, dcp290Text($status));
    }
    Notification::assertSentToTimes(Password::broker()->getUser(['email' => $citizen['email']]), ResetPassword::class, 1);
    Notification::assertCount(1);
})->with(['it', 'en', 'de', 'es']);

it('keeps the status region in the page before any message and puts the invalid email in the error summary', function (): void {
    /** @var TestCase $this */
    app()->setLocale('it');
    $empty = Livewire::test(ForgotPasswordWidget::class);
    $region = dcp290Fragment($empty->html())->querySelector('[role="status"]');
    Assert::assertNotNull($region, 'La regione live deve esistere prima del messaggio, altrimenti non viene annunciato.');
    Assert::assertSame('', dcp290Text($region));

    $component = Livewire::test(ForgotPasswordWidget::class)->set('data.email', 'non-una-email')->call('sendResetLink');
    $component->assertHasErrors(['data.email']);
    $document = dcp290Fragment($component->html());
    $input = $document->querySelector('input[type="email"]');
    Assert::assertNotNull($input, 'Campo email assente.');
    Assert::assertSame('true', $input->getAttribute('aria-invalid'));
    Assert::assertNotSame('', (string) $input->getAttribute('aria-describedby'));
    Assert::assertNotNull($document->querySelector('[role="alert"]'), 'Riepilogo errori role=alert.');
    Assert::assertStringContainsString('1', dcp290Text($document->querySelector('[role="alert"]')));
    Assert::assertSame(0, preg_match_all('/passwords\.[a-z]+/', dcp290Text($document->documentElement)));
});

it('sets a new password from a real token and lets the citizen sign in with it', function (): void {
    /** @var TestCase $this */
    app()->setLocale('it');
    $old = 'Vecchia-Password-2026!';
    $new = 'Nuova-Password-2026!x';
    $citizen = dcp290Citizen($old);
    $token = dcp290Token($citizen['email']);

    $component = Livewire::test(ResetPasswordWidget::class, ['token' => $token, 'email' => $citizen['email']]);
    $component->assertSet('data.email', $citizen['email']);
    $component->set('data.password', $new);
    $component->set('data.password_confirmation', $new);
    $component->call('resetPassword');
    $component->assertHasNoErrors();
    $component->assertRedirect();

    Assert::assertSame(trans('user::password_recovery.reset_done'), session('status'));
    Assert::assertTrue(Auth::attempt(['email' => $citizen['email'], 'password' => $new]), 'Accesso con la nuova password (nessun doppio hash).');
    Auth::logout();
    Assert::assertFalse(Auth::attempt(['email' => $citizen['email'], 'password' => $old]), 'La vecchia password non vale piu.');
    Auth::logout();
});

it('refuses an invalid link with a translated message and without a server error', function (string $locale): void {
    /** @var TestCase $this */
    app()->setLocale($locale);
    $citizen = dcp290Citizen();
    $new = 'Nuova-Password-2026!x';

    $component = Livewire::test(ResetPasswordWidget::class, ['token' => 'token-sbagliato', 'email' => $citizen['email']]);
    $component->set('data.password', $new);
    $component->set('data.password_confirmation', $new);
    $component->call('resetPassword');

    $component->assertHasErrors(['data.email']);
    $component->assertNoRedirect();
    $text = dcp290Text(dcp290Fragment($component->html())->documentElement);
    Assert::assertStringContainsString(trans('user::password_recovery.invalid_link', [], $locale), $text);
    Assert::assertSame(0, preg_match_all('/passwords\.[a-z]+/', $text), 'Chiave grezza visibile.');
    Assert::assertFalse(Auth::attempt(['email' => $citizen['email'], 'password' => $new]));
})->with(['it', 'en', 'de', 'es']);

it('shows the outcome of the password change on the login page in a status region', function (string $locale): void {
    /** @var TestCase $this */
    $message = trans('user::password_recovery.reset_done', [], $locale);
    $document = HTMLDocument::createFromString((string) $this->withSession(['status' => $message])->get("/{$locale}/auth/login")->assertOk()->getContent(), LIBXML_NOERROR);

    $found = false;
    foreach ($document->querySelectorAll('main [role="status"]') as $region) {
        $found = $found || str_contains(dcp290Text($region), $message);
    }
    Assert::assertTrue($found, 'Esito del cambio password non annunciato nel login.');
})->with(['it', 'en', 'de', 'es']);

it('has the password recovery translations in every language, different from Italian', function (): void {
    $keys = ['title', 'intro', 'submit', 'sent', 'back_to_login', 'reset_title', 'reset_intro', 'reset_submit', 'reset_done', 'invalid_link', 'request_new', 'sending', 'saving'];
    foreach ($keys as $key) {
        $italian = trans("user::password_recovery.{$key}", [], 'it');
        Assert::assertNotSame("user::password_recovery.{$key}", $italian, "Manca it/{$key}.");
        foreach (['en', 'de', 'es'] as $locale) {
            $value = trans("user::password_recovery.{$key}", [], $locale);
            Assert::assertNotSame("user::password_recovery.{$key}", $value, "Manca {$locale}/{$key}.");
            Assert::assertNotSame($italian, $value, "{$locale}/{$key} e' uguale all'italiano.");
        }
    }
});
