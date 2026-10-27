<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\Export\SanitizeSpreadsheetCellAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('sanitizes spreadsheet cells against formula injection', function (): void {
    $action = app(SanitizeSpreadsheetCellAction::class);

    Assert::assertSame("'=1+1", $action->execute('=1+1'));
    Assert::assertSame('test', $action->execute('test'));
    Assert::assertSame('', $action->execute(null));
});
