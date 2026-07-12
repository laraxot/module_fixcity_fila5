<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Payload JSON popup mappa (GET /api/ticket-details/{ticket}) — logica in Action, non Controller.
 */
final class BuildTicketPublicDetailsPayloadAction
{
    use QueueableAction;

    /**
     * @param  Ticket|array<string, mixed>  $ticket
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     images: list<string>
     * }
     */
    public function execute(Ticket|array $ticket): array
    {
        if (is_array($ticket)) {
            return $this->payloadFromGeoJsonFeature($ticket);
        }

        $images = $ticket->getMedia('attachments');
        if ($images->isEmpty()) {
            $images = $ticket->getMedia('ticket');
        }

        /** @var list<string> $imageUrls */
        $imageUrls = array_values($images
            ->map(static fn ($media): string => $media->getFullUrl())
            ->all());

        return [
            'id' => $ticket->id,
            'title' => $ticket->name,
            'description' => SafeStringCastAction::cast($ticket->content),
            'images' => $imageUrls,
        ];
    }

    /**
     * @param  array<string, mixed>  $feature
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     images: list<string>
     * }
     */
    public function executeFromGeoJsonFeature(array $feature): array
    {
        return $this->execute($feature);
    }

    /**
     * @param  array<string, mixed>  $feature
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     images: list<string>
     * }
     */
    private function payloadFromGeoJsonFeature(array $feature): array
    {
        $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
        $images = is_array($properties['images'] ?? null) ? $properties['images'] : [];

        /** @var list<string> $imageUrls */
        $imageUrls = array_values(array_filter(
            $images,
            static fn (mixed $url): bool => is_string($url) && $url !== '',
        ));

        return [
            'id' => SafeIntCastAction::cast($properties['id'] ?? 0),
            'title' => SafeStringCastAction::cast($properties['title'] ?? $properties['name'] ?? ''),
            'description' => SafeStringCastAction::cast($properties['description'] ?? $properties['content'] ?? ''),
            'images' => $imageUrls,
        ];
    }
}
