<?php

declare(strict_types=1);

use Illuminate\View\View;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Models\Ticket;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('tickets.detail');

render(function (View $view, int|string $id): View {
    $ticket = app(BuildPublicTicketsQueryAction::class)->execute()->whereKey($id)->first();
    abort_unless($ticket instanceof Ticket, 404);
    $location = is_array($ticket->location) ? $ticket->location : [];

    return $view->with([
        'ticket' => $ticket,
        'address' => (string) ($location['address'] ?? $location['display_name'] ?? $location['city'] ?? ''),
        'type' => (string) ($ticket->typeLabel ?: $ticket->type?->value ?? ''),
        'status' => $ticket->status?->getLabel() ?? $ticket->resolveTicketStatusValue(),
    ]);
});
?>

<x-pub_theme::layouts.app :title="$ticket->name">
    <main class="container py-4 py-lg-5" id="ticket-detail" aria-labelledby="ticket-detail-title">
        <nav class="mb-4" aria-label="{{ __('pub_theme::home.list.breadcrumb_aria') }}">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/'.app()->getLocale()) }}">{{ __('pub_theme::footer.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/'.app()->getLocale().'/tickets') }}">{{ __('pub_theme::home.list.title') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('pub_theme::home.map.details') }}</li>
            </ol>
        </nav>
        <article class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <p class="text-uppercase text-secondary fw-semibold mb-2">{{ __('pub_theme::home.map.details') }}</p>
                <h1 id="ticket-detail-title" class="title-xxxlarge mb-3">{{ $ticket->name }}</h1>
                <span class="badge text-bg-secondary mb-4">{{ $status }}</span>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">{{ __('pub_theme::home.map.type') }}</dt>
                            <dd class="col-sm-8">{{ $type ?: __('pub_theme::home.map.not_available') }}</dd>
                            <dt class="col-sm-4">{{ __('pub_theme::home.map.address') }}</dt>
                            <dd class="col-sm-8">{{ $address ?: __('pub_theme::home.map.not_available') }}</dd>
                            <dt class="col-sm-4">{{ __('pub_theme::home.map.description') }}</dt>
                            <dd class="col-sm-8">{{ $ticket->content ?: __('pub_theme::home.map.not_available') }}</dd>
                        </dl>
                    </div>
                </div>
                <a class="btn btn-outline-primary mt-4" href="{{ url('/'.app()->getLocale().'/tickets') }}">{{ __('pub_theme::home.map.back_to_map') }}</a>
            </div>
        </article>
    </main>
</x-pub_theme::layouts.app>
