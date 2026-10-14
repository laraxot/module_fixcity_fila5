<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/*
 * DCP-131: ogni voce del footer o porta a una pagina che esiste nella lingua del visitatore, o e' testo non attivo (`<a>` senza href:
 * l'ente non ha ancora fornito la pagina). Mai `href="#"`, mai un percorso che risponde 404. Il footer e' condiviso con le repliche
 * /it/tests/*: i target dei link cambiano, il markup no. FooterLocalizedLinksTest copre il prefisso di lingua.
 */
it('has footer links that all resolve in the visitor language and no dead link', function (string $locale): void {
    $response = $this->get("/{$locale}")->assertOk();
    $footer = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR)->querySelector('footer');
    Assert::assertNotNull($footer, 'Footer assente.');

    $resolved = [];
    $inactive = 0;
    foreach ($footer->querySelectorAll('a') as $link) {
        if (! $link->hasAttribute('href')) {
            $inactive++;

            continue;
        }

        $href = (string) $link->getAttribute('href');
        Assert::assertNotContains($href, ['', '#'], "Footer ({$locale}): link morto «{$link->textContent}» con href=\"{$href}\".");

        $host = parse_url($href, PHP_URL_HOST);
        $scheme = parse_url($href, PHP_URL_SCHEME);
        if ((is_string($scheme) && ! in_array($scheme, ['http', 'https'], true)) || (is_string($host) && $host !== parse_url(config()->string('app.url'), PHP_URL_HOST) && $host !== 'localhost')) {
            continue; // mailto:, tel: o sito esterno
        }

        $path = parse_url($href, PHP_URL_PATH);
        $path = is_string($path) ? $path : '';
        $status = $this->get($path)->getStatusCode();
        // /tickets/create richiede il login: da ospite e' un reindirizzamento, non un 404
        Assert::assertContains($status, [200, 302], "Footer ({$locale}): «{$link->textContent}» → {$path} risponde {$status}.");
        $resolved[$path] = $status;
    }

    foreach (['privacy', 'accessibilita', 'note-legali', 'media-policy', 'mappa-sito'] as $page) {
        Assert::assertArrayHasKey("/{$locale}/{$page}", $resolved, "Footer ({$locale}) senza il link a /{$locale}/{$page}.");
    }
    Assert::assertGreaterThan(0, $inactive, 'Le voci senza pagina fornita dall’ente devono restare come testo non attivo.');
})->with(['it', 'en', 'de', 'es']);

it('keeps the legal pages of the footer reachable in four languages', function (string $page, string $locale): void {
    $this->get("/{$locale}/{$page}")->assertOk()->assertSee('<h1', false);
})->with(['privacy', 'accessibilita', 'note-legali', 'media-policy'])->with(['it', 'en', 'de', 'es']);

it('has header links that resolve too')->todo('Header fuori dal perimetro di DCP-131: /regione, /amministrazione, /novita, /servizi, /eventi, /argomenti* rispondono 404 senza prefisso lingua e 12 voci hanno href="#" (agente dell’header).');
