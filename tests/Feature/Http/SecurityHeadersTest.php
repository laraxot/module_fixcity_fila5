<?php

declare(strict_types=1);

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Modules\Fixcity\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

uses(TestCase::class);

/*
 * DCP-139 (GDPR art. 32): intestazioni di sicurezza su ogni risposta. Tre rotte (home, login, elenco) devono averle tutte;
 * il test fallisce se una manca. La Permissions-Policy deve lasciare la geolocalizzazione al sito (il passo 2 del wizard la usa).
 */
dataset('public routes', ['/it', '/it/auth/login', '/it/tickets']);

it('sends the security headers on every public route', function (string $path): void {
    /** @var TestCase $this */
    $response = $this->get($path);
    $response->assertOk();

    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    expect((string) $response->headers->get('Content-Security-Policy'))->toContain("frame-ancestors 'self'");
    expect((string) $response->headers->get('Content-Security-Policy-Report-Only'))->toContain("default-src 'self'");
    $response->assertHeaderMissing('Strict-Transport-Security');
    $response->assertHeaderMissing('X-Powered-By');
})->with('public routes');

it('keeps geolocation for the site and denies the other sensors', function (): void {
    /** @var TestCase $this */
    $policy = (string) $this->get('/it')->headers->get('Permissions-Policy');

    expect($policy)->toContain('geolocation=(self)')->not->toContain('geolocation=()')
        ->and($policy)->toContain('camera=()')->toContain('microphone=()');
});

it('lists the hosts really loaded by the pages in the report-only policy', function (): void {
    /** @var TestCase $this */
    $csp = (string) $this->get('/it')->headers->get('Content-Security-Policy-Report-Only');

    // Misurati con bashscripts/tools/visual/net-hosts-probe.mjs: tessere OSM, Nominatim (indirizzo dal GPS), Google Fonts.
    expect($csp)->toContain('https://*.tile.openstreetmap.org')
        ->toContain('https://nominatim.openstreetmap.org')
        ->toContain('https://fonts.googleapis.com')
        ->toContain('https://fonts.gstatic.com');
});

it('sends Strict-Transport-Security only on HTTPS requests and honours the configured max age', function (): void {
    /** @var TestCase $this */
    $this->get('/it')->assertHeaderMissing('Strict-Transport-Security');

    $secure = $this->withServerVariables(['HTTPS' => 'on'])->get('https://localhost/it');
    $secure->assertHeader('Strict-Transport-Security', 'max-age=31536000');

    config(['security.headers.hsts.max_age' => 0]);
    $this->withServerVariables(['HTTPS' => 'on'])->get('https://localhost/it')->assertHeaderMissing('Strict-Transport-Security');
});

it('switches the content security policy between report-only, enforce and off', function (): void {
    /** @var TestCase $this */
    config(['security.headers.csp.mode' => 'enforce']);
    $enforced = $this->get('/it');
    expect((string) $enforced->headers->get('Content-Security-Policy'))->toContain("default-src 'self'")->toContain("frame-ancestors 'self'");
    $enforced->assertHeaderMissing('Content-Security-Policy-Report-Only');

    config(['security.headers.csp.mode' => 'off']);
    $off = $this->get('/it');
    $off->assertHeaderMissing('Content-Security-Policy');
    $off->assertHeaderMissing('Content-Security-Policy-Report-Only');
    $off->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('does nothing when disabled and never overrides a header the response already set', function (): void {
    /** @var TestCase $this */
    $middleware = new SecurityHeaders;
    $next = static fn (Request $request): Response => new Response('', 200, ['Referrer-Policy' => 'no-referrer']);

    $kept = $middleware->handle(Request::create('/'), $next);
    expect($kept->headers->get('Referrer-Policy'))->toBe('no-referrer')
        ->and($kept->headers->get('X-Content-Type-Options'))->toBe('nosniff');

    config(['security.headers.enabled' => false]);
    $disabled = $middleware->handle(Request::create('/'), $next);
    expect($disabled->headers->has('X-Content-Type-Options'))->toBeFalse();
});
