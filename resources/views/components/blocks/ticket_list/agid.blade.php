<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Enums\TicketStatusEnum;

new class extends Component
{
    use WithPagination;

    public $locationSet = false;
    public $userLatitude;
    public $userLongitude;
    public $search = '';
    public $selectedCategories = [];
    public $selectedStatus = '';
    public $resolvedTicketsCount;
    public $perPage = 3;
    public $expandedTickets = [];
    public $filteredCount = 0;
    public $currentTab = 'lista'; // 'lista' or 'mappa'

    public function toggleTicket($ticketId)
    {
        if (in_array($ticketId, $this->expandedTickets)) {
            $this->expandedTickets = array_diff($this->expandedTickets, [$ticketId]);
        } else {
            $this->expandedTickets[] = $ticketId;
        }
    }

    public function switchTab($tab)
    {
        $this->currentTab = $tab;
    }

    public function mount()
    {
        $this->resolvedTicketsCount = Ticket::query()
            ->where(function ($q) {
                $q->whereIn('status', TicketStatusEnum::canViewByAll())
                    ->orWhere('created_by', authId())
                    ->orWhere('updated_by', authId());
            })
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->count();
        $this->dispatch('get-user-location');
    }

    public function setUserLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;
        $this->locationSet = true;

        $this->dispatch('updateMapCenter', $latitude, $longitude)
            ->to(\Modules\Fixcity\Filament\Widgets\TicketsMapWidget::class);
    }

    public function notSetUserLocation()
    {
        $this->userLatitude = 41.125278;
        $this->userLongitude = 16.866667;
        $this->locationSet = true;

        $this->dispatch('updateMapCenter', $this->userLatitude, $this->userLongitude)
            ->to(\Modules\Fixcity\Filament\Widgets\TicketsMapWidget::class);
    }

    public function loadMore()
    {
        $this->perPage += 3;
    }

    public function updatedSelectedCategories($value)
    {
        Log::error('Categories updated', ['value' => $this->selectedCategories]);
        $this->dispatch('categoryFilterUpdated')
            ->to(\Modules\Fixcity\Filament\Widgets\TicketsMapWidget::class);
    }

    public function clearCategories()
    {
        $this->selectedCategories = [];
        $this->dispatch('categoryFilterUpdated')
            ->to(\Modules\Fixcity\Filament\Widgets\TicketsMapWidget::class);
    }

    private function getAddress($lat, $lon): string
    {
        Log::error('Getting address for coordinates', ['lat' => $lat, 'lon' => $lon]);

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'FixCity/1.0 (your@email.com)', // Replace with your actual app name and contact email
                'Accept-Language' => 'it' // For Italian results
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lon,
                'zoom' => 18,
                'addressdetails' => 1,
            ]);

            Log::error('API Response', ['response' => $response->json()]);

            if ($response->successful()) {
                $data = $response->json();
                $address = $data['display_name'] ?? 'Indirizzo non trovato';
                Log::error('Address found', ['address' => $address]);
                return $address;
            }
        } catch (\Exception $e) {
            Log::error('Error getting address', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lat' => $lat,
                'lon' => $lon
            ]);
        }

        Log::error('Returning default address');
        return 'Indirizzo non trovato';
    }

    public function with(): array
    {
        $categories = collect(TicketTypeEnum::cases())->map(function ($type) {
            $count = Ticket::where('type', $type->value)
                ->where(function ($q) {
                    $q->whereIn('status', TicketStatusEnum::canViewByAll())
                        ->orWhere('created_by', authId())
                        ->orWhere('updated_by', authId());
                })
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->count();

            return [
                'label' => $type->getLabel(),
                'value' => $type->value,
                'count' => $count
            ];
        });

        $query = Ticket::query()
            ->where(function ($q) {
                $q->whereIn('status', TicketStatusEnum::canViewByAll())
                    ->orWhere('created_by', authId())
                    ->orWhere('updated_by', authId());
            })
            ->select('id', 'name', 'slug', 'type', 'content', 'created_at', 'latitude', 'longitude')
            ->with('media')
            ->latest();

        if (count($this->selectedCategories) > 0) {
            $query->whereIn('type', $this->selectedCategories);
        }

        $this->filteredCount = $query->count();

        $tickets = $query->take($this->perPage)->get();

        $hasMorePages = Ticket::count() > $this->perPage;

        return [
            'categories' => $categories,
            'tickets' => $tickets,
            'hasMorePages' => $hasMorePages,
            'filteredCount' => $this->filteredCount,
            'userLatitude' => $this->userLatitude,
            'userLongitude' => $this->userLongitude,
        ];
    }
}
?>

@volt('ticket_list')

<div class="space-y-8">
    {{-- Statistics section --}}
    <section class="fi-section">
        <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900 dark:to-primary-800 p-6 rounded-lg border border-primary-200 dark:border-primary-700">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.23a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-primary-900 dark:text-primary-100">
                        {{ number_format($resolvedTicketsCount) }} segnalazioni
                    </h2>
                    <p class="text-primary-700 dark:text-primary-300">
                        risolte negli ultimi 12 mesi dal nostro team
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Filters and content section --}}
    <section class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Sidebar with filters --}}
        <aside class="lg:col-span-1" role="complementary" aria-label="Filtri segnalazioni">
            <div class="fi-section sticky top-4">
                <h3 class="fi-section-header">Filtra per categoria</h3>
                <p class="fi-section-description">Seleziona una o più categorie per filtrare le segnalazioni</p>

                <fieldset class="space-y-3">
                    <legend class="sr-only">Seleziona categorie</legend>
                    @foreach($categories as $category)
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="category-{{ $category['value'] }}"
                            class="filament-checkbox"
                            wire:model.live="selectedCategories"
                            value="{{ $category['value'] }}"
                        />
                        <label for="category-{{ $category['value'] }}" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            {{ $category['label'] }}
                            <span class="ml-1 px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded-full">
                                {{ $category['count'] }}
                            </span>
                        </label>
                    </div>
                    @endforeach
                </fieldset>

                @if(count($selectedCategories) > 0)
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                    <button
                        wire:click="clearCategories"
                        class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 agid-focus">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Rimuovi tutti i filtri
                    </button>
                </div>
                @endif
            </div>
        </aside>

        {{-- Main content area --}}
        <main class="lg:col-span-3" role="main" aria-label="Elenco segnalazioni">
            {{-- Results header --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ number_format($filteredCount) }} segnalazioni trovate
                    </h2>
                    @if(count($selectedCategories) > 0)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Filtrate per: {{ implode(', ', array_map(fn($cat) => collect($categories)->firstWhere('value', $cat)['label'] ?? $cat, $selectedCategories)) }}
                    </p>
                    @endif
                </div>
            </div>

            {{-- Tab navigation --}}
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6" role="tablist" aria-label="Modalità visualizzazione">
                <nav class="-mb-px flex space-x-8">
                    <button
                        wire:click="switchTab('lista')"
                        class="py-2 px-1 border-b-2 font-medium text-sm agid-focus @if($currentTab === 'lista') border-primary-500 text-primary-600 dark:text-primary-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif"
                        role="tab"
                        aria-selected="{{ $currentTab === 'lista' ? 'true' : 'false' }}"
                        aria-controls="lista-panel"
                    >
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Lista
                    </button>
                    <button
                        wire:click="switchTab('mappa')"
                        class="py-2 px-1 border-b-2 font-medium text-sm agid-focus @if($currentTab === 'mappa') border-primary-500 text-primary-600 dark:text-primary-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif"
                        role="tab"
                        aria-selected="{{ $currentTab === 'mappa' ? 'true' : 'false' }}"
                        aria-controls="mappa-panel"
                    >
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Mappa
                    </button>
                </nav>
            </div>

            {{-- Tab panels --}}
            <div class="space-y-6">
                {{-- Lista panel --}}
                @if($currentTab === 'lista')
                <div id="lista-panel" role="tabpanel" aria-labelledby="lista-tab">
                    @if($tickets->count() > 0)
                        <div class="space-y-6">
                            @foreach($tickets as $ticket)
                            <article class="fi-card group" aria-labelledby="ticket-{{ $ticket->id }}-title">
                                <div class="fi-card-content">
                                    <header class="mb-4">
                                        <h3 id="ticket-{{ $ticket->id }}-title" class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                            <a
                                                href="{{ route('ticket.view', ['slug' => $ticket->slug]) }}"
                                                class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 agid-focus"
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                {{ $ticket->name }}
                                                <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd" />
                                                    <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="sr-only">(si apre in una nuova finestra)</span>
                                            </a>
                                        </h3>

                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                                <time datetime="{{ $ticket->created_at->toISOString() }}">
                                                    {{ $ticket->created_at->format('d/m/Y') }}
                                                </time>
                                            </div>

                                            @if($ticket->type)
                                            <span class="filament-badge filament-badge-primary">
                                                {{ $ticket->type->getLabel() }}
                                            </span>
                                            @endif
                                        </div>
                                    </header>

                                    @if(in_array($ticket->id, $expandedTickets))
                                    <div class="mt-4 space-y-4" id="ticket-{{ $ticket->id }}-details">
                                        {{-- Address --}}
                                        @if($ticket->latitude && $ticket->longitude)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Indirizzo:</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white">
                                                {{ $ticket->address ?? 'Coordinate: ' . $ticket->latitude . ', ' . $ticket->longitude }}
                                            </dd>
                                        </div>
                                        @endif

                                        {{-- Content --}}
                                        @if($ticket->content)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrizione:</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white leading-relaxed">
                                                {{ $ticket->content }}
                                            </dd>
                                        </div>
                                        @endif

                                        {{-- Images --}}
                                        @if($ticket->media->count() > 0)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Immagini:</dt>
                                            <dd class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                                @foreach($ticket->media as $media)
                                                <div class="aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800">
                                                    <img
                                                        src="{{ asset($media->getUrl()) }}"
                                                        alt="Foto della segnalazione {{ $ticket->name }}"
                                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-200"
                                                        loading="lazy"
                                                    />
                                                </div>
                                                @endforeach
                                            </dd>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    <footer class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <button
                                            wire:click="toggleTicket({{ $ticket->id }})"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-md agid-focus"
                                            aria-expanded="{{ in_array($ticket->id, $expandedTickets) ? 'true' : 'false' }}"
                                            aria-controls="ticket-{{ $ticket->id }}-details"
                                        >
                                            {{ in_array($ticket->id, $expandedTickets) ? 'Mostra meno' : 'Mostra dettagli' }}
                                            <svg class="ml-2 w-4 h-4 transform {{ in_array($ticket->id, $expandedTickets) ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </footer>
                                </div>
                            </article>
                            @endforeach
                        </div>

                        {{-- Load more button --}}
                        @if($hasMorePages)
                        <div class="text-center pt-8">
                            <button
                                wire:click="loadMore"
                                class="inline-flex items-center px-6 py-3 border border-primary-600 text-primary-600 hover:bg-primary-50 hover:border-primary-700 hover:text-primary-700 dark:border-primary-400 dark:text-primary-400 dark:hover:bg-primary-900 dark:hover:border-primary-300 dark:hover:text-primary-300 rounded-lg font-medium agid-focus"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>Carica altre segnalazioni</span>
                                <span wire:loading class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Caricamento...
                                </span>
                            </button>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nessuna segnalazione trovata</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if(count($selectedCategories) > 0)
                                Nessuna segnalazione corrisponde ai filtri selezionati.
                                @else
                                Non ci sono segnalazioni da visualizzare al momento.
                                @endif
                            </p>
                            @if(count($selectedCategories) > 0)
                            <div class="mt-6">
                                <button
                                    wire:click="clearCategories"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 agid-focus"
                                >
                                    Rimuovi filtri
                                </button>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
                @endif

                {{-- Mappa panel --}}
                @if($currentTab === 'mappa')
                <div id="mappa-panel" role="tabpanel" aria-labelledby="mappa-tab">
                    <div class="fi-section">
                        @if($locationSet)
                        @livewire(\Modules\Fixcity\Filament\Widgets\TicketsMapWidget::class, [
                        'categoryFilter' => $selectedCategories,
                        'latitude' => $userLatitude,
                        'longitude' => $userLongitude,
                        ], key('map-' . implode('-', $selectedCategories)))
                        @else
                        <div class="flex items-center justify-center py-12">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Caricamento mappa...</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Attendere il caricamento della posizione
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </main>
    </section>

    {{-- Call to action section --}}
    <section class="fi-section">
        <div class="bg-gradient-to-r from-success-50 to-success-100 dark:from-success-900 dark:to-success-800 p-8 rounded-lg border border-success-200 dark:border-success-700 text-center">
            <h2 class="text-2xl font-bold text-success-900 dark:text-success-100 mb-4">
                Hai notato un disservizio nella tua città?
            </h2>
            <p class="text-success-700 dark:text-success-300 mb-6 max-w-2xl mx-auto">
                Segnala problemi di manutenzione stradale, illuminazione pubblica, raccolta rifiuti e molto altro.
                Il tuo contributo aiuta a migliorare la qualità dei servizi pubblici.
            </p>
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <a
                    href="{{ route('tickets.create') }}"
                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-success-600 hover:bg-success-700 agid-focus"
                >
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Segnala un disservizio
                </a>
                <a
                    href="/come-funziona"
                    class="inline-flex items-center px-8 py-3 border border-success-600 text-success-600 hover:bg-success-50 dark:border-success-400 dark:text-success-400 dark:hover:bg-success-900 rounded-md font-medium agid-focus"
                >
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Come funziona
                </a>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('get-user-location', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    window.dispatchEvent(new CustomEvent('set-user-location', {
                        detail: {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        }
                    }));
                }, function(error) {
                    window.dispatchEvent(new CustomEvent('not-set-user-location', {}));
                });
            }
        });

        window.addEventListener('set-user-location', function(event) {
            @this.call('setUserLocation', event.detail.latitude, event.detail.longitude);
        });
        window.addEventListener('not-set-user-location', function(event) {
            @this.call('notSetUserLocation');
        });
    });
</script>
@endvolt