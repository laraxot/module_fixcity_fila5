<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Filament\Widgets\Auth\LoginWidget;

uses(TestCase::class);

/**
 * G-07 (DCP-54): il login chiamava `Auth::attempt` senza alcun limite, quindi una password si poteva provare all'infinito
 * (forza bruta). Dopo 5 tentativi sbagliati per la stessa email e lo stesso IP il login si ferma per un minuto, anche con la
 * password giusta, e dice quanto aspettare; un accesso riuscito azzera il contatore.
 */
beforeEach(function (): void {
    RateLimiter::clear('test-reset');
    app()->setLocale('it');
});

/**
 * @return Testable<LoginWidget>
 */
function loginAttempt(string $email, string $password): Testable
{
    return Livewire::test(LoginWidget::class)
        ->set('data.email', $email)
        ->set('data.password', $password)
        ->call('login');
}

it('blocks the login after five wrong passwords, even with the right one', function (): void {
    $user = UserFactory::new()->createOne(['password' => Hash::make('Password-giusta-123')]);
    $email = (string) $user->email;

    foreach (range(1, 5) as $i) {
        loginAttempt($email, 'sbagliata-'.$i)->assertHasErrors('data.email');
    }

    $blocked = loginAttempt($email, 'Password-giusta-123');

    $blocked->assertHasErrors('data.email');
    expect(Auth::check())->toBeFalse('Il sesto tentativo, con la password giusta, ha fatto accedere.');
    expect(trans('user::login.actions.login.throttle', ['seconds' => 1]))->not->toBe('user::login.actions.login.throttle');
});

it('lets the user in with the right password before the limit and clears the counter', function (): void {
    $user = UserFactory::new()->createOne(['password' => Hash::make('Password-giusta-123')]);
    $email = (string) $user->email;

    foreach (range(1, 3) as $i) {
        loginAttempt($email, 'sbagliata-'.$i);
    }
    loginAttempt($email, 'Password-giusta-123');
    expect(Auth::check())->toBeTrue();

    Auth::logout();
    foreach (range(1, 4) as $i) {
        loginAttempt($email, 'sbagliata-di-nuovo-'.$i);
    }
    loginAttempt($email, 'Password-giusta-123');
    expect(Auth::check())->toBeTrue('Il contatore non e\' stato azzerato dopo l\'accesso riuscito.');
});

it('keeps the limit per email so that another account is not locked out', function (): void {
    $locked = UserFactory::new()->createOne(['password' => Hash::make('Password-giusta-123')]);
    $other = UserFactory::new()->createOne(['password' => Hash::make('Altra-password-456')]);

    foreach (range(1, 6) as $i) {
        loginAttempt((string) $locked->email, 'sbagliata-'.$i);
    }
    loginAttempt((string) $other->email, 'Altra-password-456');

    expect(Auth::check())->toBeTrue();
    expect((string) Auth::user()?->email)->toBe((string) $other->email);
});
