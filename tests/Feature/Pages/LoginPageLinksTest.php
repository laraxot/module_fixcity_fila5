<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/*
 * DCP-186 (scheda del front office, gruppo C): `tickets/create` richiede il login (D17), quindi il login e' la porta d'ingresso di chi non ha
 * ancora un account. La pagina nascondeva «Registrati» dietro `Route::has('register')`: nessuna rotta con quel nome esiste (le pagine sono
 * dinamiche), quindi il link non compariva mai e chi veniva mandato al login non trovava ne' la registrazione ne' il recupero password.
 */
it('offers registration and password recovery from the login page', function (string $locale): void {
    $response = $this->get("/{$locale}/auth/login")->assertOk();
    $document = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);

    $paths = [];
    foreach ($document->querySelectorAll('main a[href]') as $link) {
        $path = parse_url((string) $link->getAttribute('href'), PHP_URL_PATH);
        $paths[] = is_string($path) ? $path : '';
    }

    // `password.request` e' la rotta che invia il link di recupero; `/auth/password/forgot` cadeva su `[token]` (form di nuova password).
    foreach (["/{$locale}/auth/register", "/{$locale}/auth/password/reset"] as $expected) {
        Assert::assertContains($expected, $paths, "Login ({$locale}) senza il link a {$expected}.");
        $this->get($expected)->assertOk();
    }
})->with(['it', 'en', 'de', 'es']);
