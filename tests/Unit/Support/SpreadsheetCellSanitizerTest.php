<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Support;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Support\SpreadsheetCellSanitizer;
use PHPUnit\Framework\TestCase;

uses(\Modules\Fixcity\Tests\TestCase::class);

describe('Spreadsheet Cell Sanitizer', function (): void {
    test('_it_prefixes_formula_like_values', function (): void {
Assert::assertSame("'=1+1", SpreadsheetCellSanitizer::sanitize('=1+1'));
        Assert::assertSame('test', SpreadsheetCellSanitizer::sanitize('test'));
        Assert::assertSame('', SpreadsheetCellSanitizer::sanitize(null));
    });
});
