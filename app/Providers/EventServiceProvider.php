<?php

declare(strict_types=1);

namespace Modules\Fixcity\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
    }
}
