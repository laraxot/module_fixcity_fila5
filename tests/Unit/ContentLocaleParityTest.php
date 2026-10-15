<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\glob;
use function Safe\json_decode;
use function Safe\json_encode;

/*
 * DCP-120: parita' multilingua dei contenuti del chrome e della home.
 *
 * `fallback_locale` e' `it`: un blocco mancante in en/de/es NON da' errore, mostra l'italiano sotto `<html lang="de">`
 * (WCAG 3.1.1/3.1.2). Qui la mancanza, un URL diverso, un testo rimasto italiano o una copia modulo/tenant divergente fanno fallire
 * il test. Le lingue servite si leggono da config/laravellocalization.php: aggiungerne una fa fallire il test finche' i contenuti non ci sono.
 * Solo filesystem, nessun database. Glossario e decisioni: Themes/Sixteen/docs/i18n/01-glossario.md.
 */
$laravelRoot = dirname(__DIR__, 4);

/** @var array<string, string> sottopercorso => chiave dei blocchi */
$contentParityTargets = [
    'sections/header.json' => 'blocks',
    'sections/footer.json' => 'blocks',
    'sections/search-modal.json' => 'blocks',
    'pages/home.json' => 'content_blocks',
];

/**
 * Testi identici all'italiano per natura, e non per dimenticanza: marchi, codici lingua, omografi. Chiave '*' = tutte le lingue.
 *
 * @var array<string, list<string>>
 */
$contentParityIdenticalAllowed = [
    '*' => ['Twitter', 'Facebook', 'YouTube', 'Telegram', 'Whatsapp', 'WhatsApp', 'RSS', 'ITA', 'ENG', 'DEU', 'SPA'],
    // "Cookie policy" e' il termine inglese stesso (in italiano e' un prestito): de ed es sono tradotte, verificato in local ed eu il 2026-10-14.
    'en' => ['Media policy', 'Cookie policy'],
    'es' => ['Turismo'],
];

/**
 * Chiavi di sola presentazione che il blade valorizza con un default: il loro scarto tra lingue non e' un contenuto mancante.
 *
 * @var list<string>
 */
$contentParityPresentationKeys = ['step_numbers'];

/**
 * @return list<string>
 */
function contentParityServedLocales(string $laravelRoot): array
{
    $config = require $laravelRoot.'/config/laravellocalization.php';
    Assert::assertIsArray($config);
    $supported = $config['supportedLocales'] ?? null;
    Assert::assertIsArray($supported);

    return array_values(array_map('strval', array_keys($supported)));
}

/**
 * @return array<string, mixed>
 */
function contentParityLoad(string $path): array
{
    $json = json_decode(file_get_contents($path), true);
    Assert::assertIsArray($json, $path.' must contain a json object.');

    /** @var array<string, mixed> $json */
    return $json;
}

/**
 * @param  array<array-key, mixed>  $node
 * @param  list<string>  $skipKeys
 * @return array<string, scalar|null>
 */
function contentParityFlatten(array $node, array $skipKeys, string $path = ''): array
{
    $flat = [];
    foreach ($node as $key => $value) {
        if (in_array((string) $key, $skipKeys, true)) {
            continue;
        }
        $here = $path.'.'.$key;
        if (is_array($value)) {
            $flat += contentParityFlatten($value, $skipKeys, $here);
        } elseif (is_scalar($value) || $value === null) {
            $flat[$here] = $value;
        }
    }

    return $flat;
}

/** Un valore e' "testo" se e' una stringa che un cittadino legge: non un URL, una vista, un'icona o una chiave di traduzione. */
function contentParityIsText(string $path, mixed $value): bool
{
    if (! is_string($value) || $value === '') {
        return false;
    }
    $leaf = substr($path, (int) strrpos($path, '.') + 1);
    if (in_array($leaf, ['url', 'view', 'icon', 'logo', 'element', 'locale', 'data_element', 'src', 'office_url', 'email', 'phone', 'height', 'type', 'slug', 'class', 'style'], true) || str_ends_with($leaf, '_url')) {
        return false;
    }

    return ! str_starts_with($value, 'http') && ! str_starts_with($value, '/') && ! str_starts_with($value, '#')
        && ! str_starts_with($value, 'themes/') && ! str_starts_with($value, 'pub_theme::') && ! str_starts_with($value, '{{');
}

/**
 * @param  array<string, mixed>  $json
 * @return array<array-key, mixed>
 */
function contentParityBlocks(array $json, string $key, string $locale): array
{
    $all = $json[$key] ?? null;
    Assert::assertIsArray($all, 'Missing "'.$key.'".');
    $blocks = $all[$locale] ?? null;
    Assert::assertIsArray($blocks, 'Missing "'.$key.'.'.$locale.'" block set: the site would fall back to Italian under lang="'.$locale.'".');

    return $blocks;
}

it('has a block set, a name and a sidebar for every served locale', function () use ($laravelRoot, $contentParityTargets): void {
    $locales = contentParityServedLocales($laravelRoot);
    Assert::assertContains('it', $locales);
    $problems = [];
    foreach ($contentParityTargets as $sub => $key) {
        $json = contentParityLoad($laravelRoot.'/config/local/fixcity/database/content/'.$sub);
        foreach (['name', 'title', 'description', 'sidebar_blocks', $key] as $field) {
            if (! array_key_exists($field, $json) || ! is_array($json[$field])) {
                continue;
            }
            foreach ($locales as $locale) {
                if (! array_key_exists($locale, $json[$field])) {
                    $problems[] = $sub.': "'.$field.'" has no "'.$locale.'"';
                }
            }
        }
    }
    Assert::assertSame([], $problems, implode("\n", $problems));
});

it('keeps the same structure, urls, views and icons in every served locale', function () use ($laravelRoot, $contentParityTargets, $contentParityPresentationKeys): void {
    $locales = contentParityServedLocales($laravelRoot);
    $problems = [];
    foreach ($contentParityTargets as $sub => $key) {
        $json = contentParityLoad($laravelRoot.'/config/local/fixcity/database/content/'.$sub);
        $italian = contentParityFlatten(contentParityBlocks($json, $key, 'it'), $contentParityPresentationKeys);
        foreach ($locales as $locale) {
            $other = contentParityFlatten(contentParityBlocks($json, $key, $locale), $contentParityPresentationKeys);
            foreach (array_diff_key($italian, $other) as $path => $unused) {
                $problems[] = $sub.' ['.$locale.']: missing '.$path;
            }
            foreach (array_diff_key($other, $italian) as $path => $unused) {
                $problems[] = $sub.' ['.$locale.']: extra '.$path;
            }
            foreach ($italian as $path => $value) {
                if (array_key_exists($path, $other) && ! contentParityIsText($path, $value) && $other[$path] !== $value) {
                    $problems[] = $sub.' ['.$locale.']: '.$path.' differs from it ('.json_encode($other[$path]).' vs '.json_encode($value).')';
                }
            }
        }
    }
    Assert::assertSame([], $problems, "\n".implode("\n", $problems));
});

it('has no text identical to the Italian one outside the documented list', function () use ($laravelRoot, $contentParityTargets, $contentParityPresentationKeys, $contentParityIdenticalAllowed): void {
    $locales = array_values(array_diff(contentParityServedLocales($laravelRoot), ['it']));
    $problems = [];
    foreach ($contentParityTargets as $sub => $key) {
        $json = contentParityLoad($laravelRoot.'/config/local/fixcity/database/content/'.$sub);
        $italian = contentParityFlatten(contentParityBlocks($json, $key, 'it'), $contentParityPresentationKeys);
        foreach ($locales as $locale) {
            $allowed = array_merge($contentParityIdenticalAllowed['*'], $contentParityIdenticalAllowed[$locale] ?? []);
            foreach (contentParityFlatten(contentParityBlocks($json, $key, $locale), $contentParityPresentationKeys) as $path => $value) {
                if (contentParityIsText($path, $value) && ($italian[$path] ?? null) === $value && ! in_array($value, $allowed, true)) {
                    $problems[] = $sub.' ['.$locale.']: '.$path.' is still Italian: '.json_encode($value);
                }
            }
        }
    }
    Assert::assertSame([], $problems, "\n".implode("\n", $problems));
});

it('offers every served locale in the language selector of every header block', function () use ($laravelRoot): void {
    $served = contentParityServedLocales($laravelRoot);
    sort($served);
    $json = contentParityLoad($laravelRoot.'/config/local/fixcity/database/content/sections/header.json');
    foreach ($served as $locale) {
        $first = contentParityBlocks($json, 'blocks', $locale)[0] ?? null;
        Assert::assertIsArray($first);
        $items = data_get($first, 'data.languages.items');
        Assert::assertIsArray($items, 'header ['.$locale.']: languages.items missing.');
        $offered = array_map(static fn (mixed $item): string => is_array($item) && is_string($item['locale'] ?? null) ? $item['locale'] : '', $items);
        sort($offered);
        Assert::assertSame($served, $offered, 'header ['.$locale.']: the selector must list exactly the served locales.');
    }
});

it('keeps the tenant and module copies of these contents byte-identical', function () use ($laravelRoot, $contentParityTargets): void {
    $tenantDirs = glob($laravelRoot.'/config/local/*/database/content');
    Assert::assertNotSame([], $tenantDirs, 'No tenant content directory found: the guard would pass vacuously.');
    $problems = [];
    foreach ($tenantDirs as $tenantDir) {
        Assert::assertIsString($tenantDir);
        foreach (array_keys($contentParityTargets) as $sub) {
            $tenantFile = $tenantDir.'/'.$sub;
            $moduleFile = $laravelRoot.'/Modules/Fixcity/config/database/content/'.$sub;
            if (file_get_contents($tenantFile) !== file_get_contents($moduleFile)) {
                $problems[] = $sub.': '.$tenantFile.' differs from '.$moduleFile;
            }
        }
    }
    Assert::assertSame([], $problems, "\n".implode("\n", $problems));
});
