<?php

declare(strict_types=1);

?>
<div class="container py-4 ticket-view-widget">
    <x-filament-widgets::widget class="fi-wi-ticket-view">
        @if ($this->getInfolistRecord())
            {{ $this->infolist }}

            @if ($this->ticket?->needsCitizenRatingPrompt())
                @livewire(\Modules\Fixcity\Filament\Widgets\TicketCitizenRatingPromptWidget::class, ['ticketId' => (int) $this->ticket->getKey()], key('ticket-rating-'.$this->ticket->getKey()))
            @endif

            @if (auth()->check())
                <section class="mt-4" aria-labelledby="ticket-follow-heading">
                    <h2 id="ticket-follow-heading" class="h5">
                        {{ __('fixcity::ticket.subscription.heading') }}
                    </h2>
                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        wire:click="setFollowing({{ $this->isFollowing ? 'false' : 'true' }})"
                        wire:loading.attr="disabled"
                        wire:target="setFollowing"
                        aria-pressed="{{ $this->isFollowing ? 'true' : 'false' }}"
                    >
                        {{ $this->isFollowing ? __('fixcity::ticket.subscription.unfollow') : __('fixcity::ticket.subscription.follow') }}
                    </button>
                    <p class="mt-2 mb-0" role="status" aria-live="polite">
                        {{ $this->isFollowing ? __('fixcity::ticket.subscription.following_status') : __('fixcity::ticket.subscription.not_following_status') }}
                    </p>
                </section>
            @else
                <p class="mt-4">
                    <a href="{{ LaravelLocalization::localizeUrl('/auth/login') }}">
                        {{ __('fixcity::ticket.subscription.login_to_follow') }}
                    </a>
                </p>
            @endif
        @else
            <div class="alert alert-warning shadow-sm border-0" role="alert">
                {{ __('fixcity::ticket.detail.not_found.label') }}
            </div>
        @endif
    </x-filament-widgets::widget>
</div>
