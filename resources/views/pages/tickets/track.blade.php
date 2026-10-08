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
    <main id="main-container" class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <header class="cmp-heading mb-4">
                    <h1 class="title-xxxlarge">{{ __('fixcity::ticket.track.title') }}</h1>
                    <p class="subtitle-small mb-0">
                        @if ($authenticatedLookup)
                            {{ __('fixcity::ticket.track.account_subtitle') }}
                        @else
                        {{ __('fixcity::ticket.track.subtitle') }}
                        @endif
                    </p>
                </header>

                @unless ($authenticatedLookup)
                <form method="get" action="{{ url()->current() }}" class="card shadow-sm border-0 mb-4 ticket-track-form">
                    <div class="card-body p-4">
                        <label class="form-label" for="ticket-track-code">
                            {{ __('fixcity::ticket.fields.code.label') }}
                        </label>
                        <div class="input-group ticket-track-search-group">
                            <input
                                id="ticket-track-code"
                                name="code"
                                type="text"
                                class="form-control"
                                value="{{ $code }}"
                                required
                                autocomplete="off"
                                aria-invalid="{{ $notFound ? 'true' : 'false' }}"
                                aria-describedby="ticket-track-help{{ $notFound ? ' ticket-track-error' : '' }}"
                            >
                            <button type="submit" class="btn btn-primary">
                                {{ __('fixcity::ticket.track.submit') }}
                            </button>
                        </div>
                        <div id="ticket-track-help" class="form-text">
                            {{ __('fixcity::ticket.track.help') }}
                        </div>
                    </div>
                </form>
                @endunless

                @if ($notFound)
                    <div id="ticket-track-error" class="alert alert-warning" role="alert">
                        {{ __('fixcity::ticket.track.not_found') }}
                    </div>
                @endif

                @if (is_array($payload))
                    <article class="card shadow-sm border-0" aria-live="polite">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">{{ $payload['title'] }}</h2>
                            <dl class="row mb-0">
                                @if ($payload['code'] !== '')
                                    <dt class="col-sm-3">{{ __('fixcity::ticket.fields.code.label') }}</dt>
                                    <dd class="col-sm-9"><code>{{ $payload['code'] }}</code></dd>
                                @endif
                                <dt class="col-sm-3">{{ __('fixcity::ticket.fields.status.label') }}</dt>
                                <dd class="col-sm-9">{{ TicketStatusEnum::tryFrom($payload['status'])?->getLabel() ?? $payload['status'] }}</dd>
                            </dl>
                            @if ($payload['description'] !== '')
                                <p class="mt-3 mb-0">{{ $payload['description'] }}</p>
                            @endif
                        </div>
                    </article>

                    <section class="mt-4" aria-labelledby="ticket-track-timeline-heading">
                        <h2 id="ticket-track-timeline-heading" class="h4 mb-3">
                            {{ __('fixcity::ticket.track.timeline_title') }}
                        </h2>

                        @if ($timeline === [])
                            <p class="text-muted" role="status">
                                {{ __('fixcity::ticket.track.timeline_empty') }}
                            </p>
                        @else
                            <ol class="list-group list-group-numbered" aria-label="{{ __('fixcity::ticket.track.timeline_title') }}">
                                @foreach ($timeline as $entry)
                                    <li class="list-group-item">
                                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-1">
                                            <strong>{{ $entry['to_label'] }}</strong>
                                            @if ($entry['occurred_at'] !== '')
                                                <time class="text-muted small">{{ $entry['occurred_at'] }}</time>
                                            @endif
                                        </div>
                                        @if ($entry['reason'] !== null && $entry['reason'] !== '')
                                            <p class="mb-0 mt-2">{{ $entry['reason'] }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </section>
                @endif
            </div>
        </div>
    </main>
</x-layouts.app>
