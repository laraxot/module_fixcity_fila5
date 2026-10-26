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
    <div class="bg-slate-50 px-4 py-10 sm:px-6 lg:px-8" id="ticket-detail" aria-labelledby="ticket-detail-title">
        <nav class="mx-auto mb-8 max-w-3xl" aria-label="{{ __('pub_theme::home.list.breadcrumb_aria') }}">
            <ol class="flex flex-wrap gap-2 text-sm text-slate-700">
                <li><a class="underline decoration-slate-400 underline-offset-4 hover:text-sky-800" href="{{ url('/'.app()->getLocale()) }}">{{ __('pub_theme::footer.home') }}</a></li>
                <li aria-hidden="true">/</li>
                <li><a class="underline decoration-slate-400 underline-offset-4 hover:text-sky-800" href="{{ url('/'.app()->getLocale().'/tickets') }}">{{ __('pub_theme::home.list.title') }}</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">{{ __('pub_theme::home.map.details') }}</li>
            </ol>
        </nav>
        <article class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-semibold text-slate-700">{{ __('pub_theme::home.map.details') }}</p>
                <h1 id="ticket-detail-title" class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $ticket->name }}</h1>
                <span class="mt-4 inline-flex rounded-full bg-slate-200 px-3 py-1 text-sm font-semibold text-slate-900">{{ $status }}</span>
                <dl class="mt-6 grid gap-4 border-y border-slate-200 py-5 sm:grid-cols-[minmax(0,1fr)_2fr]">
                            <dt class="font-semibold text-slate-700">{{ __('pub_theme::home.map.type') }}</dt>
                            <dd class="text-slate-950">{{ $type ?: __('pub_theme::home.map.not_available') }}</dd>
                            <dt class="font-semibold text-slate-700">{{ __('pub_theme::home.map.address') }}</dt>
                            <dd class="text-slate-950">{{ $address ?: __('pub_theme::home.map.not_available') }}</dd>
                            <dt class="font-semibold text-slate-700">{{ __('pub_theme::home.map.description') }}</dt>
                            <dd class="leading-7 text-slate-800">{{ $ticket->content ?: __('pub_theme::home.map.not_available') }}</dd>
                </dl>
                <a class="mt-6 inline-flex min-h-12 items-center rounded-lg border-2 border-sky-800 px-5 py-3 font-semibold text-sky-900 transition hover:bg-sky-50 focus:outline-none focus:ring-4 focus:ring-sky-800/30" href="{{ url('/'.app()->getLocale().'/tickets') }}">{{ __('pub_theme::home.map.back_to_map') }}</a>

        @if ($ticket instanceof \Modules\Fixcity\Models\Ticket)
            @php
                $timeline = app(\Modules\Fixcity\Actions\BuildTicketTimelineAction::class)->execute($ticket);
            @endphp
            @if ($timeline !== [])
                <section class="mt-8" aria-labelledby="ticket-passaggi-heading">
                    <h2 id="ticket-passaggi-heading" class="text-xl font-bold text-slate-950">Passaggi</h2>
                    <ol class="mt-4 space-y-3" aria-label="Passaggi del ticket">
                        @foreach ($timeline as $entry)
                            <li class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex flex-col justify-between gap-1 sm:flex-row">
                    <strong class="text-slate-950">{{ $entry['to_label'] ?? $entry['reason'] ?? 'Passaggio' }}</strong>
                    @if (filled($entry['from_label'] ?? null))
                        <span class="text-sm text-slate-600">({{ $entry['from_label'] }} → {{ $entry['to_label'] }})</span>
                    @endif
                                    @if ($entry['occurred_date'] !== '')
                                        <time class="text-sm text-slate-600" datetime="{{ $entry['occurred_on'] }}">{{ $entry['occurred_date'] }}</time>
                                    @endif
                                </div>
                                @if ($entry['reason'] !== null && $entry['reason'] !== '')
                                    <p class="mt-2 leading-7 text-slate-700">{{ $entry['reason'] }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            <x-ticket-comments :ticket="$ticket" />
        @endif
        </article>
    </div>
</x-pub_theme::layouts.app>
