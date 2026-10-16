<?php

declare(strict_types=1);

namespace Modules\Fixcity\Observers;

use Modules\Fixcity\Actions\RecordTicketActivityAction;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Media\Models\Media;

final class TicketMediaObserver
{
    public function created(Media $media): void
    {
        $ticketClass = (new Ticket)->getMorphClass();
        if (! in_array($media->model_type, [Ticket::class, $ticketClass], true)) {
            return;
        }

        $ticket = Ticket::query()->find($media->model_id);
        if (! $ticket instanceof Ticket) {
            return;
        }

        $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;
        $actorId = $media->created_by ?? auth()->id();
        $userId = is_int($actorId) || is_string($actorId) ? $actorId : null;

        app(RecordTicketActivityAction::class)->execute(
            $ticket,
            TicketActivityEventTypeEnum::MediaAdded,
            $status,
            $status,
            null,
            in_array($status, TicketStatusEnum::canViewByAll(), true)
                ? TicketActivityVisibilityEnum::Public
                : TicketActivityVisibilityEnum::AuthorOnly,
            $userId,
            [
                'media_id' => $media->getKey(),
                'collection' => (string) $media->collection_name,
                'file_name' => basename((string) $media->file_name),
                'mime_type' => (string) $media->mime_type,
            ],
        );
    }
}
