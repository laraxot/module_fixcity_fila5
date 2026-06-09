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
        // API HTTP: Folio pages/api/ + Actions (see docs/wiki/concepts/folio-api-no-controllers.md).
        // No Route:: / Controllers — Folio + Volt + Filament only.
    }
}
