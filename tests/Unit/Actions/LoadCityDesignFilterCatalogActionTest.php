<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Actions\LoadCityDesignFilterCatalogAction;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

uses(\Modules\Fixcity\Tests\TestCase::class);

describe('Load City Design Filter Catalog Action', function (): void {
    test('_it_loads_eleven_city_design_categories', function (): void {
$catalog = app(LoadCityDesignFilterCatalogAction::class)->execute();

        Assert::assertSame('categoria', $catalog['legend']);
        Assert::assertCount(11, $catalog['items']);
        Assert::assertSame(645, $catalog['totalCount']);
        Assert::assertSame(
            'Acqua, allagamenti, problemi fognari',
            $catalog['items'][0]['label'],
        );
        Assert::assertStringContainsString(
            '(21)',
            SafeStringCastAction::cast($catalog['items'][0]['display_label']),
        );
        Assert::assertSame(21, $catalog['items'][0]['count']);
    });
});
