<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Schemas\Components;

use Filament\Schemas\Components\Wizard;

/**
 * Wizard schema component che usa la vista tema {@see pub_theme::components.wizard}
 * (allineata al markup vendor Filament) per il frontoffice Fixcity.
 */
final class PubThemeWizard extends Wizard
{
    /**
     * @var view-string
     */
    protected string $view = 'pub_theme::components.wizard';
}
