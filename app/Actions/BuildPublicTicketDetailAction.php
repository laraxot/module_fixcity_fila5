<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dettaglio pubblico di una segnalazione (/{lang}/tickets/{id}), DCP-148.
 *
 * E' l'UNICA fonte dei campi che la pagina espone: un array esplicito, mai il modello serializzato. Stessa visibilita' della mappa e
 * dell'elenco ({@see BuildPublicTicketsQueryAction}: stati visibili a tutti, piu' le segnalazioni del proprietario se e' loggato).
 * Mai: `code` (capability token), `owner_id`, email, autore, immagini originali. L'indirizzo e' quello che l'elenco mostra oggi
 * (decisione aperta PP-02 della story DCP-104: civico e arrotondamento); le coordinate sono quelle gia' pubbliche nel geojson.
 * Il giorno di creazione e' `Y-m-d`, mai l'ora. Il test dell'insieme esatto di chiavi e' la guardia contro le fughe.
 */
final class BuildPublicTicketDetailAction
{
    use QueueableAction;

    private const int META_DESCRIPTION_LENGTH = 160;

    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     type: string,
     *     type_label: string,
     *     status: string,
     *     status_label: string,
     *     created_date: string,
     *     address: string,
     *     latitude: float|null,
     *     longitude: float|null,
     *     description: string,
     *     meta_description: string,
     *     timeline: list<array{occurred_at: string, occurred_date: string, occurred_on: string, from_label: string|null, to_label: string, reason: string|null}>,
     *     is_following: bool
     * }|null null se la segnalazione non esiste o non e' visibile al pubblico
     */
    public function execute(int $ticketId, ?Authenticatable $viewer = null): ?array
    {
        $ticket = app(BuildPublicTicketsQueryAction::class)->execute()
            ->select(['id', 'name', 'content', 'type', 'status', 'location', 'latitude', 'longitude', 'created_at'])
            ->whereKey($ticketId)
            ->first();

        if (! $ticket instanceof Ticket) {
            return null;
        }

        $location = is_array($ticket->location) ? $ticket->location : [];
        $address = $location['address'] ?? $location['display_name'] ?? '';
        $lat = SafeFloatCastAction::cast($location['lat'] ?? $location['latitude'] ?? $ticket->getAttribute('latitude') ?? 0);
        $lng = SafeFloatCastAction::cast($location['lng'] ?? $location['longitude'] ?? $ticket->getAttribute('longitude') ?? 0);
        $hasPoint = $lat !== 0.0 || $lng !== 0.0;
        $description = (string) $ticket->content;
        $type = $ticket->type;
        $status = $ticket->status;

        return [
            'id' => $ticket->id,
            'title' => $ticket->name,
            'type' => $type->value ?? '',
            'type_label' => $type?->getLabel() ?? '',
            'status' => $status->value ?? '',
            'status_label' => $status?->getLabel() ?? '',
            'created_date' => $ticket->created_at?->toDateString() ?? '',
            'address' => is_string($address) ? $address : '',
            'latitude' => $hasPoint ? $lat : null,
            'longitude' => $hasPoint ? $lng : null,
            'description' => $description,
            'meta_description' => Str::of($description)->squish()->limit(self::META_DESCRIPTION_LENGTH - 3)->toString(),
            'timeline' => app(BuildTicketTimelineAction::class)->execute($ticket, $viewer),
            'is_following' => $this->isFollowing($ticket, $viewer),
        ];
    }

    private function isFollowing(Ticket $ticket, ?Authenticatable $viewer): bool
    {
        $viewerId = $viewer?->getAuthIdentifier();
        if (! is_int($viewerId) && (! is_string($viewerId) || $viewerId === '')) {
            return false;
        }

        return TicketSubscriber::query()->where('ticket_id', $ticket->id)->where('user_id', $viewerId)->exists();
    }
}
