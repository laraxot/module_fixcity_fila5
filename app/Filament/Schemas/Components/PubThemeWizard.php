<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Schemas\Components;

use Filament\Schemas\Components\Wizard;

/**
 * PubThemeWizard - Wizard with Design Comuni styling for public frontend.
 *
 * This class extends Filament's Wizard component and overrides the view
 * to use the pub_theme::components.wizard template which implements
 * Design Comuni styling patterns.
 *
 * Architecture:
 * - Module provides: Wizard schema (steps, validation, actions)
 * - PubThemeWizard provides: View override to pub_theme::components.wizard
 * - pub_theme::components.wizard provides: Design Comuni stepper + footer
 */
class PubThemeWizard extends Wizard
{
    /**
     * The view for the wizard component.
     * This enables Design Comuni styling on the public frontend.
     */
    protected string $view = 'pub_theme::components.wizard';
}
