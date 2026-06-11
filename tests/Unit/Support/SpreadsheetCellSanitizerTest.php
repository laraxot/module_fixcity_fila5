<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Support;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Support\SpreadsheetCellSanitizer;
use PHPUnit\Framework\TestCase;

class SpreadsheetCellSanitizerTest extends TestCase
{
    public function test_it_prefixes_formula_like_values(): void
    {
        Assert::assertSame("'=1+1", SpreadsheetCellSanitizer::sanitize('=1+1'));
        Assert::assertSame('test', SpreadsheetCellSanitizer::sanitize('test'));
        Assert::assertSame('', SpreadsheetCellSanitizer::sanitize(null));
    }
}
