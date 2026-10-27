<?php

declare(strict_types=1);

use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use Modules\Fixcity\Tests\TestCase;
use Modules\Gdpr\Actions\Cookie\GetCookieInventoryAction;
use Modules\Gdpr\Enums\CookieCategory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-153: l'inventario e' l'unica fonte del banner e della cookie policy. Oggi il sito imposta solo cookie tecnici: il banner informa.
 * Un modulo che aggiunge un cookie analitico o di marketing lo dichiara in `gdpr.config.cookie.extra` e il banner passa al consenso.
 */
it('lists only technical cookies by default and does not ask for consent', function (): void {
    config(['gdpr.config.cookie.extra' => []]);
    $inventory = app(GetCookieInventoryAction::class)->execute();

    $names = array_map(static fn ($cookie): string => $cookie->name, $inventory->cookies);
    $sessionName = config('session.cookie');
    $consentName = config('cookie-consent.cookie_key');
    Assert::assertIsString($sessionName);
    Assert::assertIsString($consentName);
    Assert::assertContains($sessionName, $names);
    Assert::assertContains('XSRF-TOKEN', $names);
    Assert::assertContains($consentName, $names);
    Assert::assertSame([], $inventory->optionalCategories());
    Assert::assertFalse($inventory->requiresConsent());
    foreach ($inventory->cookies as $cookie) {
        Assert::assertSame(CookieCategory::Necessary, $cookie->category, "{$cookie->name} deve essere tecnico finche' non e' dichiarato in gdpr.config.cookie.extra");
        Assert::assertNotSame('', $cookie->purpose);
        Assert::assertNotSame('', $cookie->duration);
    }
});

it('switches to consent as soon as a non technical cookie is declared', function (): void {
    config(['gdpr.config.cookie.extra' => [['name' => '_demo', 'category' => 'analytics', 'purpose' => 'Misura le visite', 'duration' => '30 giorni']]]);
    $inventory = app(GetCookieInventoryAction::class)->execute();

    Assert::assertTrue($inventory->requiresConsent());
    Assert::assertSame([CookieCategory::Analytics], $inventory->optionalCategories());
    Assert::assertTrue($inventory->has(CookieCategory::Analytics));
    Assert::assertFalse($inventory->has(CookieCategory::Marketing));
});

it('keeps the consent for 6 months, the earliest the Garante allows to ask again', function (): void {
    Assert::assertSame(180, config('gdpr.config.cookie.consent_lifetime'));
});

/*
 * "Ricordami" sul login imposta `remember_web_<hash>` per 400 giorni (Laravel, 576000 minuti): e' tecnico, ma la cookie policy lo
 * ometteva. Il nome si ricava dalla guardia, non si scrive a mano.
 */
it('lists the remember-me cookie of the web guard with its real name and duration', function (): void {
    config(['gdpr.config.cookie.extra' => []]);
    app()->setLocale('it');
    $guard = Auth::guard('web');
    Assert::assertInstanceOf(SessionGuard::class, $guard);

    $remember = collect(app(GetCookieInventoryAction::class)->execute()->cookies)->first(static fn ($cookie): bool => $cookie->name === $guard->getRecallerName());

    Assert::assertNotNull($remember);
    Assert::assertSame(CookieCategory::Necessary, $remember->category);
    Assert::assertStringContainsString('400', $remember->duration);
});

it('describes the consent cookie with the configured lifetime', function (): void {
    config(['gdpr.config.cookie.consent_lifetime' => 180]);
    app()->setLocale('it');
    $consent = collect(app(GetCookieInventoryAction::class)->execute()->cookies)->first(static fn ($cookie): bool => $cookie->name === config('cookie-consent.cookie_key'));

    Assert::assertNotNull($consent);
    Assert::assertStringContainsString('180', $consent->duration);
});
