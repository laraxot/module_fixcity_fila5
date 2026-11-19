<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/**
 * DCP-126: la pagina catch-all `[container0]/[slug0]` rispondeva 200 con `<main>` vuoto a qualunque percorso a due segmenti
 * (soft-404: i motori di ricerca lo indicizzano e il cittadino non capisce di aver sbagliato indirizzo).
 */
describe('percorsi a due segmenti', function (): void {
    test('senza modello ne pagina CMS rispondono 404 in tutte le lingue', function (string $locale, string $path): void {
        /** @var TestCase $this */
        $this->get('/'.$locale.$path)->assertNotFound();
    })->with(['it', 'en', 'de', 'es'])->with([
        'segmenti qualunque' => '/zzz/yyy',
        'ticket con codice non numerico' => '/tickets/zzz-inesistente',
    ]);

    test('le pagine CMS esistenti non diventano 404', function (string $path): void {
        /** @var TestCase $this */
        $this->get('/it'.$path)->assertOk();
    })->with([
        'traccia segnalazione' => ['/tickets/track'],
        'conferma' => ['/tickets/confirmation'],
        'accesso' => ['/auth/login'],
    ]);
});
