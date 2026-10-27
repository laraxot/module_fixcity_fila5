{{--
    BO timeline — TicketActivity via BuildTicketTimelineAction.
--}}
@php
    /** @var list<array{occurred_at: string, occurred_date: string, occurred_on: string, from_label: string|null, to_label: string|null, reason: string|null}> $steps */
    $steps = $steps ?? [];
@endphp

<section class="fi-wi-timeline-stepper space-y-3" aria-label="{{ __('fixcity::ticket_detail.status.history') }}">
    @forelse ($steps as $step)
        <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                <strong class="text-sm text-gray-950 dark:text-white">{{ $step['to_label'] }}</strong>
                @if (($step['occurred_at'] ?? '') !== '')
                    <time class="text-xs text-gray-600 dark:text-gray-400" datetime="{{ $step['occurred_on'] ?? '' }}">
                        {{ $step['occurred_at'] }}
                    </time>
                @endif
            </div>
            @if (filled($step['from_label'] ?? null))
                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                    {{ __('fixcity::ticket_detail.status.previous', ['status' => $step['from_label']]) }}
                </p>
            @endif
            @if (filled($step['reason'] ?? null))
                <p class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $step['reason'] }}</p>
            @endif
        </article>
    @empty
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('fixcity::ticket.track.timeline_empty') }}
        </p>
    @endforelse
</section>
