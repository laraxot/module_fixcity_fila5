<?php

declare(strict_types=1);

?>
<div class="cmp-ticket-citizen-rating">
    @if ($showPrompt)
        <div class="alert alert-info border-0 shadow-sm mb-4" role="region" aria-labelledby="ticket-rating-prompt-title">
            <h2 id="ticket-rating-prompt-title" class="title-small-semi-bold mb-2">
                {{ __('fixcity::ticket_citizen_rating.prompt.title.label') }}
            </h2>
            <p class="text-paragraph-small mb-3">
                {{ __('fixcity::ticket_citizen_rating.prompt.description.label') }}
            </p>
            <fieldset class="rating mb-3">
                <legend class="visually-hidden">
                    {{ __('fixcity::ticket_citizen_rating.prompt.stars_legend.label') }}
                </legend>
                @for ($star = 5; $star >= 1; $star--)
                    <input
                        type="radio"
                        id="ticket-rating-star-{{ $ticketId }}-{{ $star }}"
                        name="ticket-rating-{{ $ticketId }}"
                        value="{{ $star }}"
                        wire:model.live="selectedRating"
                    >
                    <label
                        for="ticket-rating-star-{{ $ticketId }}-{{ $star }}"
                        title="{{ __('fixcity::rating.fields.star.labels.'.$star.'.label') }}"
                    >
                        <span class="visually-hidden">{{ __('fixcity::rating.fields.star.labels.'.$star.'.label') }}</span>
                    </label>
                @endfor
            </fieldset>
            <x-filament::button type="button" wire:click="submitRating" wire:loading.attr="disabled">
                {{ __('fixcity::ticket_citizen_rating.actions.submit.label') }}
            </x-filament::button>
        </div>
    @elseif ($submitted)
        <div class="alert alert-success border-0 mb-4" role="status">
            {{ __('fixcity::ticket_citizen_rating.prompt.thank_you.label') }}
        </div>
    @endif
</div>
