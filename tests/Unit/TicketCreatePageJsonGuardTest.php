<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\glob;

uses(TestCase::class);

/*
 * Guardia page-json (DCP-35): /it/tickets/create e' servita da `tickets.create.json` del tenant fixcity.
 * Un `tests.tickets*.json` (es. tests.tickets-create.json) creerebbe una seconda sorgente per la stessa pagina.
 * Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);

it('has no tests.tickets page json in any tenant or module', function () use ($laravelRoot): void {
    $pages = $laravelRoot.'/{config/*/*,Modules/*/config}/database/content/pages';

    Assert::assertNotSame([], glob($pages, GLOB_BRACE | GLOB_ONLYDIR), 'No page json directory found: the guard would pass vacuously.');
    Assert::assertSame(
        [],
        glob($pages.'/tests.tickets*.json', GLOB_BRACE),
        'Remove tests.tickets*.json: tickets.create.json is the only source for the page.',
    );
});

it('keeps tickets.create.json in the fixcity tenant', function () use ($laravelRoot): void {
    Assert::assertFileExists($laravelRoot.'/config/local/fixcity/database/content/pages/tickets.create.json');
});
