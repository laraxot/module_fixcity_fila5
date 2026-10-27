<?php

declare(strict_types=1);

use Dom\Element;
use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-143: difetti ARIA dei blocchi del tema trovati da axe-core (wcag2a/aa, wcag21a/aa, 375 e 1440 px) su 82 pagine CMS.
 * Qui si controlla la STRUTTURA nell'HTML renderizzato, senza browser, cosi' che un blocco nuovo o ritoccato non la rompa:
 *  - un tablist contiene solo `li[role=presentation] > a[role=tab]` (o tab diretti), mai `li[role=tab]` con un link dentro (nested-interactive);
 *  - ogni tab punta con aria-controls a un tabpanel che esiste (aria-valid-attr-value) e `aria-selected` sta solo su un tab (aria-allowed-attr);
 *  - una barra role=progressbar ha un nome accessibile (aria-progressbar-name).
 * Il test "e' il contrario di axe": non sostituisce la misura nel browser (a11y-axe.spec.mjs), la tiene al riparo tra una misura e l'altra.
 */
dataset('theme pages', [
    '/it', '/it/tickets', '/it/tests', '/it/tests/servizi-index', '/it/tests/segnalazioni-elenco', '/it/tests/permessi-scheda-servizio',
    '/it/tests/pagamenti-imu-scheda-servizio', '/it/tests/graduatoria-scheda-servizio', '/it/tests/novita-dettaglio', '/it/tests/evento-dettaglio',
]);

function ariaPage(TestCase $test, string $path): HTMLDocument
{
    $response = $test->get($path);
    $response->assertOk();
    $html = $response->getContent();
    Assert::assertIsString($html);

    return HTMLDocument::createFromString($html, LIBXML_NOERROR);
}

it('builds tab lists with presentation items and tab links that control an existing tab panel', function (string $path): void {
    /** @var TestCase $this */
    $document = ariaPage($this, $path);

    foreach ($document->querySelectorAll('[role="tablist"]') as $tablist) {
        $tabs = $tablist->querySelectorAll('[role="tab"]');
        Assert::assertGreaterThan(0, $tabs->length, "{$path}: un tablist senza nessun role=tab (una lista di link di navigazione non e' un tablist).");
    }
    Assert::assertNull($document->querySelector('li[role="tab"]'), "{$path}: li[role=tab] contiene un link: controllo interattivo annidato.");
    Assert::assertNull($document->querySelector('a[role="button"][aria-selected]'), "{$path}: aria-selected su un link con role=button non e' ammesso.");

    foreach ($document->querySelectorAll('[role="tab"]') as $tab) {
        $panelId = trim($tab->getAttribute('aria-controls') ?? '');
        $panel = $panelId === '' ? null : $document->getElementById($panelId);
        Assert::assertInstanceOf(Element::class, $panel, "{$path}: il tab \"".trim($tab->textContent ?? '').'" ha aria-controls="'.$panelId.'" che non esiste.');
        Assert::assertSame('tabpanel', $panel->getAttribute('role'), "{$path}: #{$panelId} non e' un tabpanel.");
    }
})->with('theme pages');

it('gives every progress bar an accessible name', function (string $path): void {
    /** @var TestCase $this */
    $document = ariaPage($this, $path);

    foreach ($document->querySelectorAll('[role="progressbar"]') as $bar) {
        $name = trim($bar->getAttribute('aria-label') ?? '').trim($bar->getAttribute('aria-labelledby') ?? '');
        Assert::assertNotSame('', $name, "{$path}: role=progressbar senza aria-label ne' aria-labelledby (classe \"".$bar->className.'").');
    }
})->with('theme pages');

it('translates the progress bar name in the four languages', function (): void {
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        $translator = app('translator');
        Assert::assertTrue($translator->hasForLocale('pub_theme::a11y.reading_progress', $locale), "Manca pub_theme::a11y.reading_progress in {$locale}.");
        if ($locale !== 'it') {
            Assert::assertNotSame($translator->get('pub_theme::a11y.reading_progress', [], 'it', false), $translator->get('pub_theme::a11y.reading_progress', [], $locale, false), "pub_theme::a11y.reading_progress in {$locale} e' ancora il testo italiano.");
        }
    }
});
