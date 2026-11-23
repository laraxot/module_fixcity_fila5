<?php

declare(strict_types=1);

namespace Modules\Fixcity\Providers;

use Illuminate\Support\Facades\File;
use Laravel\Folio\Folio;
use Modules\Fixcity\Actions\ResolveTicketMediaDiskAction;
use Modules\Fixcity\Actions\Ticket\ListPublicTicketPathsAction;
use Modules\Fixcity\Http\Middleware\RedirectTicketTrackCodeMiddleware;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Observers\TicketActivityObserver;
use Modules\Fixcity\Observers\TicketMediaObserver;
use Modules\Media\Models\Media;
use Modules\Seo\Contracts\SitemapPathsContract;
use Modules\Xot\Providers\XotBaseServiceProvider;

/**
 * Registers Fixcity's application services and CMS middleware extensions.
 *
 * @see Modules/Fixcity/docs/adr/public-ticket-detail-route-ownership.md
 */
class FixcityServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Fixcity';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    /**
     * @see Modules/Fixcity/docs/adr/public-ticket-detail-route-ownership.md
     */
    public function register(): void
    {
        parent::register();

        // Le segnalazioni pubbliche entrano nella sitemap di Seo senza che Seo conosca Fixcity (DCP-273).
        $this->app->tag([ListPublicTicketPathsAction::class], SitemapPathsContract::TAG);
    }

    /**
     * Registers Fixcity-owned middleware with the generic CMS page pipeline.
     *
     * CMS remains reusable in installations that do not include Fixcity; the
     * application module opts into the pipeline from its own service provider.
     *
     * @see Modules/Fixcity/docs/contracts/redirect-ticket-track-code-middleware.md
     */
    private function registerPageMiddleware(): void
    {
        $configured = config('cms.config.page_middleware_allowlist');
        $allowlist = is_array($configured) ? $configured : [];

        config([
            'cms.config.page_middleware_allowlist' => array_values(array_unique([
                ...array_filter($allowlist, is_string(...)),
                RedirectTicketTrackCodeMiddleware::class,
            ])),
        ]);
    }

    /**
     * @see Modules/Fixcity/docs/adr/public-ticket-detail-route-ownership.md
     * @see Modules/Fixcity/docs/adr/policy-autodiscovery.md
     * @see Modules/Fixcity/docs/contracts/policy-registration.md
     */
    public function boot(): void
    {
        parent::boot();

        $this->app->booted(function (): void {
            $this->registerPageMiddleware();

            config([
                'xra.container0_model_map' => array_replace(
                    (array) config('xra.container0_model_map', []),
                    ['tickets' => Ticket::class],
                ),
            ]);
        });

        TicketActivity::observe(TicketActivityObserver::class);
        $this->registerTicketMediaDisk();
        Media::observe(TicketMediaObserver::class);

        $this->registerFolioApiRoutes();
    }

    /**
     * Disco privato degli allegati dei ticket (SEC-04, DB-19), solo se la config del tenant non lo definisce gia'
     * (per usare S3 o un altro percorso basta definire `filesystems.disks.<nome>` nel tenant).
     */
    protected function registerTicketMediaDisk(): void
    {
        $disk = app(ResolveTicketMediaDiskAction::class)->configuredName();
        if ($disk === null || is_array(config('filesystems.disks.'.$disk))) {
            return;
        }

        config(['filesystems.disks.'.$disk => ResolveTicketMediaDiskAction::defaultDiskConfig()]);
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
