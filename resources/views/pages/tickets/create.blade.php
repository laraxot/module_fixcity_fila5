<?php

use Illuminate\View\View;
use Modules\Fixcity\Models\Ticket; 
use function Laravel\Folio\{middleware, name, render, withTrashed};
use Modules\Cms\Http\Middleware\PageSlugMiddleware;

withTrashed();

name('tickets.create');
middleware(PageSlugMiddleware::class);


render(function (View $view) {
    $view_params = [
        'data' => [],
    ];

    return $view->with($view_params);
});
?>


<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 py-6">
        <x-page side="content" slug="tickets.create" :data="$data" />
    </div>
</x-layouts.app>