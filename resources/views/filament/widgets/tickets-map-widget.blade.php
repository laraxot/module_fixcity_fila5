<div class="w-full h-[420px] rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800" id="ticket-map-container" data-geojson="{{ route('api.tickets.geojson') }}">
    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-700 dark:text-gray-300 font-medium">
            {{ __('fixcity::map.title') }}
        </div>
        @if(isset($categoryFilter) && is_array($categoryFilter) && count($categoryFilter))
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ __('fixcity::map.filters') }}: {{ implode(', ', $categoryFilter) }}
            </div>
        @endif
    </div>
    <div id="leaflet-map" class="relative w-full h-[372px] bg-gray-50 dark:bg-gray-900"></div>
</div>

@push('scripts')
<script>
(function() {
    const container = document.getElementById('ticket-map-container');
    if (!container) return;
    const geoUrl = container.dataset.geojson || '{{ route("api.tickets.geojson") }}';

    // Load data and initialize map
    fetch(geoUrl)
        .then(r => r.json())
        .then(data => {
            if (!data.features || !data.features.length) {
                document.getElementById('leaflet-map').innerHTML = '<div class="p-4 text-sm text-gray-500">{{ __("fixcity::map.no_data") }}</div>';
                return;
            }
            // Render markers with popup
            const html = '<div class="map-markers">' + data.features.map((f, i) => {
                const props = f.properties || {};
                const link = props.id ? '/it/tickets/' + props.id : '#';
<<<<<<< HEAD
                return '<div class="marker-item p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" data-id="' + (props.id || i) + '" data-lat="' + (f.geometry.coordinates[1] || 0) + '" data-lng="' + (f.geometry.coordinates[0] || 0) + '">' +
                    '<h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">' + (props.name || props.title || '{{ __("fixcity::map.unknown") }}') + '</h4>' +
                    '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 mb-2">' + (props.status || '') + '</span>' +
                    '<a href="' + link + '" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 text-sm font-medium">{{ __("fixcity::map.view_detail") }}</a>' +
=======
                return '<div class="marker-item" data-id="' + (props.id || i) + '" data-lat="' + (f.geometry.coordinates[1] || 0) + '" data-lng="' + (f.geometry.coordinates[0] || 0) + '">' +
                    '<h4>' + (props.name || props.title || '{{ __("fixcity::map.unknown") }}') + '</h4>' +
                    '<span class="badge">' + (props.status || '') + '</span>' +
                    '<a href="' + link + '">{{ __("fixcity::map.view_detail") }}</a>' +
>>>>>>> laraxot/dev
                '</div>';
            }).join('') + '</div>';
            document.getElementById('leaflet-map').innerHTML = html;
        });
})();
</script>
@endpush
