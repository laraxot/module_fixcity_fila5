<?php

declare(strict_types=1);

namespace Modules\Fixcity\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects legacy public ticket receipt URLs to the canonical tracking page.
 *
 * @see docs/contracts/redirect-ticket-track-code-middleware.md
 *
 * @contract docs/contracts/redirect-ticket-track-code-middleware.md
 */
final class RedirectTicketTrackCodeMiddleware
{
    /** @see docs/contracts/redirect-ticket-track-code-middleware.md */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $code = $route?->parameter('container1');

        if (! is_string($code) || $code === '') {
            $response = $next($request);
            if (! $response instanceof Response) {
                throw new \LogicException('The next middleware must return a Response.');
            }

            return $response;
        }

        $path = '/tickets/track?code='.rawurlencode($code);
        $localized = LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), $path);

        return redirect()->to($localized !== false ? $localized : $path);
    }
}
