<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Modules\Fixcity\Actions\GetCitizenRatingAggregateAction;
use PHPUnit\Framework\Assert;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

it('returns zero count and null average when no ratings exist', function (): void {
    $result = app(GetCitizenRatingAggregateAction::class)->execute();

    Assert::assertEquals([
        'count' => 0,
        'average' => null,
    ], is_array($result) ? $result : $result->all());
});
