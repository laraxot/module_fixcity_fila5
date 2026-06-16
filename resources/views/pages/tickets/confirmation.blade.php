<?php

use Illuminate\View\View;
use function Laravel\Folio\{middleware, name, render};
use Modules\Cms\Http\Middleware\PageSlugMiddleware;

name('tickets.confirmation');
middleware(PageSlugMiddleware::class);

render(function (View $view) {
    return $view->with(['data' => []]);
});
?>

<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 py-6">
        <x-page side="content" slug="tickets.confirmation" :data="$data" />
    </div>
</x-layouts.app>
