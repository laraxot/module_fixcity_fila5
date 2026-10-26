<?php

declare(strict_types=1);

use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

use function Safe\file_get_contents;
use function Safe\putenv;

uses(TestCase::class);

/*
 * G-09 (DCP-282): `bootstrap/app.php` fissava `trustProxies(at: '127.0.0.1')` e non c'era nessun `trustHosts`. Ora i proxy fidati vengono dalla
 * config `trustedproxy.proxies` (variabile TRUSTED_PROXIES, default 127.0.0.1: stesso comportamento di prima) e gli host fidati dalla config
 * `security.trusted_hosts` (variabile TRUSTED_HOSTS, vuota di default: nessuna restrizione, il framework la salta comunque con APP_ENV=local e sotto test).
 * `env()` nel callback di `withMiddleware` non vedrebbe `.env`, che a quel punto non e' ancora caricato: per questo la lettura passa dalla config.
 */
afterEach(function (): void {
    putenv('TRUSTED_PROXIES');
    putenv('TRUSTED_HOSTS');
    Request::setTrustedHosts([]);
    Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
});

/**
 * @return array<string, mixed>
 */
function configFile(string $name): array
{
    $config = require base_path('config/'.$name.'.php');
    Assert::assertIsArray($config);

    /** @var array<string, mixed> $config */
    return $config;
}

function isHttpsThroughProxy(string $remoteAddress): bool
{
    $request = Request::create('http://fixcity.example/', 'GET', [], [], [], ['REMOTE_ADDR' => $remoteAddress, 'HTTP_X_FORWARDED_PROTO' => 'https']);
    $secure = false;

    (new TrustProxies)->handle($request, static function (Request $handled) use (&$secure): Response {
        $secure = $handled->isSecure();

        return new Response;
    });

    return $secure;
}

it('no longer pins the trusted proxy in the bootstrap code', function (): void {
    $bootstrap = file_get_contents(base_path('bootstrap/app.php'));

    Assert::assertStringNotContainsString("trustProxies(at: '127.0.0.1')", $bootstrap);
    Assert::assertStringContainsString('trustHosts(', $bootstrap);
});

it('trusts only localhost as a proxy by default, exactly as before', function (): void {
    Assert::assertSame('127.0.0.1', config('trustedproxy.proxies'));
    Assert::assertTrue(isHttpsThroughProxy('127.0.0.1'), 'Il proxy locale deve restare fidato (proxy HTTPS di sviluppo).');
    Assert::assertFalse(isHttpsThroughProxy('203.0.113.9'), 'Un IP qualunque non deve poter dichiarare https.');
});

it('takes the trusted proxies from the configuration', function (): void {
    config(['trustedproxy.proxies' => '10.0.0.0/8']);

    Assert::assertTrue(isHttpsThroughProxy('10.1.2.3'));
    Assert::assertFalse(isHttpsThroughProxy('127.0.0.1'));
});

it('reads TRUSTED_PROXIES from the environment and falls back to localhost when it is empty', function (): void {
    putenv('TRUSTED_PROXIES=10.0.0.0/8,192.168.1.40');
    Assert::assertSame('10.0.0.0/8,192.168.1.40', configFile('trustedproxy')['proxies']);

    putenv('TRUSTED_PROXIES=');
    Assert::assertSame('127.0.0.1', configFile('trustedproxy')['proxies']);
});

it('does not restrict hosts unless TRUSTED_HOSTS is set', function (): void {
    Assert::assertSame([], config('security.trusted_hosts'));
    Assert::assertSame([], (new TrustHosts(app()))->hosts());

    Request::setTrustedHosts((new TrustHosts(app()))->hosts());
    Assert::assertSame('qualunque.example', Request::create('http://qualunque.example/')->getHost());
});

it('turns TRUSTED_HOSTS into an exact, case-insensitive allow list', function (): void {
    putenv('TRUSTED_HOSTS=fixcity.example.it, www.fixcity.example.it');
    Assert::assertSame(['fixcity.example.it', 'www.fixcity.example.it'], configFile('security')['trusted_hosts']);

    config(['security.trusted_hosts' => ['fixcity.example.it', 'www.fixcity.example.it']]);
    Request::setTrustedHosts((new TrustHosts(app()))->hosts());

    Assert::assertSame('fixcity.example.it', Request::create('https://fixcity.example.it/it')->getHost());
    Assert::assertSame('www.fixcity.example.it', Request::create('https://WWW.fixcity.example.it/it')->getHost());
    Assert::assertSame('fixcity.example.it', Request::create('https://fixcity.example.it:8443/it')->getHost());
    foreach (['https://evil.example/', 'https://fixcity-example-it.evil.example/', 'https://evil.fixcity.example.it/', 'https://xfixcity.example.it/'] as $url) {
        expect(static fn (): string => Request::create($url)->getHost())->toThrow(SuspiciousOperationException::class);
    }
});

it('documents both variables in the production example without a value to copy blindly', function (): void {
    $example = file_get_contents(base_path('.env.production.example'));

    Assert::assertStringContainsString('TRUSTED_PROXIES=', $example);
    Assert::assertStringContainsString('TRUSTED_HOSTS=', $example);
});
