<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

/*
 * Guardia delle pagine JSON dell'area personale (DCP-110, DCP-106): `area-personale.pratiche` e `area-personale.seguite` si aprono
 * solo da autenticati. `PageSlugMiddleware` risolve ogni voce di `middleware` come alias o classe: `web` darebbe 500 e `guest`
 * bloccherebbe proprio gli utenti autenticati, quindi la chiave deve essere esattamente ["auth"] in tutte le copie.
 * Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);
$locales = ['it', 'en', 'de', 'es'];

dataset('personal area json copies', [
    'practices tenant local' => ['config/local/fixcity/database/content/pages/area-personale.pratiche.json', 'area-personale.pratiche', 'personal-area-practices.design-comuni'],
    'practices module' => ['Modules/Fixcity/config/database/content/pages/area-personale.pratiche.json', 'area-personale.pratiche', 'personal-area-practices.design-comuni'],
    'followed tenant local' => ['config/local/fixcity/database/content/pages/area-personale.seguite.json', 'area-personale.seguite', 'personal-area-practices.followed'],
    'followed module' => ['Modules/Fixcity/config/database/content/pages/area-personale.seguite.json', 'area-personale.seguite', 'personal-area-practices.followed'],
]);

it('requires authentication and the Design Comuni layout, with matching id and slug', function (string $relativePath, string $slug) use ($laravelRoot): void {
    $json = json_decode(file_get_contents($laravelRoot.'/'.$relativePath), true);

    Assert::assertIsArray($json);
    Assert::assertSame(['auth'], $json['middleware'] ?? null, $relativePath.': middleware must be exactly ["auth"] (no "web", no "guest").');
    Assert::assertSame('design-comuni', $json['layout'] ?? null, $relativePath.': layout');
    Assert::assertSame($slug, $json['id'] ?? null, $relativePath.': id');
    Assert::assertSame($slug, $json['slug'] ?? null, $relativePath.': slug');
    Assert::assertSame($slug.'.json', basename($relativePath), 'The file name must be the slug.');
})->with('personal area json copies');

it('titles the page in every served language and uses one adapter block per language', function (string $relativePath, string $slug, string $view) use ($laravelRoot, $locales): void {
    $json = json_decode(file_get_contents($laravelRoot.'/'.$relativePath), true);

    Assert::assertIsArray($json);
    foreach ($locales as $locale) {
        $title = data_get($json, 'title.'.$locale);
        Assert::assertIsString($title, $relativePath.': title.'.$locale);
        Assert::assertNotSame('', trim($title), $relativePath.': title.'.$locale);

        $blocks = data_get($json, 'content_blocks.'.$locale);
        Assert::assertIsArray($blocks, $relativePath.': content_blocks.'.$locale);
        Assert::assertCount(1, $blocks, $relativePath.': exactly one adapter block in '.$locale);
        Assert::assertSame('pub_theme::components.blocks.'.$view, data_get($blocks, '0.data.view'), $relativePath.': view in '.$locale);
        $blockData = data_get($blocks, '0.data');
        Assert::assertIsArray($blockData);
        Assert::assertSame(['view'], array_keys($blockData), $relativePath.': texts live in lang files, not in the block data.');
    }
    Assert::assertFileExists($laravelRoot.'/Themes/Sixteen/resources/views/components/blocks/'.str_replace('.', '/', $view).'.blade.php');
})->with('personal area json copies');

it('keeps the tenant and module copies of each personal area page identical', function () use ($laravelRoot): void {
    foreach (['area-personale.pratiche.json', 'area-personale.seguite.json'] as $file) {
        $copies = [
            'config/local/fixcity/database/content/pages/'.$file,
            'Modules/Fixcity/config/database/content/pages/'.$file,
        ];
        $hashes = array_unique(array_map(static fn (string $path): string => md5(file_get_contents($laravelRoot.'/'.$path)), $copies));

        Assert::assertCount(1, $hashes, 'The tenant and module copies of '.$file.' must be identical.');
    }
});
