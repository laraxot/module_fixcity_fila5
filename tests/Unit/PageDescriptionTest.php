<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Themes\Sixteen\Presentation\Metadata\PageDescription;

use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\json_encode;

uses(TestCase::class);

/**
 * DCP-147: la descrizione di una pagina CMS puo' essere un oggetto per lingua nel JSON; `Page::description` non e' traducibile e la restituisce come
 * testo grezzo, che finiva nel `<meta name="description">` come JSON (`/it/tickets/confirmation`).
 */
describe('PageDescription::resolve', function (): void {
    test('legge la lingua corrente da una descrizione per lingua', function (): void {
        $raw = '{"it": "Elenco", "en": "List", "de": "Liste"}';

        Assert::assertSame('List', PageDescription::resolve($raw, 'en'));
        Assert::assertSame('Liste', PageDescription::resolve($raw, 'de'));
    });

    test('ricade sull\'italiano e poi sulla prima voce non vuota', function (): void {
        Assert::assertSame('Elenco', PageDescription::resolve('{"it": "Elenco", "en": "List"}', 'es'));
        Assert::assertSame('List', PageDescription::resolve('{"it": "", "en": "List"}', 'es'));
        Assert::assertSame('', PageDescription::resolve('{"it": "", "en": " "}', 'es'));
    });

    test('lascia com\'e\' una stringa semplice e non inventa nulla per i valori assenti', function (): void {
        Assert::assertSame('Testo semplice', PageDescription::resolve(' Testo semplice ', 'it'));
        Assert::assertSame('', PageDescription::resolve(null, 'it'));
        Assert::assertSame('', PageDescription::resolve('', 'it'));
        Assert::assertSame('{non json', PageDescription::resolve('{non json', 'it'));
    });
});

describe('descrizioni delle pagine pubbliche', function (): void {
    test('tickets.index ha una descrizione non vuota in tutte le lingue', function (): void {
        $root = dirname(__DIR__, 4);
        $page = json_decode(file_get_contents($root.'/config/local/fixcity/database/content/pages/tickets/index.json'), true);
        Assert::assertIsArray($page);
        $raw = json_encode($page['description'] ?? null);

        foreach (['it', 'en', 'de', 'es'] as $locale) {
            Assert::assertNotSame('', PageDescription::resolve($raw, $locale), 'descrizione vuota in '.$locale);
        }
    });

    test('la homepage espone titolo e descrizione localizzati per ogni lingua pubblica', function (): void {
        $root = dirname(__DIR__, 4);
        $page = json_decode(file_get_contents($root.'/config/local/fixcity/database/content/pages/home.json'), true);
        Assert::assertIsArray($page);

        foreach (['it', 'en', 'de', 'es'] as $locale) {
            Assert::assertNotSame('', PageDescription::resolve(json_encode($page['title'] ?? null), $locale));
            Assert::assertNotSame('', PageDescription::resolve(json_encode($page['description'] ?? null), $locale));
        }
    });
});
