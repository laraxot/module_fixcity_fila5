<?php

declare(strict_types=1);

use Modules\Comment\Http\Livewire\CommentsComponent;
use Modules\Fixcity\Models\Ticket;

?>
@php
    /** @var Ticket|null $record */
    $record = $record ?? null;
@endphp

@if ($record)
    <div class="ticket-comments-fo">
        @auth
            @livewire(CommentsComponent::class, [
                'model' => $record,
                'readOnly' => false,
                'hideNotificationOptions' => true,
                'noReplies' => true,
                'noReactions' => true,
            ], key('ticket-comments-'.$record->getKey()))
        @endauth

        @guest
            @livewire(CommentsComponent::class, [
                'model' => $record,
                'readOnly' => true,
                'hideNotificationOptions' => true,
                'noReplies' => true,
                'noReactions' => true,
            ], key('ticket-comments-ro-'.$record->getKey()))
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ url('/'.app()->getLocale().'/auth/login') }}" class="underline">
                    {{ __('comment::txt.log-in-for-comment') }}
                </a>
            </p>
        @endguest
    </div>
@endif
