<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\glob;
use function Safe\json_decode;

/*
 * DCP-92: la voce di menu attiva dell'header Design Comuni e' un dato del primo blocco di ogni pagina (`header_active`, valore = `data-element`
 * della voce). Un valore fuori elenco non attiverebbe nessuna voce in silenzio. Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);

it('uses only known menu elements as header_active in page json', function () use ($laravelRoot): void {
    $allowed = ['all-services', 'news', 'management', 'live'];
    $files = glob($laravelRoot.'/{config/*/*,Modules/*/config}/database/content/pages/{tests.,tickets.}*.json', GLOB_BRACE);
    Assert::assertNotSame([], $files, 'No page json found: the guard would pass vacuously.');

    $checked = 0;
    foreach ($files as $file) {
        if (! is_string($file)) {
            continue;
        }
        $json = json_decode(file_get_contents($file), true);
        if (! is_array($json) || ! is_array($json['content_blocks'] ?? null)) {
            continue;
        }
        foreach ($json['content_blocks'] as $language => $blocks) {
            $first = is_array($blocks) ? ($blocks[0] ?? null) : null;
            $data = is_array($first) ? ($first['data'] ?? null) : null;
            $value = is_array($data) ? ($data['header_active'] ?? null) : null;
            if ($value === null) {
                continue;
            }
            $checked++;
            Assert::assertContains($value, $allowed, basename($file).' ('.$language.'): header_active must be one of '.implode(', ', $allowed));
        }
    }

    Assert::assertGreaterThan(0, $checked, 'No header_active found in any page json.');
});
