<?php

declare(strict_types=1);

namespace Modules\Fixcity\Providers;

use Illuminate\Support\Facades\File;
use Laravel\Folio\Folio;
use Modules\Xot\Providers\XotBaseServiceProvider;

class FixcityServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Fixcity';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    public function boot(): void
    {
        parent::boot();

        $this->registerFolioApiRoutes();
    }

    /**
     * Endpoint JSON pubblici (mappa) senza prefisso locale — Folio, mai Controller.
     *
     * @see resources/views/pages/api/
     * @see docs/wiki/concepts/folio-api-no-controllers.md
     */
    protected function registerFolioApiRoutes(): void
    {
        $apiPages = dirname($this->module_dir, 2).'/resources/views/pages/api';

        if (! File::isDirectory($apiPages)) {
            return;
        }

        Folio::path($apiPages)
            ->uri('/api')
            ->middleware([
                '*' => ['web'],
            ]);
    }
}
