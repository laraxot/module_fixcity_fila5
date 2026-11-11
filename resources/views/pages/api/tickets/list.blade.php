<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\BuildTicketPublicImageUrlsAction;
use Modules\Fixcity\Models\Ticket;
use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('api.tickets.list');
// SEC-12: endpoint pubblico senza autenticazione, limite per IP.
middleware(['throttle:120,1']);

render(function (): JsonResponse {
    /** @var array<int, string> $types */
    $types = request()->collect('types')
        ->filter(static fn (mixed $type): bool => is_string($type) && $type !== '')
        ->values()
        ->all();

    $query = app(BuildPublicTicketsQueryAction::class)->execute();
    if ($types !== []) {
        $query->whereIn('type', $types);
    }

    $offset = max(0, (int) request()->query('offset', 0));
    $limit = min(50, max(1, (int) request()->query('limit', 20)));

    $total = (clone $query)->count();
    // `media` caricata in una query sola (DB-13): prima era una query per riga.
    /** @var \Illuminate\Support\Collection<int, Ticket> $tickets */
    $tickets = (clone $query)->with('media')->latest()->skip($offset)->take($limit)->get();
    $imageUrls = app(BuildTicketPublicImageUrlsAction::class);
    $viewer = auth()->user();

    $items = $tickets->map(static function (Ticket $ticket) use ($imageUrls, $viewer): array {
        $location = is_array($ticket->location) ? $ticket->location : [];
        $address = (string) ($location['address'] ?? $location['display_name'] ?? '');

        return [
            'id' => $ticket->getKey(),
            'name' => (string) $ticket->name,
            'type_label' => (string) $ticket->type_label,
            'address' => $address,
            'content' => Str::limit((string) $ticket->content, 200),
            // Solo foto di ticket pubblici (o del proprietario), mai PDF: SEC-04.
            'images' => $imageUrls->execute($ticket, $viewer),
            'url' => (string) LaravelLocalization::localizeURL('/tickets/'.$ticket->getKey()),
        ];
    })->all();

    return response()->json([
        'tickets' => $items,
        'total' => $total,
        'has_more' => $offset + $limit < $total,
    ]);
});
