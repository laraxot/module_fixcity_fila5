<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Modules\Fixcity\Tests\TestCase;
use Modules\Gdpr\Actions\Registration\HandleSuccessfulRegistrationAction;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Filament\Widgets\Auth\LoginWidget;
use Modules\User\Models\User;
use Modules\Xot\Filament\Widgets\XotBaseWidget;

uses(TestCase::class);

/*
 * Viaggio del cittadino (DCP-104): un ospite che clicca "Segnala disservizio" viene mandato al login; dopo l'accesso o la registrazione
 * deve tornare a quella pagina. Prima il login finiva sulla home e la registrazione su `route('dashboard')` (404 nel front office).
 */

it('returns to the page that required the login', function (): void {
    /** @var TestCase $this */
    $user = UserFactory::new()->createOne(['password' => Hash::make('password123')]);
    session(['url.intended' => url('/it/tickets/create')]);

    Livewire::test(LoginWidget::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password123')
        ->call('save')
        ->assertRedirect(url('/it/tickets/create'));
});

it('returns to the page named by the redirect parameter of the login link', function (): void {
    /** @var TestCase $this */
    $user = UserFactory::new()->createOne(['password' => Hash::make('password123')]);
    session()->forget('url.intended');

    Livewire::withQueryParams(['redirect' => '/it/blog/una-guida?page=2'])
        ->test(LoginWidget::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password123')
        ->call('save')
        ->assertRedirect(url('/it/blog/una-guida?page=2'));
});

it('ignores a redirect parameter that is not an internal path', function (string $target): void {
    /** @var TestCase $this */
    session()->forget('url.intended');

    Livewire::withQueryParams(['redirect' => $target])->test(LoginWidget::class);

    expect(session('url.intended'))->toBeNull();
})->with([
    'sito esterno' => ['https://evil.example/it/blog'],
    'schema implicito' => ['//evil.example/it/blog'],
    'backslash' => ['/\\evil.example'],
    'javascript' => ['javascript:alert(1)'],
    'a capo' => ["/it/blog\r\nLocation: https://evil.example"],
    'relativo' => ['it/blog'],
    'vuoto' => [''],
]);

it('goes to the home of the current language when no page was required', function (): void {
    /** @var TestCase $this */
    $user = UserFactory::new()->createOne(['password' => Hash::make('password123')]);
    session()->forget('url.intended');

    Livewire::test(LoginWidget::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password123')
        ->call('save')
        ->assertRedirect(url('/'.app()->getLocale().'/area-personale'));
});

it('sends a newly registered user back to the required page, not to a missing route', function (): void {
    /** @var TestCase $this */
    session(['url.intended' => url('/it/tickets/create')]);
    $user = UserFactory::new()->createOne();
    // widget minimo: registra dove la registrazione manda l'utente invece di rispondere al browser
    $widget = new class extends XotBaseWidget
    {
        public string $destination = '';

        public function redirectIntended(mixed $default = '/', mixed $navigate = false): void
        {
            $this->destination = is_string($default) ? $default : '';
        }
    };

    app(HandleSuccessfulRegistrationAction::class)->execute($user instanceof User ? $user : User::query()->findOrFail($user->getKey()), $widget);

    expect($widget->destination)->not->toBe('')
        ->and(session('url.intended'))->toBe(url('/it/tickets/create'));
});
