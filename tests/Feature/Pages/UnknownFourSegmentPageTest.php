<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/**
 * DCP-186 (scheda del front office, gruppo E): la pagina catch-all `[container0]/[slug0]/[container1]/[token]` mostra qualcosa solo per il
 * ripristino della password (`auth/password/reset/<token>`); per ogni altro percorso a quattro segmenti il frammento Volt non aveva radice e
 * Livewire lanciava `RootTagMissingFromViewException`: HTTP 500 a un URL qualunque (rumore per i log e per chi monitora i 5xx). Stesso criterio
 * di DCP-126 per i due segmenti: un percorso senza pagina e' un 404 vero.
 */
describe('percorsi a quattro segmenti', function (): void {
    test('senza pagina rispondono 404 in tutte le lingue', function (string $locale, string $path): void {
        /** @var TestCase $this */
        $this->get('/'.$locale.$path)->assertNotFound();
    })->with(['it', 'en', 'de', 'es'])->with([
        'segmenti qualunque' => '/a/b/c/d',
        'prefisso auth ma non il ripristino' => '/auth/password/forgot/abc',
        'ripristino con un segmento sbagliato' => '/auth/password/reimposta/abc',
    ]);

    test('il ripristino della password con un token non diventa 404', function (): void {
        /** @var TestCase $this */
        $this->get('/it/auth/password/reset/abc123')->assertOk();
    });
});
