<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_encode;

/**
 * DCP-104: `tickets.index.json` esiste in tre copie (tenant locale, tenant eu, modulo) che devono restare identiche.
 * La pagina e' PUBBLICA per scelta (trasparenza sulle segnalazioni prese in carico): nessun `middleware`, a differenza di `tickets.create`.
 */
$root = dirname(__DIR__, 4);
$copies = [
    'config/local' => $root.'/config/local/fixcity/database/content/pages/tickets.index.json',
    'config/eu' => $root.'/config/eu/fixcity/database/content/pages/tickets.index.json',
    'modulo Fixcity' => $root.'/Modules/Fixcity/config/database/content/pages/tickets.index.json',
];

test('the three copies of tickets.index.json are identical', function () use ($copies): void {
    $hashes = array_map(static fn (string $path): string => md5(file_get_contents($path)), $copies);

    Assert::assertCount(1, array_unique($hashes), 'copie diverse: '.json_encode($hashes));
});

test('tickets.index.json is a public Design Comuni page served by the ticket-list block', function () use ($copies): void {
    foreach ($copies as $path) {
        /** @var array<string, mixed> $page */
        $page = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        Assert::assertSame('tickets.index', $page['slug']);
        Assert::assertSame('design-comuni', $page['layout']);
        Assert::assertArrayNotHasKey('middleware', $page, 'l\'elenco e\' pubblico per scelta');
        foreach (['it', 'en'] as $locale) {
            Assert::assertSame('pub_theme::components.blocks.ticket-list.design-comuni', data_get($page, "content_blocks.$locale.0.data.view"));
            Assert::assertSame('all-services', data_get($page, "content_blocks.$locale.0.data.header_active"));
        }
    }
});
