<?php

declare(strict_types=1);

use Illuminate\View\View;
use Modules\Cms\Http\Middleware\PageSlugMiddleware;
use Modules\Cms\Models\Page;
use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\GetPublicTicketByCodeAction;
use Modules\Fixcity\Models\Ticket;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('tickets.confirmation');
middleware('throttle:10,1');
middleware(PageSlugMiddleware::class);

render(function (View $view): View {
    $confirmation = app(BuildTicketConfirmationDataAction::class);
    $data = $confirmation->pullFromSession();

    if ($data === []) {
        $code = request()->query('code');
        $codeString = is_string($code) ? $code : null;
        $ticket = app(GetPublicTicketByCodeAction::class)->execute($codeString);
        if ($ticket instanceof Ticket) {
            $data = $confirmation->execute($ticket);
        }
    }

    return $view->with(['data' => $data]);
});
?>

@php
    $cmsPage = Page::findUniqueBySlug('tickets.confirmation');
    $pageTitle = $cmsPage?->getTranslation('title', app()->getLocale(), false)
        ?? __('fixcity::ticket_confirmation.title');
@endphp

<x-layouts.app :title="$pageTitle">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <x-page side="content" slug="tickets.confirmation" :data="$data" />
    </div>
</x-layouts.app>
