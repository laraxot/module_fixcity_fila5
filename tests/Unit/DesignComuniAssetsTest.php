<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Modules\Fixcity\Tests\TestCase;
use Themes\Sixteen\Infrastructure\Assets\DesignComuniAssets;

uses(TestCase::class);

/*
 * DCP-99: asset Design Comuni con lo stesso valore relativo del riferimento (`../assets/...`) solo quando la pagina Folio condivide il flag
 * `dcRelativeAssets`; altrimenti l'URL assoluto di sempre (sito reale e aggiornamenti Livewire). Nessun database.
 */
function dcFakeRequest(string $path): void
{
    app()->instance('request', Request::create('/'.$path));
}

afterEach(function (): void {
    view()->share('dcRelativeAssets', false);
});

it('keeps the absolute url without the flag', function (): void {
    view()->share('dcRelativeAssets', false);
    dcFakeRequest('it/tests/homepage');

    expect(DesignComuniAssets::base())->toBe(asset('themes/Sixteen/design-comuni/assets'));
});

it('is relative to the current path with the flag', function (string $path, string $expected): void {
    view()->share('dcRelativeAssets', true);
    dcFakeRequest($path);

    expect(DesignComuniAssets::base())->toBe($expected);
})->with([
    'page under a section' => ['it/tests/homepage', '../assets'],
    'real wizard page' => ['it/tickets/create', '../assets'],
    'english' => ['en/tests/eventi', '../assets'],
    'catalog' => ['it/tests', './assets'],
    'deeper path' => ['it/a/b/c', '../../assets'],
    'trailing slash on a page' => ['it/tests/homepage/', '../../assets'],
    'trailing slash on a section' => ['it/tests/', '../assets'],
    'query string is ignored' => ['it/tickets/create?step=riepilogo', '../assets'],
]);

it('falls back to the absolute url when the first segment is not a language', function (string $path): void {
    view()->share('dcRelativeAssets', true);
    dcFakeRequest($path);

    expect(DesignComuniAssets::base())->toBe(asset('themes/Sixteen/design-comuni/assets'));
})->with(['no language' => ['tests/homepage'], 'language only' => ['it'], 'long prefix' => ['ita/tests/homepage']]);

it('maps only paths under the design comuni assets to the relative base', function (): void {
    view()->share('dcRelativeAssets', true);
    dcFakeRequest('it/tests/homepage');

    expect(DesignComuniAssets::url('themes/Sixteen/design-comuni/assets/images/logo-comune.svg'))->toBe('../assets/images/logo-comune.svg')
        ->and(DesignComuniAssets::url('themes/Sixteen/images/logo.svg'))->toBe(asset('themes/Sixteen/images/logo.svg'));
});
