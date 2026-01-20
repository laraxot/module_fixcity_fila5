<?php

use Modules\Fixcity\Models\Ticket;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Illuminate\Support\Facades\Http;
use function Laravel\Folio\{withTrashed, middleware, name, render};

withTrashed();
name('ticket.view');
middleware(['auth']);

render(function (View $view, string $slug) {
    $ticket = Ticket::firstWhere(['slug' => $slug]);

    if ($ticket == null) {
        return view('pub_theme::404');
    }

    $status = TicketStatusEnum::from($ticket->getRawOriginal('status'));
    $medias = $ticket->getMedia('ticket');

    $address = 'N/A';
    if ($ticket->latitude && $ticket->longitude) {
        $response = Http::withHeaders([
            'User-Agent' => 'YourAppName/1.0 (your@email.com)'
        ])->get("https://nominatim.openstreetmap.org/reverse", [
            'lat' => $ticket->latitude,
            'lon' => $ticket->longitude,
            'format' => 'json',
            'accept-language' => 'it' // Change to your preferred language
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $address = $data['display_name'] ?? 'N/A';
        }
    }

    return $view->with([
        'ticket' => $ticket,
        'medias' => $medias,
        'address' => $address,
        'user' => auth()->user(),
        'status' => $status
    ]);
});


?>
<x-layouts.marketing>
    <x-ui.marketing.breadcrumbs :crumbs="[
        [
            'href' => '/tickets',
            'text' => 'Tickets'
        ],
        [
            'text' => $ticket->name
        ]
    ]" />

    <div class="container w-full md:w-1/2 mx-auto mt-4 px-2">
        <div class="pb-6">
            <h1 class="text-3xl md:text-6xl font-bold dark:text-white">Segnalazione disservizio</h1>
        </div>

        <div class="bg-gray-100 border-l-4 border-yellow-500 p-4">
            <strong class="text-yellow-500">ATTENZIONE</strong>
            <p>Le informazioni che hai fornito hanno valore di dichiarazione. Verifica che siano corrette.</p>
        </div>

        <div class="py-4">
            <h1 class="text-2xl md:text-4xl font-bold dark:text-white">Segnalazione</h1>
        </div>

        <div class="bg-gray-100 p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold pb-2">Disservizio</h2>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="mb-4">
                    <p class="font-semibold">Indirizzo</p>
                    <p class="text-gray-700">{{ $address }}</p>
                    <hr class="my-2 border-gray-300">
                </div>

                <div class="mb-4">
                    <p class="font-semibold">Tipo di disservizio</p>
                    <p class="text-gray-700">{{ $ticket->type?->getLabel() ?? 'N/A' }}</p>
                    <hr class="my-2 border-gray-300">
                </div>

                <div class="mb-4">
                    <p class="font-semibold">Titolo</p>
                    <p class="text-gray-700">{{ $ticket->name ?? 'N/A' }}</p>
                    <hr class="my-2 border-gray-300">
                </div>

                <div class="mb-4">
                    <p class="font-semibold">Stato</p>
                    <p class="text-gray-700">{{ $status->getLabel() ?? 'N/A' }}</p>
                    <hr class="my-2 border-gray-300">
                </div>

                <div class="mb-4">
                    <p class="font-semibold">Dettagli</p>
                    <p class="text-gray-700">{{ $ticket->content ?? 'N/A' }}</p>
                    <hr class="my-2 border-gray-300">
                </div>

                <div class="mb-4">
                    <p class="font-semibold">Immagini</p>
                    @if($medias->isNotEmpty())
                    @foreach($medias as $media)
                    <img src="{{ $media->getFullUrl() }}" alt="Immagine del disservizio" class="rounded-md w-full max-w-md">
                    @endforeach
                    @else
                    <p class="text-gray-700">Nessuna immagine disponibile.</p>
                    @endif
                    <hr class="my-2 border-gray-300">
                </div>
            </div>
        </div>

        <div class="py-4">
            <h1 class="text-2xl md:text-4xl font-bold dark:text-white">Dati Generali</h1>
        </div>

        <div class="bg-gray-100 p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold pb-2">Autore della segnalazione</h2>
            <div class="bg-white p-4 rounded-lg shadow-sm pb-3">
                <div class="mb-4">
                    <p class="font-semibold text-3xl">{{ $user->name }}</p>
                    <hr class="my-2 border-gray-300">
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="mb-4">
                    <p class="font-semibold text-3xl">Contatti</p>
                    <hr class="my-2 border-gray-300">
                </div>
                <div class="mb-4">
                    <p class="font-semibold">Email</p>
                    <p class="text-gray-700">{{ $user->email }}</p>
                    <hr class="my-2 border-gray-300">
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>

</html>
