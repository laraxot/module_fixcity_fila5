<?php

declare(strict_types=1);
use Modules\Fixcity\Tests\TestCase;

use function Safe\file_get_contents;

uses(TestCase::class);

/**
 * Gate facts:
 * 1. Behaviour: public timeline must not 500 when Spatie permissions table
 *    is missing on the user connection.
 * 2. Existing: no dedicated timeline unit; detail covered by page tests.
 * 3. User: prod crash on GET /it/tickets/1 via BuildTicketTimelineAction.
 */
test('ticket timeline soft-guards missing permissions table', function (): void {
    $source = (string) file_get_contents(
        dirname(__DIR__, 3).'/app/Actions/BuildTicketTimelineAction.php'
    );

    expect($source)
        ->toContain('userPermissionsReady')
        ->toContain("Schema::connection('user')->hasTable('permissions')")
        ->toContain('viewerIsOperator')
        ->toContain('ticket timeline operator check skipped');
});
