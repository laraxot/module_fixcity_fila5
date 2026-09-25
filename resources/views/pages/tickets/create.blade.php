<?php

<<<<<<< HEAD
declare(strict_types=1);

use Illuminate\View\View;
use function Laravel\Folio\{middleware, name, render};
use Modules\Cms\Http\Middleware\PageSlugMiddleware;

name('tickets.create');
middleware(PageSlugMiddleware::class);

render(function (View $view): View {
    return $view->with(['data' => []]);
});
?>

=======
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


>>>>>>> 54ffa6d (.)
<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 py-6">
        <x-page side="content" slug="tickets.create" :data="$data" />
    </div>
<<<<<<< HEAD
</x-layouts.app>
=======
</x-layouts.app>
>>>>>>> 54ffa6d (.)
