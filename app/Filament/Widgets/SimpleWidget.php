<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Modules\Xot\Filament\Widgets\XotBaseSchemaWidget;

class SimpleWidget extends XotBaseSchemaWidget
{
    protected string $view = 'fixcity::filament.widgets.simple';

    public function getFormSchema(): array
    {
        return [];
    }
}
