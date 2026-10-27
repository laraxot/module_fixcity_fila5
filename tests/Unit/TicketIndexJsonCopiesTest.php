<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_encode;

uses(TestCase::class);

/**
 * DCP-104: `tickets.index.json` esiste in tre copie (tenant locale, tenant eu, modulo) che devono restare identiche.
 * La pagina e' PUBBLICA per scelta (trasparenza sulle segnalazioni prese in carico): nessun `middleware`, a differenza di `tickets.create`.
 */
$root = dirname(__DIR__, 4);
$copies = [
    'config/local' => $root.'/config/local/fixcity/database/content/pages/tickets/index.json',
    'modulo Fixcity' => $root.'/Modules/Fixcity/config/database/content/pages/tickets.index.json',
];

test('the canonical tickets.index.json exists', function () use ($copies): void {
    foreach ($copies as $path) {
        Assert::assertFileExists($path);
    }
});

test('tickets.index.json is a public Design Comuni page served by the ticket-list block', function () use ($copies): void {
    foreach ($copies as $path) {
        /** @var array<string, mixed> $page */
        $page = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        Assert::assertContains($page['slug'] ?? null, ['index', 'tickets.index']);
        Assert::assertSame('design-comuni', $page['layout']);
        Assert::assertArrayNotHasKey('middleware', $page, 'l\'elenco e\' pubblico per scelta');
        foreach (['it', 'en'] as $locale) {
            Assert::assertSame('pub_theme::components.blocks.ticket-list.design-comuni', data_get($page, "content_blocks.$locale.0.data.view"));
            Assert::assertSame('all-services', data_get($page, "content_blocks.$locale.0.data.header_active"));
        }
    }
});
