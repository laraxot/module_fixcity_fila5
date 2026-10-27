<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/*
 * DCP-55 difetto 5: il logo dell'header (e quello del menu mobile) puntava a `url('/')`; `/` rimanda sempre a `/it`,
 * quindi da /en, /de o /es un clic sul logo cambiava lingua. Deve restare nella lingua della pagina.
 */
dataset('locales', ['it', 'en', 'de', 'es']);

it('keeps the language when the logo is clicked', function (string $locale): void {
    /** @var TestCase $this */
    $response = $this->get("/{$locale}");
    $response->assertOk();

    $document = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);
    $links = $document->querySelectorAll('.it-brand-wrapper > a, a.logo-hamburger');
    Assert::assertGreaterThanOrEqual(2, $links->length, 'Mancano il logo dell\'header o quello del menu mobile.');

    foreach ($links as $link) {
        $href = (string) $link->getAttribute('href');
        Assert::assertSame("/{$locale}", parse_url($href, PHP_URL_PATH), "Il logo deve portare alla home della lingua corrente (href: '{$href}').");
    }
})->with('locales');
