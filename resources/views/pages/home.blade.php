<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

/**
 * Archive /it/home del modulo: non deve competere con la home tema Sixteen
 * (`pages/index.blade.php` → /{locale}) né chiamare route() Illuminate.
 * Redirect alla home locale; nome dedicato per non rubare `home` al tema.
 */
name('fixcity.home.archive');

render(static fn (): RedirectResponse => redirect()->to(
    LaravelLocalization::localizeURL('/'),
));
