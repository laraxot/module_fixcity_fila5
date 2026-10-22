<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\json_encode;

uses(TestCase::class);

/*
 * Guardia di accesso (DCP-42, STORY-357): `/it/tickets/create` si apre solo da autenticati e lo dice il JSON CMS della pagina.
 * `PageSlugMiddleware` risolve ogni voce di `middleware` come nome di alias o classe: `web` darebbe 500 ("Target class [web] does not exist")
 * e `guest` bloccherebbe proprio gli utenti autenticati. Quindi la chiave deve essere esattamente ["auth"], in tutte le copie.
 * Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);

dataset('tickets create json copies', [
    'tenant local' => ['config/local/fixcity/database/content/pages/tickets/create.json'],
    'module' => ['Modules/Fixcity/config/database/content/pages/tickets.create.json'],
]);

it('requires authentication in every copy of tickets.create.json', function (string $relativePath) use ($laravelRoot): void {
    $json = json_decode(file_get_contents($laravelRoot.'/'.$relativePath), true);

    Assert::assertIsArray($json);
    Assert::assertSame(['auth'], $json['middleware'] ?? null, $relativePath.': middleware must be exactly ["auth"] (no "web", no "guest").');
})->with('tickets create json copies');

it('keeps the three copies of tickets.create.json identical', function () use ($laravelRoot): void {
    $copies = [
        'config/local/fixcity/database/content/pages/tickets/create.json',
        'Modules/Fixcity/config/database/content/pages/tickets.create.json',
    ];
    $hashes = array_unique(array_map(static function (string $path) use ($laravelRoot): string {
        $json = json_decode(file_get_contents($laravelRoot.'/'.$path), true);
        Assert::assertIsArray($json);
        unset($json['id'], $json['slug']);
        return md5(json_encode($json));
    }, $copies));

    Assert::assertCount(1, $hashes, 'The tenant, eu and module copies of tickets.create.json must be identical.');
});
