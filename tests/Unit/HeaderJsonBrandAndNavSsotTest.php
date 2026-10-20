<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

uses(TestCase::class);

/*
 * Header FO: <x-section slug="header"> → Section::getBlocksBySlug('header') → sections/header.json.
 * Slogan (brand.tagline), nome e link vivono nel JSON; Blade e' solo il vestito.
 */
it('keeps FixCity brand slogan and real nav urls in every header.json copy', function (): void {
    $relative = 'database/content/sections/header.json';
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
                Assert::fail($path.' '.$locale.' data must be an array');
            }

            $brand = $data['brand'] ?? [];
            if (! is_array($brand)) {
                Assert::fail($path.' '.$locale.' brand must be an array');
            }
            $brandName = $brand['name'] ?? null;
            $brandTagline = $brand['tagline'] ?? null;
            Assert::assertSame('FixCity', $brandName, $path.' '.$locale.' brand.name');
            Assert::assertIsString($brandTagline);
            Assert::assertNotSame('', trim($brandTagline), $path.' '.$locale.' brand.tagline (slogan)');
            Assert::assertStringNotContainsString('Nome del Comune', $brandName);
            Assert::assertStringNotContainsString('Un comune da vivere', $brandTagline);

            $account = $data['account'] ?? [];
            if (! is_array($account)) {
                Assert::fail($path.' '.$locale.' account must be an array');
            }
            Assert::assertSame('/auth/login', $account['url'] ?? null);

            $navigation = $data['navigation'] ?? [];
            if (! is_array($navigation)) {
                Assert::fail($path.' '.$locale.' navigation must be an array');
            }
            $urls = [];
            foreach ($navigation as $item) {
                if (is_array($item) && is_string($item['url'] ?? null)) {
                    $urls[] = $item['url'];
                }
            }
            Assert::assertContains('/tickets', $urls);
            Assert::assertContains('/tickets/create', $urls);
            Assert::assertContains('/tickets/track', $urls);
            Assert::assertNotContains('/amministrazione', $urls);
            Assert::assertSame([], $data['social_links'] ?? null);
        }
    }
});

it('does not hardcode follow_us or search labels in the tailwind header vestito', function (): void {
    $blade = file_get_contents(dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/components/sections/header/tailwind.blade.php');
    Assert::assertStringContainsString("\$d('follow_us')", $blade);
    Assert::assertStringContainsString("\$d('search.label')", $blade);
    Assert::assertStringNotContainsString('>Seguici su</span>', $blade);
    Assert::assertStringNotContainsString('aria-label="Cerca nel sito"', $blade);
    Assert::assertStringNotContainsString("'Nome della Regione'", $blade);
});
