<?php

declare(strict_types=1);

use App\Http\Middleware\BlockShowcasePages;
use Illuminate\Http\Request;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Le pagine dimostrative /{lang}/tests/* rispondevano 200 in produzione su fixcity.eu (verificato il 2026-11-05), con dati
 * finti e pagamenti simulati. BlockShowcasePages le nasconde dove config('security.showcase_pages') è falso.
 */
it('recognises showcase paths with and without the locale prefix', function (string $uri, bool $expected): void {
    config(['laravellocalization.supportedLocales' => ['it' => [], 'en' => [], 'de' => [], 'es' => []]]);

    Assert::assertSame($expected, BlockShowcasePages::isShowcasePath(Request::create($uri)));
})->with([
    'tests index' => ['/it/tests', true],
    'tests page' => ['/en/tests/segnalazioni-elenco', true],
    'without locale' => ['/tests/homepage', true],
    'test auth' => ['/sixteen/test-auth/spid', true],
    'counter' => ['/it/counter', true],
    'legacy demo login' => ['/it/auth/login3', true],
    'legacy homepage' => ['/it/homepage', true],
    'real login' => ['/it/auth/login', false],
    'cms slug of a showcase page' => ['/it/pages/tests.amministrazione', true],
    'regular cms page' => ['/it/pages/privacy', false],
    'tickets list' => ['/it/tickets', false],
    'prefix only as substring' => ['/it/testsuite', false],
    'home' => ['/it', false],
]);

it('answers 404 for showcase pages when they are switched off', function (): void {
    config(['security.showcase_pages' => false]);

    $this->get('/it/tests')->assertNotFound();
    $this->get('/it/tests/segnalazioni-elenco')->assertNotFound();
});

it('lets the showcase pages through when they are switched on', function (): void {
    config(['security.showcase_pages' => true]);

    $response = $this->get('/it/tests');

    Assert::assertLessThan(400, $response->getStatusCode(), 'Con SHOWCASE_PAGES=true la pagina deve rispondere senza errori: '.($response->exception?->getMessage() ?? 'nessuna eccezione registrata'));
});
