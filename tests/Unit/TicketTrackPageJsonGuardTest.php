<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

/*
 * Guardia del freno alla ricerca per codice (DCP-102, DCP-149): il codice di tracciamento e' un capability token e `/{lang}/tickets/track` e' servita dal
 * ramo `ticket-track` del tema, non dalla pagina Folio del modulo (che dichiarava `throttle:10,1` ma e' oscurata). Il limite sta quindi nel JSON CMS della
 * pagina, letto da `PageSlugMiddleware`. Se qualcuno toglie la chiave, la ricerca torna senza freni (14 risposte 200 di fila su 14).
 * Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);

dataset('tickets track json copies', [
    'tenant local' => ['config/local/fixcity/database/content/pages/tickets.track.json'],
    'tenant eu' => ['config/eu/fixcity/database/content/pages/tickets.track.json'],
    'module' => ['Modules/Fixcity/config/database/content/pages/tickets.track.json'],
]);

it('limits the tracking lookups to ten requests per minute in every copy of tickets.track.json', function (string $relativePath) use ($laravelRoot): void {
    $json = json_decode(file_get_contents($laravelRoot.'/'.$relativePath), true);

    Assert::assertIsArray($json);
    Assert::assertSame('tickets.track', $json['slug'] ?? null);
    Assert::assertSame(['throttle:10,1'], $json['middleware'] ?? null, $relativePath.': il freno del tracciamento deve essere esattamente ["throttle:10,1"].');
})->with('tickets track json copies');

it('keeps the three copies of tickets.track.json identical', function () use ($laravelRoot): void {
    $copies = [
        'config/local/fixcity/database/content/pages/tickets.track.json',
        'config/eu/fixcity/database/content/pages/tickets.track.json',
        'Modules/Fixcity/config/database/content/pages/tickets.track.json',
    ];
    $hashes = array_unique(array_map(static fn (string $path): string => md5(file_get_contents($laravelRoot.'/'.$path)), $copies));

    Assert::assertCount(1, $hashes, 'The tenant, eu and module copies of tickets.track.json must be identical.');
});

it('describes the page in all four languages', function () use ($laravelRoot): void {
    $json = json_decode(file_get_contents($laravelRoot.'/config/local/fixcity/database/content/pages/tickets.track.json'), true);

    Assert::assertIsArray($json);
    foreach (['title', 'description'] as $key) {
        $perLocale = $json[$key] ?? null;
        Assert::assertIsArray($perLocale, $key.' deve essere un oggetto per lingua');
        foreach (['it', 'en', 'de', 'es'] as $locale) {
            $text = $perLocale[$locale] ?? null;
            Assert::assertIsString($text, $key.' mancante in '.$locale);
            Assert::assertNotSame('', trim($text), $key.' vuoto in '.$locale);
        }
    }
});
