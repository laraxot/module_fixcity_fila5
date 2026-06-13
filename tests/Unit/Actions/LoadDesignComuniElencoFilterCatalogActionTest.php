<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Actions\LoadDesignComuniElencoFilterCatalogAction;
use Modules\Fixcity\Tests\TestCase;

uses(\Modules\Fixcity\Tests\TestCase::class);

describe('Load Design Comuni Elenco Filter Catalog Action', function (): void {
    test('_it_loads_eleven_design_comuni_categories', function (): void {
$catalog = app(LoadDesignComuniElencoFilterCatalogAction::class)->execute();

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
    });
});
