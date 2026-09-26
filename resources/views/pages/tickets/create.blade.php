<?php

declare(strict_types=1);

use Illuminate\View\View;
use Modules\Cms\Http\Middleware\PageSlugMiddleware;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('tickets.create');
middleware(PageSlugMiddleware::class);

render(function (View $view): View {
    return $view->with(['data' => []]);
});
?>

<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 py-6">
        <x-page side="content" slug="tickets.create" :data="$data" />
    </div>
</x-layouts.app>
