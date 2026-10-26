<?php

declare(strict_types=1);

use Dom\Element;
use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_replace;

uses(TestCase::class);

/*
 * DCP-290 (FO-5-26): "Accedi con Google" compariva sempre (la guardia `Route::has('oauth.redirect')` e' sempre vera, come
 * `config()->has('services.google')` in DCP-271) e in registrazione usciva senza stile (gli stili stavano sotto la card del login):
 * 510x183 px contro 510x44 del login. Ora compare solo con il provider configurato e porta con se' la propria resa.
 */

/**
 * @return array<string, array<string, string|null>>
 */
function dcp290GoogleSettings(bool $configured): array
{
    return ['services.google' => $configured
        ? ['client_id' => 'test-client.apps.googleusercontent.com', 'client_secret' => 'test-secret', 'redirect' => 'http://localhost/it/auth/login/google/callback']
        : ['client_id' => null, 'client_secret' => null, 'redirect' => null]];
}

function dcp290GoogleWrapper(TestCase $test, string $path): ?Element
{
    $document = HTMLDocument::createFromString((string) $test->get($path)->assertOk()->getContent(), LIBXML_NOERROR);

    return $document->querySelector('main .google-login-wrapper');
}

it('does not show the Google button when the provider has no keys', function (string $path): void {
    /** @var TestCase $this */
    config(dcp290GoogleSettings(false));
    $html = (string) $this->get($path)->assertOk()->getContent();

    Assert::assertNull(dcp290GoogleWrapper($this, $path), "Pulsante Google visibile senza chiavi su {$path}.");
    Assert::assertStringNotContainsString('/auth/login/google', $html);
})->with(['/it/auth/login', '/it/auth/register', '/en/auth/login', '/de/auth/register', '/es/auth/login']);

it('shows the very same Google button in login and registration when the provider is configured', function (string $locale): void {
    /** @var TestCase $this */
    config(dcp290GoogleSettings(true));

    $login = dcp290GoogleWrapper($this, "/{$locale}/auth/login");
    $register = dcp290GoogleWrapper($this, "/{$locale}/auth/register");
    Assert::assertNotNull($login, 'Pulsante assente nel login.');
    Assert::assertNotNull($register, 'Pulsante assente nella registrazione.');

    $normalise = static fn (Element $element): string => preg_replace('/\s+/', ' ', trim($element->innerHTML));
    Assert::assertSame($normalise($login), $normalise($register), 'Il pulsante deve essere lo stesso componente nelle due pagine.');
    Assert::assertStringContainsString("/{$locale}/auth/login/google", $normalise($login));

    // La resa non dipende dalla card che lo contiene: lo stile arriva con il componente (una sola volta per pagina).
    foreach (["/{$locale}/auth/login", "/{$locale}/auth/register"] as $path) {
        $page = (string) $this->get($path)->assertOk()->getContent();
        Assert::assertSame(1, substr_count($page, 'data-google-button-style'), "Stile del pulsante assente o duplicato su {$path}.");
    }
})->with(['it', 'en', 'de', 'es']);
