<?php

declare(strict_types=1);

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

it('keeps the requested locale when a guest opens the ticket wizard', function (string $locale): void {
    /** @var TestCase $this */
    $response = $this->get('/'.$locale.'/tickets/create');

    $response->assertRedirect();
    Assert::assertSame($locale, app()->getLocale());
    Assert::assertSame($locale, LaravelLocalization::getCurrentLocale());
    $location = $response->headers->get('Location');
    Assert::assertIsString($location);
    Assert::assertSame(
        '/'.$locale.'/auth/login',
        parse_url($location, PHP_URL_PATH),
        'The guest login redirect must keep the requested route locale.',
    );
    $intended = session()->get('url.intended');
    Assert::assertIsString($intended);
    Assert::assertSame(
        '/'.$locale.'/tickets/create',
        parse_url($intended, PHP_URL_PATH),
        'The login flow must retain the protected URL as its intended destination.',
    );
})->with(['it', 'en']);

it('keeps API guests on the JSON unauthorized path', function (): void {
    /** @var TestCase $this */
    $this->getJson('/en/tickets/create')->assertUnauthorized();
});
