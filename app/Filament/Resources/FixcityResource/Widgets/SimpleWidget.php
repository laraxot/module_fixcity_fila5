<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\FixcityResource\Widgets;

use Modules\Xot\Filament\Widgets\XotBaseWidget;

class SimpleWidget extends XotBaseWidget
{
    protected string $view = 'fixcity::filament.widgets.simple';

    public function getFormSchema(): array
    {
        return [];
    }
}
