<?php

declare(strict_types=1);

namespace Modules\Fixcity\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Laravel\Folio\Folio;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Observers\TicketActivityObserver;
use Modules\Fixcity\Policies\TicketPolicy;
use Modules\Xot\Providers\XotBaseServiceProvider;

class FixcityServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Fixcity';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();


        Gate::policy(Ticket::class, TicketPolicy::class);
        TicketActivity::observe(TicketActivityObserver::class);

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
        $apiPages = dirname($this->module_dir, 3).'/resources/views/pages/api';

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
