<?php

declare(strict_types=1);

namespace Modules\Fixcity\Providers;

use Modules\Xot\Providers\XotBaseRouteServiceProvider;

class RouteServiceProvider extends XotBaseRouteServiceProvider
{
    public string $name = 'Fixcity';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    protected function mapApiRoutes(): void
    {
        // No API routes — Fixcity uses Folio for API endpoints.
        // Controllers are not used in this project (Folio + Volt + Filament).
    }
}
