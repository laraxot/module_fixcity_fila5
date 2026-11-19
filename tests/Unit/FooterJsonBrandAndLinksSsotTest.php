<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

uses(TestCase::class);

/*
 * Footer FO: <x-section slug="footer"> → Section::getBlocksBySlug('footer') → sections/footer.json.
 * Brand/slogan, colonne, contatti e bottom links vivono nel JSON; Blade e' solo il vestito (D11 / DCP-198 A5).
 */
it('keeps FixCity brand slogan and real footer urls in every footer.json copy', function (): void {
    $relative = 'database/content/sections/footer.json';
    $roots = [
        dirname(__DIR__, 4).'/config/local/fixcity',
    ];

    foreach ($roots as $root) {
        $path = $root.'/'.$relative;
        Assert::assertFileExists($path, $path);
        /** @var array{blocks: array<string, list<array{data: array<string, mixed>}>>} $json */
        $json = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $headerPath = $root.'/database/content/sections/header.json';
        Assert::assertFileExists($headerPath, $headerPath);
        /** @var array{blocks: array<string, list<array{data: array<string, mixed>}>>} $headerJson */
        $headerJson = json_decode(file_get_contents($headerPath), true, 512, JSON_THROW_ON_ERROR);

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
            Assert::assertSame(
                data_get($headerJson, 'blocks.'.$locale.'.0.data.brand.logo'),
                $brand['logo'] ?? null,
                $path.' '.$locale.' must reuse the header brand logo'
            );

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

/*
 * Behaviour: footer vestito usa brand-mark (come header), niente img/it-pa.
 * Module: Fixcity FO chrome. Existing: questo file + HeaderJsonBrandAndNavSsotTest.
 * Istruzione utente: «nel footer il logo … deve essere lo stesso dell'header».
 */
it('does not emit bare anchors or invent comune copy in footer vestiti', function (): void {
    $base = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/components/sections/footer';
    $v1 = file_get_contents($base.'/v1.blade.php');

    Assert::assertStringContainsString('FrontofficeUrl::fromStoredUrl', $v1);
    Assert::assertStringNotContainsString('Nome del Comune', $v1);
    Assert::assertStringNotContainsString('Servizi e informazioni del Comune.', $v1);
    Assert::assertStringNotContainsString('https://twitter.com', $v1);
});

it('renders header and footer brand mark from the same shared partial', function (): void {
    $base = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/components/sections';
    $header = file_get_contents($base.'/header/v1.blade.php');
    $footer = file_get_contents($base.'/footer/v1.blade.php');
    $partial = file_get_contents($base.'/partials/brand-mark.blade.php');

    Assert::assertStringContainsString('components.sections.partials.brand-mark', $header);
    Assert::assertStringContainsString('FrontofficeUrl::fromStoredUrl', $footer);
    Assert::assertStringContainsString('fc-brand-mark', $partial);
    Assert::assertStringContainsString('xlink:href', $partial);
    Assert::assertStringNotContainsString("\$icon('it-pa')", $header);
});
