<?php

declare(strict_types=1);

?>
<div class="container py-4 ticket-view-widget">
    <x-filament-widgets::widget class="fi-wi-ticket-view">
        @if ($this->getInfolistRecord())
            {{ $this->infolist }}
        @else
            <div class="alert alert-warning shadow-sm border-0" role="alert">
                {{ __('fixcity::ticket.detail.not_found.label') }}
            </div>
        @endif
    </x-filament-widgets::widget>
</div>
