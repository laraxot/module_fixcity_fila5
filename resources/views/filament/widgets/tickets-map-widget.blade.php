<div class="w-full h-[420px] rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            Mappa segnalazioni
        </div>
        @if(isset($categoryFilter) && is_array($categoryFilter) && count($categoryFilter))
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Filtri: {{ implode(', ', $categoryFilter) }}
            </div>
        @endif
    </div>
    <div class="relative w-full h-[372px]">
        {{-- Placeholder implementativo: qui integreremo la mappa (Leaflet/Mapbox/Google) --}}
        <div class="absolute inset-0 grid place-items-center text-gray-500 dark:text-gray-400">
            <div class="text-center">
                <svg class="mx-auto h-10 w-10 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                <div class="text-sm">
                    @if(isset($latitude, $longitude) && $latitude && $longitude)
                        Centro mappa: {{ number_format($latitude, 5) }}, {{ number_format($longitude, 5) }}
                    @else
                        In attesa della posizione…
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


