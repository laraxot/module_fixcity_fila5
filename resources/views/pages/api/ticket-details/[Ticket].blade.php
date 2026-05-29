@php
    use function Laravel\Folio\name;
    use Modules\Fixcity\Models\Ticket;

    name('api.ticket-details');

    $ticket = $ticket ?? null;

    if (! $ticket instanceof Ticket || ! $ticket->isVisibleOnPublicFrontoffice()) {
        abort(404);
    }

    $images = $ticket->getMedia('attachments');
    if ($images->isEmpty()) {
        $images = $ticket->getMedia('ticket');
    }

    $imageUrls = $images
        ->map(fn ($media): string => $media->getFullUrl())
        ->values()
        ->all();

    echo json_encode([
        'id' => $ticket->id,
        'title' => $ticket->name,
        'description' => (string) $ticket->content,
        'images' => $imageUrls,
    ], JSON_UNESCAPED_UNICODE);
@endphp
