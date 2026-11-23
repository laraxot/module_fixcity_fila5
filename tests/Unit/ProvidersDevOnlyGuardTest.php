<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;

uses(TestCase::class);

/**
 * Laravel 13 mantiene bootstrap/providers.php volutamente minimale: i provider
 * applicativi e dei moduli arrivano dal normale discovery/configurazione.
 *
 * @see Modules/Xot/docs/adr/0009-laravel-bootstrap-provider-registry.md
 */
it('keeps the Laravel bootstrap provider registry minimal', function (): void {
    $providers = require base_path('bootstrap/providers.php');
    if (! is_array($providers)) {
        throw new LogicException('bootstrap/providers.php deve restituire un array.');
    }

    Assert::assertSame([AppServiceProvider::class], array_values(array_filter($providers, is_string(...))));
});

it('does not hardcode optional development providers in the bootstrap file', function (): void {
    $contents = file_get_contents(base_path('bootstrap/providers.php'));

    Assert::assertStringNotContainsString('Laravel\\Boost', $contents);
    Assert::assertStringNotContainsString('Laravel\\Mcp', $contents);
});
