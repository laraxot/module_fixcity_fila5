<?php

declare(strict_types=1);

namespace Modules\Fixcity\Providers;

use Modules\Xot\Providers\XotBaseServiceProvider;

class FixcityServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Fixcity';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    public function boot(): void
    {
        parent::boot();
    }
}
