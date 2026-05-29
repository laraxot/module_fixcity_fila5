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
}
