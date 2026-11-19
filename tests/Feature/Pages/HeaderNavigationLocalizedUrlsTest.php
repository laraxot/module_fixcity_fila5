<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/*
 * DCP-198 A1/A2: la nav principale del header usa percorsi FixCity localizzati; nessun href="#" nei social renderizzati.
 */
it('localizes header main navigation links and omits hash social hrefs', function (string $locale): void {
    $response = $this->get("/{$locale}")->assertOk();

    $document = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);
    $header = $document->querySelector('.it-header-wrapper');
    Assert::assertNotNull($header, "Header assente ({$locale}).");

    foreach ($header->querySelectorAll('.it-socials a') as $link) {
        if (! $link->hasAttribute('href')) {
            continue;
        }

        $href = (string) $link->getAttribute('href');
        Assert::assertNotContains($href, ['', '#'], "Social header ({$locale}): href morto «{$href}».");
    }

    $mainNav = $header->querySelector('ul[data-element="main-navigation"]');
    Assert::assertNotNull($mainNav, "Nav principale assente ({$locale}).");

    $navLinks = $mainNav->querySelectorAll('a');
    Assert::assertGreaterThanOrEqual(4, $navLinks->length, "Nav principale incompleta ({$locale}).");

    $appHost = parse_url(config()->string('app.url'), PHP_URL_HOST);

    foreach ($navLinks as $link) {
        Assert::assertTrue($link->hasAttribute('href'), "Voce nav senza href ({$locale}).");
        $href = (string) $link->getAttribute('href');
        Assert::assertNotContains($href, ['', '#'], "Nav ({$locale}): href morto «{$link->textContent}» → «{$href}».");

        $host = parse_url($href, PHP_URL_HOST);
        if (is_string($host) && $host !== $appHost && $host !== 'localhost') {
            continue;
        }

        $path = parse_url($href, PHP_URL_PATH);
        $path = is_string($path) ? $path : '';
        Assert::assertStringStartsWith("/{$locale}/", $path, "Nav ({$locale}): «{$link->textContent}» senza prefisso lingua → {$path}.");

        $status = $this->get($path)->getStatusCode();
        Assert::assertContains($status, [200, 302], "Nav ({$locale}): «{$link->textContent}» → {$path} risponde {$status}.");
    }
})->with(['it', 'en', 'de', 'es']);

it('does not expose a hash-only region portal link in the slim header bar', function (string $locale): void {
    $response = $this->get("/{$locale}")->assertOk();

    $document = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);
    $regionLink = $document->querySelector('.it-header-slim-wrapper-content a.navbar-brand');
    if ($regionLink === null) {
        return;
    }

    $href = $regionLink->hasAttribute('href') ? (string) $regionLink->getAttribute('href') : '';
    Assert::assertNotContains($href, ['', '#'], "Link regione con href morto ({$locale}): «{$href}».");
})->with(['it', 'en', 'de', 'es']);
