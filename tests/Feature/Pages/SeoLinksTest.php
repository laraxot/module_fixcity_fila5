<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\parse_url;
use function Safe\preg_match;
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

/*
 * Cosa segnala il report Sitemap di Search Console (DCP-272): «La Sitemap contiene URL bloccati dal file robots.txt»
 * e «URL non seguiti» (un URL della sitemap che risponde con un reindirizzamento).
 */

/** @return list<string> URL della sitemap, nella forma assoluta in cui li legge Google */
function sitemapLocs(TestCase $test): array
{
    $xml = simplexml_load_string((string) $test->get('/sitemap.xml')->assertOk()->getContent());
    $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    return array_values(array_map('strval', $xml->xpath('//s:url/s:loc') ?: []));
}

/**
 * Percorsi `Disallow:` del gruppo `User-agent: *` di public_html/robots.txt (nel monorepo sta accanto a laravel/).
 *
 * @return list<string>
 */
function robotsDisallowedPaths(TestCase $test): array
{
    $robots = dirname(base_path()).'/public_html/robots.txt';
    if (! is_file($robots)) {
        $test->markTestSkipped('public_html/robots.txt non c\'e\' in questo checkout.');
    }

    $paths = [];
    foreach (explode("\n", file_get_contents($robots) ?: '') as $line) {
        if (preg_match('/^Disallow:\s*(\S+)/i', $line, $match) === 1 && isset($match[1])) {
            $paths[] = $match[1];
        }
    }

    return $paths;
}

it('lists no url that robots.txt blocks', function (): void {
    /** @var TestCase $this */
    // `Allow: /` e' sempre piu' corto di ogni Disallow, quindi basta confrontare i Disallow (prefisso, `*` jolly).
    $patterns = array_map(
        static fn (string $path): string => '#^'.str_replace('\*', '.*', preg_quote($path, '#')).'#',
        robotsDisallowedPaths($this),
    );

    $blocked = array_values(array_filter(
        sitemapLocs($this),
        static fn (string $loc): bool => array_filter($patterns, static fn (string $rx): bool => preg_match($rx, (string) (parse_url($loc, PHP_URL_PATH) ?? '')) === 1) !== [],
    ));

    expect($blocked)->toBe([]);
});

it('closes the same private and development areas in every language', function (): void {
    /** @var TestCase $this */
    $disallowed = robotsDisallowedPaths($this);
    $italian = array_values(array_map(
        static fn (string $path): string => substr($path, 3),
        array_filter($disallowed, static fn (string $path): bool => str_starts_with($path, '/it/')),
    ));

    $missing = [];
    foreach (['en', 'de', 'es'] as $lang) {
        foreach ($italian as $suffix) {
            if (! in_array('/'.$lang.$suffix, $disallowed, true)) {
                $missing[] = '/'.$lang.$suffix;
            }
        }
    }

    expect($missing)->toBe([]);
});

it('lists only final urls, never one that redirects', function (): void {
    /** @var TestCase $this */
    $redirecting = [];
    foreach (sitemapLocs($this) as $loc) {
        if (! str_starts_with((string) (parse_url($loc, PHP_URL_PATH) ?? ''), '/it')) {
            continue;
        }
        if ($this->get($loc)->isRedirection()) {
            $redirecting[] = $loc;
        }
    }

    expect($redirecting)->toBe([]);
});
