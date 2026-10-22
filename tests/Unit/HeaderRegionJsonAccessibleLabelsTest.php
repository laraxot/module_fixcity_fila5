<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Themes\Sixteen\Presentation\Accessibility\RegionPortalLinkLabels;

use function Safe\file_get_contents;
use function Safe\json_decode;

uses(TestCase::class);

require_once dirname(__DIR__, 4).'/Themes/Sixteen/app/Presentation/Accessibility/RegionPortalLinkLabels.php';

/*
 * DCP-198 A10: i template `region.title` / `region.aria_label` in header.json usano graffe CMS; il front office deve risolverle.
 */
it('replaces brace placeholders in region portal labels with the region name', function (): void {
    $resolved = RegionPortalLinkLabels::resolve([
        'name' => 'Toscana',
        'title' => 'Vai al portale {Nome della Regione}',
        'aria_label' => 'Vai al portale {Nome della Regione} - link esterno - apertura nuova scheda',
    ]);

    Assert::assertSame('Vai al portale Toscana', $resolved['title']);
    Assert::assertSame('Vai al portale Toscana - link esterno - apertura nuova scheda', $resolved['aria_label']);
});

it('resolves header.json region accessible labels without literal braces for every locale', function (): void {
    $path = dirname(__DIR__, 4).'/config/local/fixcity/database/content/sections/header.json';
    $config = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    Assert::assertIsArray($config);

    $blocks = $config['blocks'] ?? null;
    Assert::assertIsArray($blocks);

    foreach (['it', 'en', 'de', 'es'] as $locale) {
        Assert::assertArrayHasKey($locale, $blocks, "Blocchi header mancanti per {$locale}.");
        $localeBlocks = $blocks[$locale];
        Assert::assertIsArray($localeBlocks);
        $firstBlock = $localeBlocks[0] ?? null;
        Assert::assertIsArray($firstBlock);
        $data = $firstBlock['data'] ?? null;
        Assert::assertIsArray($data);
        $first = $data['region'] ?? null;
        Assert::assertIsArray($first, "region mancante in header.json ({$locale}).");

        $resolved = RegionPortalLinkLabels::resolve([
            'name' => $first['name'] ?? null,
            'title' => $first['title'] ?? null,
            'aria_label' => $first['aria_label'] ?? null,
        ]);
        foreach (['title', 'aria_label'] as $key) {
            $value = $resolved[$key];
            if (! is_string($value)) {
                continue;
            }
            Assert::assertStringNotContainsString('{', $value, "{$locale}.{$key} contiene graffe: {$value}");
            Assert::assertStringNotContainsString('}', $value, "{$locale}.{$key} contiene graffe: {$value}");
        }
    }
});
