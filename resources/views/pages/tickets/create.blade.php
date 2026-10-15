<?php

declare(strict_types=1);

use Illuminate\View\View;
use Modules\Cms\Http\Middleware\PageSlugMiddleware;
use Modules\Cms\Http\Middleware\SetFolioLocale;
use Modules\Cms\Models\Page;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('tickets.create');
middleware(PageSlugMiddleware::class);
middleware(SetFolioLocale::class);
middleware('auth');

render(function (View $view): View {
    return $view->with(['data' => []]);
});
?>

@php
    $cmsPage = Page::findUniqueBySlug('tickets.create');
    $pageTitle = $cmsPage?->getTranslation('title', app()->getLocale(), false)
        ?? __('fixcity::ticket.page.title.label');
@endphp

<x-layouts.app :title="$pageTitle">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <x-page side="content" slug="tickets.create" :data="$data" />
    </div>
</x-layouts.app>
