<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/*
 * DCP-99: `/{lingua}/assets/{path}` serve gli asset Design Comuni del tema (percorsi relativi del riferimento). Sola lettura, confinata nel tema.
 * Usa il TestCase del modulo Fixcity (connessioni su sqlite di test): il boot dell'app lo richiede.
 */
it('serves the icon sprite in every language', function (string $lang): void {
    $response = $this->get('/'.$lang.'/assets/bootstrap-italia/dist/svg/sprites.svg');

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('svg');
    expect($response->headers->get('cache-control'))->toContain('public');
})->with(['it', 'en']);

it('answers 404 for a missing asset', function (): void {
    $this->get('/it/assets/images/questo-file-non-esiste.svg')->assertNotFound();
});

it('refuses path traversal', function (string $path): void {
    $this->get($path)->assertNotFound();
})->with([
    'dot segments' => ['/it/assets/../../../composer.json'],
    'encoded dots' => ['/it/assets/%2e%2e/%2e%2e/composer.json'],
    'encoded slash' => ['/it/assets/..%2f..%2fcomposer.json'],
]);
