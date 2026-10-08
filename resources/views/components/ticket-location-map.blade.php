{{-- Static map component for ticket detail --}}
@props([
    'latitude' => null,
    'longitude' => null,
    'ticketTitle' => null
])

@if($latitude && $longitude)
<div class="ratio ratio-16x9 mb-4">
    <img
        src="https://staticmap.openstreetmap.de/staticmap.php?center={{ $latitude }},{{ $longitude }}&zoom=15&size=600x300&markers={{ $latitude }},{{ $longitude }},red-pushpin"
        alt="Location map for {{ $ticketTitle ?? 'ticket' }}"
        class="img-fluid rounded"
        loading="lazy"
    >
</div>
@else
<div class="alert alert-info text-center py-4">
    <svg class="icon icon-lg mb-2">
        <use href="/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg#it-location"></use>
    </svg>
    <p class="mb-0">{{ __('pub_theme::ui.ticket.map_location_not_available') }}</p>
</div>
@endif