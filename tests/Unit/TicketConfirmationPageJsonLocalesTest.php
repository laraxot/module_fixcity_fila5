<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

uses(TestCase::class);

/*
 * DCP-146 (prova funzionale in /de): la pagina di conferma ("Segnalazione inviata", "Traccia la segnalazione", la valutazione a stelle,
 * "Contatta il comune") era tutta in italiano perche' `tickets.confirmation.json` aveva blocchi solo per it ed en.
 * Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);
$tenantJson = $laravelRoot.'/config/local/fixcity/database/content/pages/tickets.confirmation.json';
$moduleJson = $laravelRoot.'/Modules/Fixcity/config/database/content/pages/tickets.confirmation.json';

dataset('locales', ['it', 'en', 'de', 'es']);

/**
 * @return array<mixed>
 */
function confirmationBlock(string $file, string $locale): array
{
    $page = json_decode(file_get_contents($file), true);
    Assert::assertIsArray($page);
    foreach (['title', 'content_blocks', 'sidebar_blocks'] as $key) {
        Assert::assertIsArray($page[$key] ?? null);
        Assert::assertArrayHasKey($locale, $page[$key], "{$key} senza {$locale}.");
    }
    $localeBlocks = $page['content_blocks'][$locale] ?? null;
    Assert::assertIsArray($localeBlocks);
    $first = $localeBlocks[0] ?? null;
    Assert::assertIsArray($first);
    Assert::assertIsArray($first['data'] ?? null);

    return $first['data'];
}

/**
 * Testi leggibili del blocco: esclude vista, classi, icone, URL, segnaposto (":code") e identificativi.
 *
 * @param  array<mixed>  $tree
 * @return array<string, string>
 */
function confirmationTexts(array $tree, string $prefix = ''): array
{
    $technical = ['view', 'layout', 'class', 'col_class', 'icon', 'url', 'element', 'header_active', 'type'];
    $texts = [];
    foreach ($tree as $key => $value) {
        $path = $prefix.$key;
        if (is_array($value)) {
            $texts += confirmationTexts($value, $path.'.');
        } elseif (is_string($value) && $value !== '' && ! in_array((string) $key, $technical, true) && ! str_starts_with($value, ':')) {
            $texts[$path] = $value;
        }
    }

    return $texts;
}

it('keeps the tenant and module copies of tickets.confirmation.json identical', function () use ($tenantJson, $moduleJson): void {
    Assert::assertFileEquals($tenantJson, $moduleJson);
});

it('has a block for every language with the same shape and the language prefix in its links', function (string $locale) use ($tenantJson): void {
    $italian = confirmationTexts(confirmationBlock($tenantJson, 'it'));
    $block = confirmationBlock($tenantJson, $locale);

    Assert::assertSame(array_keys($italian), array_keys(confirmationTexts($block)), "Struttura diversa da it in {$locale}.");
    Assert::assertSame("/{$locale}", data_get($block, 'breadcrumb.items.0.url'));
    Assert::assertSame("/{$locale}/tickets", data_get($block, 'breadcrumb.items.1.url'));
    Assert::assertSame("/{$locale}/tickets", data_get($block, 'heading.lines.1.parts.1.link.url'));
    Assert::assertSame("/{$locale}/tickets", data_get($block, 'without_code.heading.lines.1.parts.1.link.url'));
    Assert::assertSame(data_get(confirmationBlock($tenantJson, 'it'), 'heading.button.url'), data_get($block, 'heading.button.url'));
})->with('locales');

it('translates the visible texts instead of repeating the Italian ones', function (string $locale) use ($tenantJson): void {
    if ($locale === 'it') {
        $this->expectNotToPerformAssertions();

        return;
    }
    $italian = confirmationTexts(confirmationBlock($tenantJson, 'it'));
    $translated = confirmationTexts(confirmationBlock($tenantJson, $locale));
    // "Home" si scrive uguale in italiano e in inglese (omografo vero).
    $sameWord = $locale === 'en' ? ['breadcrumb.items.0.label'] : [];
    $untranslated = [];
    foreach ($italian as $path => $text) {
        if (! in_array($path, $sameWord, true) && ($translated[$path] ?? $text) === $text) {
            $untranslated[] = $path;
        }
    }

    Assert::assertSame([], $untranslated, "Testi di tickets.confirmation.json ancora in italiano in {$locale}.");
})->with('locales');
