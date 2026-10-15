<?php

declare(strict_types=1);

use Filament\Panel;
use Modules\Fixcity\Providers\Filament\AdminPanelProvider;
use Modules\Xot\Filament\AvatarProviders\InitialsAvatarProvider;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\base64_decode;
use function Safe\simplexml_load_string;

uses(TestCase::class);

/**
 * DCP-139 / DCP-137: l'avatar predefinito dei pannelli Filament veniva caricato da `ui-avatars.com` a ogni pagina del back office (un servizio terzo
 * riceveva IP e iniziali di chi amministra; una CSP con `img-src 'self'` lo bloccherebbe). Ora e' un SVG incorporato, senza richieste di rete.
 */
function xotAvatarFor(string $name): string
{
    $record = new Illuminate\Foundation\Auth\User;
    $record->setAttribute('name', $name);

    return (new InitialsAvatarProvider)->get($record);
}

function xotAvatarSvg(string $dataUri): string
{
    Assert::assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);

    return base64_decode(substr($dataUri, strlen('data:image/svg+xml;base64,')), true);
}

test('the avatar is an embedded image, never a remote url', function (): void {
    $uri = xotAvatarFor('Marco Sottana');

    Assert::assertStringStartsWith('data:image/svg+xml;base64,', $uri);
    Assert::assertStringNotContainsString('http', $uri);
    Assert::assertStringNotContainsString('ui-avatars', xotAvatarSvg($uri));
});

test('it shows at most two initials in upper case', function (string $name, string $initials): void {
    Assert::assertStringContainsString('>'.$initials.'</text>', xotAvatarSvg(xotAvatarFor($name)));
})->with([
    'nome e cognome' => ['Marco Sottana', 'MS'],
    'tre parti' => ['marco antonio sottana', 'MA'],
    'una parola' => ['Admin', 'A'],
    'prefisso tra parentesi' => ['[SYSTEM] Admin', 'SA'],
    'accentate' => ['élodie östberg', 'ÉÖ'],
]);

test('a name with markup cannot inject into the image', function (): void {
    $svg = xotAvatarSvg(xotAvatarFor('<script>alert(1)</script> x'));

    Assert::assertStringNotContainsString('<script', $svg);
    Assert::assertInstanceOf(SimpleXMLElement::class, simplexml_load_string($svg), 'L\'SVG deve restare XML valido.');
});

test('the panels of every module use the local avatar provider', function (): void {
    $panel = (new AdminPanelProvider(app()))->panel(Panel::make());

    Assert::assertSame(InitialsAvatarProvider::class, $panel->getDefaultAvatarProvider());
});
