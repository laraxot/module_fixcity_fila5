<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('tickets.track.code');

render(function (string $code): RedirectResponse {
    $path = '/tickets/track?code='.rawurlencode($code);
    $localized = LaravelLocalization::getLocalizedURL(
        LaravelLocalization::getCurrentLocale(),
        $path
    );

    return redirect()->to($localized !== false ? $localized : $path);
});
