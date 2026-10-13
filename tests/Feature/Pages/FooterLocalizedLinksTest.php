<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/**
 * G-03 (DCP-54): nel footer del sito reale gli URL interni erano scritti senza prefisso di lingua (`/tickets/create`) e
 * rispondevano 404 anche quando la pagina con il prefisso esiste (`/it/tickets/create`). Ogni link interno del footer deve
 * portare alla lingua della pagina in cui si trova il visitatore.
 */
it('prefixes the internal footer links with the current language', function (string $locale): void {
    $response = $this->get("/{$locale}");
    $response->assertOk();

    $footer = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR)->querySelector('footer');
    Assert::assertNotNull($footer, 'Footer assente.');

    $internal = 0;
    foreach ($footer->querySelectorAll('a[href]') as $link) {
        $href = (string) $link->getAttribute('href');
        $host = parse_url($href, PHP_URL_HOST);
        $scheme = parse_url($href, PHP_URL_SCHEME);
        $path = parse_url($href, PHP_URL_PATH);

        if (is_string($host) && $host !== parse_url(config()->string('app.url'), PHP_URL_HOST) && $host !== 'localhost') {
            continue; // link esterno
        }
        if (is_string($scheme) && ! in_array($scheme, ['http', 'https'], true)) {
            continue; // mailto:, tel:
        }

        $path = is_string($path) ? $path : '';
        if ($path === '' || $path === '#') {
            continue;
        }
        $internal++;
        Assert::assertMatchesRegularExpression(
            '#^/'.preg_quote($locale, '#').'(/|$)#',
            $path,
            "Link del footer senza prefisso '/{$locale}': {$href}",
        );
    }

    Assert::assertGreaterThan(3, $internal, 'Nel footer non sono stati trovati link interni da verificare.');
})->with(['it', 'en', 'de', 'es']);
