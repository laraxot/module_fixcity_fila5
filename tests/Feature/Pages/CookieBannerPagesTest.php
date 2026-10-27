<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use Modules\Gdpr\Actions\Cookie\GetCookieInventoryAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-153 (continua DCP-136): banner dei cookie, cookie policy e guardia sui cookie impostati.
 * Il comportamento nel browser (clic, tastiera, scadenza del cookie, axe) e' in Themes/Sixteen/tests/browser/cookie-banner.spec.mjs.
 */
it('injects the cookie bar of the Gdpr module on public pages, in every language', function (string $locale): void {
    $html = (string) $this->get("/{$locale}")->assertOk()->getContent();
    $cookieKey = config('cookie-consent.cookie_key');
    Assert::assertIsString($cookieKey);

    Assert::assertSame(1, substr_count($html, 'class="gdpr-cookiebar"'), 'La barra deve comparire una volta sola');
    Assert::assertStringContainsString('data-cookiebar="info"', $html, 'Con soli cookie tecnici il banner informa, non chiede consenso');
    Assert::assertStringContainsString('data-cookie-key="'.$cookieKey.'"', $html);
    Assert::assertStringContainsString('data-cookie-days="180"', $html);
    Assert::assertStringNotContainsString('lcc-modal', $html, 'La vista di statikbe non deve comparire: quella del modulo Gdpr la sostituisce');
    Assert::assertStringNotContainsString('js-lcc-', $html);
    Assert::assertStringContainsString("/{$locale}/cookie-policy", $html);
})->with(['it', 'en', 'de', 'es']);

it('shows the technical-only bar with a single acknowledgement and no pre-ticked box', function (): void {
    $html = (string) $this->get('/it')->assertOk()->getContent();

    Assert::assertSame(1, substr_count($html, 'data-cookie-action="reject"'), 'Solo "Ho capito"');
    Assert::assertStringNotContainsString('data-cookie-action="accept"', $html);
    Assert::assertDoesNotMatchRegularExpression('/data-cookie-category="(?:analytics|marketing)"[^>]*\bchecked\b/', $html);
    Assert::assertMatchesRegularExpression('/data-cookie-category="analytics"[^>]*\bdisabled\b/', $html, 'Categoria non usata: spenta e disattivata');
});

it('asks for consent with equal accept and reject only when a non technical cookie is declared', function (): void {
    config(['gdpr.config.cookie.extra' => [['name' => '_demo', 'category' => 'analytics', 'purpose' => 'Misura le visite', 'duration' => '30 giorni']]]);
    app()->setLocale('it');
    $html = view('cookie-consent::index')->render();

    Assert::assertStringContainsString('data-cookiebar="consent"', $html);
    foreach (['accept', 'reject', 'settings'] as $action) {
        Assert::assertStringContainsString('data-cookie-action="'.$action.'"', $html);
    }
    // la X di chiusura equivale a rifiutare, con una etichetta che lo dice
    Assert::assertMatchesRegularExpression('/data-cookie-action="reject" aria-label="Chiudi senza accettare"/', $html);
    Assert::assertDoesNotMatchRegularExpression('/data-cookie-category="analytics"[^>]*\bchecked\b/', $html);
    Assert::assertDoesNotMatchRegularExpression('/data-cookie-category="analytics"[^>]*\bdisabled\b/', $html, 'La categoria in uso si puo\' attivare');
    Assert::assertMatchesRegularExpression('/data-cookie-category="marketing"[^>]*\bdisabled\b/', $html, 'La categoria non usata resta disattivata');
});

it('publishes the cookie policy page with exactly the cookies of the inventory', function (string $locale): void {
    app()->setLocale($locale);
    $html = (string) $this->get("/{$locale}/cookie-policy")->assertOk()->getContent();

    Assert::assertStringContainsString('data-element="cookie-inventory"', $html);
    foreach (app(GetCookieInventoryAction::class)->execute()->cookies as $cookie) {
        Assert::assertStringContainsString('<code>'.e($cookie->name).'</code>', $html, "{$cookie->name} manca dalla tabella ({$locale})");
    }
    Assert::assertStringContainsString('data-element="cookie-preferences-open"', $html);
})->with(['it', 'en', 'de', 'es']);

it('links the cookie policy and the preferences from the footer without a dead anchor', function (): void {
    $html = (string) $this->get('/it')->assertOk()->getContent();

    Assert::assertMatchesRegularExpression('#<a href="[^"]*/it/cookie-policy"[^>]*>Cookie policy</a>#', $html);
    Assert::assertMatchesRegularExpression('#<a href="[^"]*/it/cookie-policy" data-cookie-preferences[^>]*>Gestisci cookie</a>#', $html);
});

/*
 * Guardia di DCP-136: ogni cookie che il server imposta a un ospite deve essere nell'inventario (quindi nella cookie policy). Un cookie
 * nuovo, per esempio uno strumento di analisi aggiunto senza pensarci, fa fallire il test finche' non viene dichiarato.
 */
it('sets no cookie that the inventory does not declare', function (string $path): void {
    $declared = array_map(static fn ($cookie): string => $cookie->name, app(GetCookieInventoryAction::class)->execute()->cookies);
    $response = $this->get($path)->assertOk();

    $set = array_map(static fn ($cookie): string => $cookie->getName(), $response->headers->getCookies());
    // il debugbar e' solo di sviluppo (DEBUGBAR_ENABLED=false in produzione, lo controlla production-checklist.sh)
    $undeclared = array_values(array_diff($set, $declared, ['PHPDEBUGBAR_STACK_DATA']));
    Assert::assertSame([], $undeclared, 'Cookie impostati ma non dichiarati in GetCookieInventoryAction / gdpr.config.cookie.extra');
})->with(['/it', '/en', '/it/tickets', '/it/tickets/track', '/it/auth/login', '/it/cookie-policy', '/it/privacy']);
