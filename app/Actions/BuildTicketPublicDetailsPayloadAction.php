<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;

/**
 * Payload JSON popup mappa (GET /api/ticket-details/{ticket}) — logica in Action, non Controller.
 */
final class BuildTicketPublicDetailsPayloadAction
{
    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     images: list<string>
     * }
     */
    public function execute(Ticket $ticket): array
    {
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
            'description' => (string) $ticket->content,
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
        $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
        $images = is_array($properties['images'] ?? null) ? $properties['images'] : [];

        /** @var list<string> $imageUrls */
        $imageUrls = array_values(array_filter(
            $images,
            static fn (mixed $url): bool => is_string($url) && $url !== '',
        ));

        return [
            'id' => (int) ($properties['id'] ?? 0),
            'title' => (string) ($properties['title'] ?? $properties['name'] ?? ''),
            'description' => (string) ($properties['description'] ?? $properties['content'] ?? ''),
            'images' => $imageUrls,
        ];
    }
}
