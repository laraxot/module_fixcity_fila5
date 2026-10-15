<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/*
 * DCP-90: la pagina generica del tema `[container0]/[slug0]/[container1]` oscurava
 * `Modules/Fixcity/resources/views/pages/tickets/track/[code].blade.php`: il link della ricevuta
 * `/{locale}/tickets/track/{code}` rispondeva 200 con una pagina vuota invece di portare al modulo di ricerca.
 */
it('sends the receipt link to the localized tracking form with the code', function (string $locale): void {
    /** @var TestCase $this */
    $response = $this->get('/'.$locale.'/tickets/track/TCK-REDIR01');

    $response->assertRedirect();
    $location = $response->headers->get('Location');
    Assert::assertIsString($location);
    Assert::assertSame('/'.$locale.'/tickets/track', parse_url($location, PHP_URL_PATH));
    Assert::assertSame('code=TCK-REDIR01', parse_url($location, PHP_URL_QUERY));
})->with(['it', 'en']);

it('does not redirect other three-segment pages handled by the same theme page', function (): void {
    /** @var TestCase $this */
    Assert::assertFalse($this->get('/it/auth/password/reset')->isRedirect());
});
