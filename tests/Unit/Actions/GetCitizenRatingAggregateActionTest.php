<?php

declare(strict_types=1);

namespace Tests\Modules\Fixcity\Unit\Actions;

use Modules\Fixcity\Actions\GetCitizenRatingAggregateAction;
use Tests\Modules\Fixcity\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('returns zero count and null average when no ratings exist', function (): void {
    $result = app(GetCitizenRatingAggregateAction::class)->execute();

    Assert::assertEquals([
        'count' => 0,
        'average' => null,
    ], is_array($result) ? $result : $result->all());
});
