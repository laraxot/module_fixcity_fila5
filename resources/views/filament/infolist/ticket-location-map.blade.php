<?php

declare(strict_types=1);

use Modules\Fixcity\Models\Ticket;

?>
@php
    /** @var Ticket|null $record */
    $record = $record ?? null;
    $detailMode = (bool) ($detailMode ?? false);
    $location = $record && is_array($record->location) ? $record->location : [];
    $lat = $location['lat'] ?? $location['latitude'] ?? null;
    $lng = $location['lng'] ?? $location['longitude'] ?? null;
@endphp

@if ($record && $lat !== null && $lat !== '' && $lng !== null && $lng !== '')
    <div class="ticket-location-map rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <map-lit
            lat="{{ $lat }}"
            lng="{{ $lng }}"
            height="320px"
            @if ($detailMode)
                detail-mode
                ticket-id="{{ $record->id }}"
            @else
                data-url="/data/tickets.json"
            @endif
        ></map-lit>
    </div>
@endif
