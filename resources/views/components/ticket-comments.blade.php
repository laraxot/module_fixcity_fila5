{{--
    Ticket comments FO — CommentsWidget (Filament). No Http/Livewire.
--}}
@props([
    'ticket' => null,
])

@php
    $localize = static fn (string $path): string => \LaravelLocalization::localizeURL($path);
@endphp

<div class="ticket-comments-section mt-5 pt-4 border-top">
    @auth
        @can('update', $ticket)
            @livewire(\Modules\Comment\Filament\Widgets\Commentable\CommentsWidget::class, [
                'commentableType' => $ticket::class,
                'commentableKey' => (string) $ticket->getKey(),
                'readOnly' => false,
                'hideNotifications' => true,
                'noReplies' => false,
                'noReactions' => true,
            ], key('ticket-comments-'.$ticket->getKey()))
        @endcan
    @endauth

    @guest
        <div class="alert alert-info mt-4">
            <a href="{{ $localize('/auth/login') }}" class="alert-link">
                {{ __('comment::txt.log-in-for-comment') }}
            </a>
        </div>
    @endguest
</div>
