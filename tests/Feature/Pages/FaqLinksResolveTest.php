<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/*
 * DCP-186 (scheda del front office, gruppo D): la FAQ e' la pagina a cui il cittadino chiede aiuto, quindi ogni suo link deve portare da
 * qualche parte. «Apri il servizio di segnalazione» puntava a /services/report-issue (404 in 4 lingue) e il test del browser controllava
 * solo l'attributo href, fissando il difetto. Come per il footer (FooterLinksResolveTest), qui si seguono davvero i link nel `<main>`.
 */
it('has faq links that all resolve in the visitor language', function (string $locale): void {
    $response = $this->get("/{$locale}/domande-frequenti")->assertOk();
    $main = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR)->querySelector('main');
    Assert::assertNotNull($main, 'La FAQ non ha un <main>.');

    $followed = 0;
    foreach ($main->querySelectorAll('a[href]') as $link) {
        $href = (string) $link->getAttribute('href');
        Assert::assertNotContains($href, ['', '#'], "FAQ ({$locale}): link morto «{$link->textContent}».");

        $scheme = parse_url($href, PHP_URL_SCHEME);
        $host = parse_url($href, PHP_URL_HOST);
        if ((is_string($scheme) && ! in_array($scheme, ['http', 'https'], true)) || (is_string($host) && $host !== parse_url(config()->string('app.url'), PHP_URL_HOST) && $host !== 'localhost' && $host !== '127.0.0.1')) {
            continue; // mailto:, tel: o sito esterno
        }

        $path = parse_url($href, PHP_URL_PATH);
        $path = is_string($path) ? $path : '';
        $status = $this->get($path)->getStatusCode();
        // /tickets/create richiede il login: da ospite e' un reindirizzamento, non un 404
        Assert::assertContains($status, [200, 302], "FAQ ({$locale}): «{$link->textContent}» → {$path} risponde {$status}.");
        $followed++;
    }

    Assert::assertGreaterThanOrEqual(4, $followed, 'La FAQ deve avere almeno i 4 link di navigazione: la guardia passerebbe a vuoto.');
})->with(['it', 'en', 'de', 'es']);
