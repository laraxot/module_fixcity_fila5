<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Modules\Fixcity\Actions\LoadCityDesignFilterCatalogAction;
use Tests\TestCase;

class LoadCityDesignFilterCatalogActionTest extends TestCase
{
    public function test_it_loads_eleven_city_design_categories(): void
    {
        $catalog = app(LoadCityDesignFilterCatalogAction::class)->execute();

        $this->assertSame('categoria', $catalog['legend']);
        $this->assertCount(11, $catalog['items']);
        $this->assertSame(645, $catalog['totalCount']);
        $this->assertSame(
            'Acqua, allagamenti, problemi fognari',
            $catalog['items'][0]['label'],
        );
        $this->assertStringContainsString(
            '(21)',
            (string) $catalog['items'][0]['display_label'],
        );
        $this->assertSame(21, $catalog['items'][0]['count']);
    }
}
