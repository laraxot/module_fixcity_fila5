<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Modules\Fixcity\Actions\GetCitizenRatingAggregateAction;
use Tests\TestCase;

uses(TestCase::class);

it('returns zero count and null average when no ratings exist', function (): void {
    $result = app(GetCitizenRatingAggregateAction::class)->execute();

    expect($result)->toMatchArray([
        'count' => 0,
        'average' => null,
    ]);
});
