<?php
namespace Modules\Fixcity\Tests\Unit;

use Modules\Fixcity\Tests\TestCase;
use Modules\Fixcity\Models\Activity;

it('has main model class', function () {
    expect(class_exists(Activity::class))->toBeTrue();
});
