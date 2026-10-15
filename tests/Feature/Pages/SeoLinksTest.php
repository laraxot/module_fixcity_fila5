<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;

use function Safe\preg_match_all;
use function Safe\simplexml_load_string;

uses(TestCase::class);

/*
 * Linee guida di design 4.3.9 (DCP-166): canonical e hreflang su ogni pagina, sitemap.xml con tutte le lingue.
 * Il canonical non porta mai la query: `?code=` e' il codice di tracciamento di una segnalazione.
 */

it('declares canonical and hreflang with the four languages and x-default', function (string $lang): void {
    /** @var TestCase $this */
    $html = (string) $this->get("/{$lang}/privacy")->assertOk()->getContent();

    expect($html)->toContain('<link rel="canonical" href="'.url("/{$lang}/privacy").'">');
    foreach (['it', 'en', 'de', 'es'] as $alternate) {
        expect($html)->toContain('hreflang="'.$alternate.'" href="'.url("/{$alternate}/privacy").'"');
    }
    expect($html)->toContain('hreflang="x-default" href="'.url('/it/privacy').'"');
})->with(['it', 'en', 'de', 'es']);

it('never puts the tracking code in the canonical url', function (): void {
    /** @var TestCase $this */
    $html = (string) $this->get('/it/tickets/track?code=ABC123SECRET')->getContent();

    expect($html)->toContain('<link rel="canonical" href="'.url('/it/tickets/track').'">');
    preg_match_all('/<link rel="(?:canonical|alternate)"[^>]*>/', $html, $links);
    expect(implode('', $links[0]))->not->toContain('ABC123SECRET');
});

it('serves a valid sitemap with the public pages in every language and nothing private', function (): void {
    /** @var TestCase $this */
    $response = $this->get('/sitemap.xml')->assertOk();
    expect((string) $response->headers->get('Content-Type'))->toContain('application/xml');

    $xml = simplexml_load_string((string) $response->getContent());
    $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $locs = array_map('strval', $xml->xpath('//s:url/s:loc') ?: []);

    foreach (['it', 'en', 'de', 'es'] as $lang) {
        expect($locs)->toContain(url("/{$lang}"))->toContain(url("/{$lang}/privacy"))->toContain(url("/{$lang}/tickets/track"));
    }
    foreach ($locs as $loc) {
        expect($loc)->not->toMatch('#/(tests|auth|area-personale|admin)(/|$)|confirmation|segnalazione-dettaglio|\?#');
    }
});
