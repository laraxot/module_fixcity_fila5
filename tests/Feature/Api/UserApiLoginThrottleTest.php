<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Actions\Auth\BuildApiLoginLimitAction;
use Modules\User\Filament\Widgets\Auth\LoginWidget;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

uses(TestCase::class);

/*
 * D-08 (DCP-282): `POST /api/user/login` verificava la password senza alcun limite (il freno G-07 stava solo nel widget), quindi
 * una password si poteva provare all'infinito dall'API. Ora la rotta ha `throttle:user-login`: 5 richieste per 60 secondi per la
 * stessa coppia email e IP, come il widget, con lo stesso messaggio. Ogni test usa un'email casuale: la chiave del limite comprende l'email.
 */
beforeEach(function (): void {
    app()->setLocale('it');
});

/**
 * @return TestResponse<Response>
 */
function apiLogin(TestCase $test, string $email): TestResponse
{
    return $test->postJson('/api/user/login', ['email' => $email, 'password' => 'password-sbagliata']);
}

function apiLoginTimes(TestCase $test, string $email, int $times): void
{
    for ($i = 0; $i < $times; $i++) {
        apiLogin($test, $email);
    }
}

function randomApiEmail(): string
{
    return Str::lower(Str::random(12)).'@example.test';
}

it('blocks the sixth login attempt of the same email and IP with a 429 and the widget message', function (): void {
    /** @var TestCase $this */
    $email = randomApiEmail();

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        Assert::assertNotSame(429, apiLogin($this, $email)->getStatusCode(), 'Il tentativo '.$attempt.' non doveva essere bloccato.');
    }

    $blocked = apiLogin($this, $email);
    $message = $blocked->json('message');

    $blocked->assertStatus(429);
    $blocked->assertJsonPath('success', false);
    Assert::assertIsString($message);
    Assert::assertStringContainsString('Troppi tentativi di accesso', $message);
    Assert::assertNotNull($blocked->headers->get('Retry-After'));
});

it('counts the same email in any letter case as one account', function (): void {
    /** @var TestCase $this */
    $email = randomApiEmail();

    apiLoginTimes($this, $email, 5);

    apiLogin($this, Str::upper($email))->assertStatus(429);
});

it('does not lock out another email when one is blocked', function (): void {
    /** @var TestCase $this */
    $locked = randomApiEmail();
    apiLoginTimes($this, $locked, 6);
    apiLogin($this, $locked)->assertStatus(429);

    Assert::assertNotSame(429, apiLogin($this, randomApiEmail())->getStatusCode());
});

it('does not fail with a server error when the email field is not a string', function (): void {
    /** @var TestCase $this */
    $response = $this->postJson('/api/user/login', ['email' => ['a', 'b'], 'password' => 'x']);

    Assert::assertLessThan(500, $response->getStatusCode());
});

it('uses the same thresholds as the login widget', function (): void {
    $widget = new ReflectionClass(LoginWidget::class);

    Assert::assertSame($widget->getConstant('MAX_ATTEMPTS'), BuildApiLoginLimitAction::MAX_ATTEMPTS);
    Assert::assertSame($widget->getConstant('DECAY_SECONDS'), BuildApiLoginLimitAction::DECAY_SECONDS);
});

it('attaches the user-login limiter to the api login route', function (): void {
    $route = Route::getRoutes()->getByName('api.login');

    Assert::assertNotNull($route);
    Assert::assertContains('throttle:user-login', $route->gatherMiddleware());
});
