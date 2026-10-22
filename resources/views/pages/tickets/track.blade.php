<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Fixcity\Actions\BuildTicketPublicDetailsPayloadAction;
use Modules\Fixcity\Actions\BuildTicketTimelineAction;
use Modules\Fixcity\Actions\GetPublicTicketByCodeAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('tickets.track');
middleware('throttle:10,1');

render(function (View $view): View {
    $code = request()->query('code');
    $codeString = is_string($code) ? trim($code) : '';
    $ticketId = request()->query('ticket_id');
    $ticketIdString = is_string($ticketId) || is_int($ticketId) ? (string) $ticketId : '';
    $authenticatedLookup = $ticketIdString !== '';
    $ticket = null;

    if ($authenticatedLookup) {
        $user = auth()->user();
        abort_unless($user instanceof UserContract, 403);
        abort_unless(ctype_digit($ticketIdString), 404);
        $candidate = Ticket::query()->find((int) $ticketIdString);
        abort_unless($candidate instanceof Ticket, 404);
        Gate::authorize('view', $candidate);
        $ticket = $candidate;
    } elseif ($codeString !== '') {
        $ticket = app(GetPublicTicketByCodeAction::class)->execute($codeString);
    }
    $payload = $ticket instanceof Ticket
        ? app(BuildTicketPublicDetailsPayloadAction::class)->execute($ticket)
        : null;
    $timeline = $ticket instanceof Ticket
        ? app(BuildTicketTimelineAction::class)->execute($ticket)
        : [];

    return $view->with([
        'code' => $codeString,
        'authenticatedLookup' => $authenticatedLookup,
        'ticket' => $ticket,
        'payload' => $payload,
        'timeline' => $timeline,
        'notFound' => $codeString !== '' && ! $ticket instanceof Ticket,
    ]);
});
?>

<x-layouts.app :title="__('fixcity::ticket.track.title')">
            <div class="bg-gradient-to-b from-[var(--fc-green-dark)] via-[var(--fc-green-dark)] to-[#0a2e2e] px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <header class="mb-10">
                <p class="fc-eyebrow">Segnalazione</p>
                <h1 class="font-serif text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('fixcity::ticket.track.title') }}</h1>
                <p class="mt-3 text-lg leading-8 text-white/90">
                        @if ($authenticatedLookup)
                            {{ __('fixcity::ticket.track.account_subtitle') }}
                        @else
                        {{ __('fixcity::ticket.track.subtitle') }}
                        @endif
                </p>
            </header>

                @unless ($authenticatedLookup)
            <form method="get" action="{{ url()->current() }}" class="fc-card bg-white rounded-xl border border-[var(--fc-line)] p-6 shadow-sm sm:p-8">
                <div>
                    <label class="block text-base font-semibold text-[var(--fc-ink)]" for="ticket-track-code">
                            {{ __('fixcity::ticket.fields.code.label') }}
                    </label>
                    <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                            <input
                                id="ticket-track-code"
                                name="code"
                                type="text"
                                class="fc-input min-h-12 w-full rounded-lg border border-[var(--fc-line)] bg-white px-4 text-base text-[var(--fc-ink)] shadow-sm outline-none transition placeholder:text-[var(--fc-ink-soft)] focus:border-[var(--fc-green)] focus:ring-4 focus:ring-[var(--fc-green)]/20"
                                value="{{ $code }}"
                                required
                                autocomplete="off"
                                aria-invalid="{{ $notFound ? 'true' : 'false' }}"
                                aria-describedby="ticket-track-help{{ $notFound ? ' ticket-track-error' : '' }}"
                            >
                            <button type="submit" class="fc-button fc-button--sun min-h-12 rounded-lg px-5 py-3 font-semibold shadow-sm transition">
                                {{ __('fixcity::ticket.track.submit') }}
                            </button>
                    </div>
                    <p id="ticket-track-help" class="mt-2 text-sm text-[var(--fc-ink-soft)]">
                            {{ __('fixcity::ticket.track.help') }}
                    </p>
                </div>
            </form>
                @endunless

                @if ($notFound)
                    <div id="ticket-track-error" class="fc-alert fc-alert--warning mt-4 rounded-lg border border-[var(--fc-sun)] bg-[var(--fc-mint)] p-4 text-[var(--fc-ink)]" role="alert">
                        {{ __('fixcity::ticket.track.not_found') }}
                    </div>
                @endif

                @if (is_array($payload))
                    <article class="fc-card mt-8 rounded-xl border border-[var(--fc-line)] bg-white p-6 shadow-sm sm:p-8" aria-live="polite">
                            <h2 class="text-2xl font-bold text-[var(--fc-ink)]">{{ $payload['title'] }}</h2>
                            <dl class="mt-6 grid gap-4 border-y border-[var(--fc-line)] py-5 sm:grid-cols-[minmax(0,1fr)_2fr]">
                                @if ($payload['code'] !== '')
                                    <dt class="font-semibold text-[var(--fc-ink-soft)]">{{ __('fixcity::ticket.fields.code.label') }}</dt>
                                    <dd class="font-mono text-[var(--fc-ink)]">{{ $payload['code'] }}</dd>
                                @endif
                                <dt class="font-semibold text-[var(--fc-ink-soft)]">{{ __('fixcity::ticket.fields.status.label') }}</dt>
                                <dd class="font-semibold text-[var(--fc-ink)]">{{ TicketStatusEnum::tryFrom($payload['status'])?->getLabel() ?? $payload['status'] }}</dd>
                            </dl>
                            @if ($payload['description'] !== '')
                                <p class="mt-5 leading-7 text-[var(--fc-ink)]/90">{{ $payload['description'] }}</p>
                            @endif
                    </article>

                    <section class="mt-4" aria-labelledby="ticket-track-timeline-heading">
                        <h2 id="ticket-track-timeline-heading" class="text-2xl font-bold text-[var(--fc-ink)]">
                            {{ __('fixcity::ticket.track.timeline_title') }}
                        </h2>

                        @if ($timeline === [])
                            <p class="mt-4 text-[var(--fc-ink-soft)]" role="status">
                                {{ __('fixcity::ticket.track.timeline_empty') }}
                            </p>
                        @else
                            <ol class="mt-4 space-y-3" aria-label="{{ __('fixcity::ticket.track.timeline_title') }}">
                                @foreach ($timeline as $entry)
                                    <li class="fc-card rounded-xl border border-[var(--fc-line)] bg-white p-5 shadow-sm">
                                        <div class="flex flex-col justify-between gap-1 sm:flex-row">
                                            <strong class="text-[var(--fc-ink)]">{{ $entry['to_label'] }}</strong>
                                            @if ($entry['occurred_at'] !== '')
                                                <time class="text-sm text-[var(--fc-ink-soft)]">{{ $entry['occurred_at'] }}</time>
                                            @endif
                                        </div>
                                        @if ($entry['reason'] !== null && $entry['reason'] !== '')
                                            <p class="mt-2 leading-7 text-[var(--fc-ink)]/90">{{ $entry['reason'] }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </section>
                @endif
        </div>
            </div>
</x-layouts.app>
