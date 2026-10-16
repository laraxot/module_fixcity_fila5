<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

/*
 * Footer FO: <x-section slug="footer"> → Section::getBlocksBySlug('footer') → sections/footer.json.
 * Brand/slogan, colonne, contatti e bottom links vivono nel JSON; Blade e' solo il vestito (D11 / DCP-198 A5).
 */
it('keeps FixCity brand slogan and real footer urls in every footer.json copy', function (): void {
    $relative = 'database/content/sections/footer.json';
    $roots = [
        dirname(__DIR__, 4).'/config/local/fixcity',
        dirname(__DIR__, 4).'/config/eu/fixcity',
        dirname(__DIR__, 2).'/config',
    ];

    foreach ($roots as $root) {
        $path = $root.'/'.$relative;
        Assert::assertFileExists($path, $path);
        /** @var array{blocks: array<string, list<array{data: array<string, mixed>}>>} $json */
        $json = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        foreach (['it', 'en', 'de', 'es'] as $locale) {
            $data = $json['blocks'][$locale][0]['data'] ?? null;
            if (! is_array($data)) {
                throw new LogicException($path.' '.$locale.' data must be an array');
            }
            /** @var array<string, mixed> $data */

            $brand = $data['brand'] ?? [];
            if (! is_array($brand)) {
                throw new LogicException($path.' '.$locale.' brand must be an array');
            }
            /** @var array<string, mixed> $brand */
            Assert::assertSame('FixCity', $brand['name'] ?? null, $path.' '.$locale.' brand.name');
            Assert::assertIsString($brand['description'] ?? null);
            Assert::assertNotSame('', trim((string) ($brand['description'] ?? '')), $path.' '.$locale.' brand.description (slogan)');
            Assert::assertStringNotContainsString('Nome del Comune', (string) ($brand['name'] ?? ''));

            $urls = [];
            $collect = static function (mixed $links) use (&$urls): void {
                if (! is_array($links)) {
                    return;
                }
                foreach ($links as $item) {
                    if (is_array($item) && is_string($item['url'] ?? null) && $item['url'] !== '') {
                        $urls[] = $item['url'];
                    }
                }
            };
            $columns = $data['columns'] ?? [];
            foreach (is_array($columns) ? $columns : [] as $column) {
                if (is_array($column)) {
                    $collect($column['links'] ?? []);
                }
            }
            $contact = $data['contact'] ?? [];
            $collect(is_array($contact) ? ($contact['links'] ?? []) : []);
            $collect($data['legal_links'] ?? []);
            $collect($data['footer_bottom_links'] ?? []);
            $groups = $data['groups'] ?? [];
            foreach (['administration', 'services', 'news', 'living'] as $group) {
                $groupData = is_array($groups) ? ($groups[$group] ?? []) : [];
                $collect(is_array($groupData) ? ($groupData['links'] ?? []) : []);
            }

            Assert::assertContains('/tickets', $urls);
            Assert::assertContains('/tickets/create', $urls);
            Assert::assertContains('/tickets/track', $urls);
            Assert::assertContains('/domande-frequenti', $urls);
            Assert::assertNotContains('/amministrazione', $urls);
            Assert::assertNotContains('/anagrafe', $urls);
            Assert::assertSame([], $data['social'] ?? null, $path.' '.$locale.' social must be empty until ente provides URLs');
            $euLogo = $data['eu_logo'] ?? [];
            $euLogoSource = is_array($euLogo) ? ($euLogo['src'] ?? '') : '';
            Assert::assertSame('', is_string($euLogoSource) ? $euLogoSource : '', $path.' '.$locale.' eu_logo.src empty (D198-4)');
        }
    }
});

it('does not emit bare anchors or invent comune copy in footer vestiti', function (): void {
    $base = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/components/sections/footer';
    $dc = file_get_contents($base.'/design-comuni.blade.php');
    $v1 = file_get_contents($base.'/v1.blade.php');

    Assert::assertStringContainsString('FrontofficeUrl::fromStoredUrl', $dc);
    Assert::assertStringContainsString('FrontofficeUrl::fromStoredUrl', $v1);
    Assert::assertStringContainsString('$activeLinks', $dc);
    Assert::assertStringNotContainsString('Nome del Comune', $dc);
    Assert::assertStringNotContainsString('Nome del Comune', $v1);
    Assert::assertStringNotContainsString('Servizi e informazioni del Comune.', $v1);
    Assert::assertStringNotContainsString('https://twitter.com', $dc);
});
