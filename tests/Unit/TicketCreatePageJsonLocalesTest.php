<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

/*
 * DCP-55 difetto 2: `tickets.create.json` aveva blocchi solo per it ed en, quindi in de ed es "Avanti", "Indietro", "Invia", il titolo,
 * il breadcrumb, l'avviso e il blocco "Contatta il comune" restavano in italiano. Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);
$tenantJson = $laravelRoot.'/config/local/fixcity/database/content/pages/tickets.create.json';
$moduleJson = $laravelRoot.'/Modules/Fixcity/config/database/content/pages/tickets.create.json';

dataset('locales', ['it', 'en', 'de', 'es']);

/**
 * @return array<mixed>
 */
function ticketCreateBlock(string $file, string $locale): array
{
    $page = json_decode(file_get_contents($file), true);
    Assert::assertIsArray($page);
    $title = $page['title'] ?? null;
    $blocks = $page['content_blocks'] ?? null;
    $sidebar = $page['sidebar_blocks'] ?? null;
    Assert::assertIsArray($title);
    Assert::assertIsArray($blocks);
    Assert::assertIsArray($sidebar);
    Assert::assertNotEmpty($title[$locale] ?? '', "Titolo mancante in {$locale}.");
    Assert::assertArrayHasKey($locale, $sidebar, "sidebar_blocks senza {$locale}.");
    $localeBlocks = $blocks[$locale] ?? null;
    Assert::assertIsArray($localeBlocks, "content_blocks senza {$locale}.");
    $first = $localeBlocks[0] ?? null;
    Assert::assertIsArray($first);
    $data = $first['data'] ?? null;
    Assert::assertIsArray($data);

    return $data;
}

/**
 * @param  array<mixed>  $tree
 * @return array<string, string>
 */
function ticketCreateTexts(array $tree, string $prefix = ''): array
{
    $texts = [];
    foreach ($tree as $key => $value) {
        $path = $prefix.$key;
        if (is_array($value)) {
            $texts += ticketCreateTexts($value, $path.'.');
        } elseif (is_string($value) && in_array(basename(str_replace('.', '/', $path)), ['label', 'title', 'back', 'next', 'next_step', 'current', 'text', 'emphasis', 'aria_label'], true)) {
            $texts[$path] = $value;
        }
    }

    return $texts;
}

it('keeps the tenant and module copies of tickets.create.json identical', function () use ($tenantJson, $moduleJson): void {
    Assert::assertFileEquals($tenantJson, $moduleJson);
});

it('has a complete block for every language', function (string $locale) use ($tenantJson): void {
    $data = ticketCreateBlock($tenantJson, $locale);

    foreach (['nav.back', 'nav.next', 'nav.next_step', 'breadcrumb.current', 'callout.text', 'contacts.main.title'] as $path) {
        Assert::assertNotEmpty(data_get($data, $path), "{$path} mancante in {$locale}.");
    }
    $items = data_get($data, 'contacts.main.items');
    Assert::assertIsArray($items);
    Assert::assertCount(4, $items, "Il blocco contatti di {$locale} deve avere 4 voci.");
    Assert::assertSame("/{$locale}", data_get($data, 'breadcrumb.items.0.url'));
    Assert::assertSame("/{$locale}/tickets", data_get($data, 'breadcrumb.items.1.url'));
    Assert::assertSame("/{$locale}/domande-frequenti", data_get($data, 'contacts.main.items.0.url'));
})->with('locales');

it('translates the visible texts instead of repeating the Italian ones', function (string $locale) use ($tenantJson): void {
    if ($locale === 'it') {
        $this->expectNotToPerformAssertions();

        return;
    }
    $italian = ticketCreateTexts(ticketCreateBlock($tenantJson, 'it'));
    $translated = ticketCreateTexts(ticketCreateBlock($tenantJson, $locale));
    // "Step" (nav.aria_label) e "Home" sono gia' uguali in en: omografi veri solo per en.
    $sameWord = $locale === 'en' ? ['nav.aria_label', 'breadcrumb.items.0.label'] : [];
    $untranslated = [];
    foreach ($italian as $path => $text) {
        if (in_array($path, $sameWord, true)) {
            continue;
        }
        if (($translated[$path] ?? $text) === $text) {
            $untranslated[] = $path;
        }
    }

    Assert::assertSame([], $untranslated, "Testi di tickets.create.json ancora in italiano in {$locale}.");
})->with('locales');
