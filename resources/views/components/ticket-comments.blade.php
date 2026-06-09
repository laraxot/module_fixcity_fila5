{{--
    Ticket comments section using Spatie Comments Livewire

    Shows:
    - List of existing comments
    - Livewire form to add new comment (if authenticated)
    - Fallback message for guests
--}}
@props([
    'ticket' => null,
])

@php
    // Ensure ticket is loaded with comments
    if (!$ticket->relationLoaded('comments')) {
        $ticket->load('comments');
    }
@endphp

<div class="ticket-comments-section mt-5 pt-4 border-top">

    {{-- Comments list using Spatie Livewire --}}
    @livewire('comments::list-comments', [
        'model' => $ticket,
        'subject' => 'Ticket #' . $ticket->id,
        'withReactions' => false,
    ])

    {{-- Comment form for authenticated users --}}
    @auth
        <div class="comment-form-section mt-4">
            <h5 class="mb-3">{{ __('cruds.ticket.fields.comments') }}</h5>
            @livewire('comments::create-comment', [
                'model' => $ticket
            ])
        </div>
    @else
        <div class="alert alert-info mt-4">
            {{ __('cruds.ticket.login_to_comment') }}
            <a href="{{ route('login') }}" class="alert-link">
                {{ __('global.login') }}
            </a>
        </div>
    @endauth
</div>