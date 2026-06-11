<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Actions\LoadCityDesignFilterCatalogAction;
use Modules\Fixcity\Tests\TestCase;

class LoadCityDesignFilterCatalogActionTest extends TestCase
{
    public function test_it_loads_eleven_city_design_categories(): void
    {
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
            (string) $catalog['items'][0]['display_label'],
        );
        Assert::assertSame(21, $catalog['items'][0]['count']);
    }
}
