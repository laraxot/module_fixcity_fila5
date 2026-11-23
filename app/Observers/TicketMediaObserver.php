<?php

declare(strict_types=1);

namespace Modules\Fixcity\Observers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Actions\RecordTicketActivityAction;
use Modules\Fixcity\Actions\ResolveTicketMediaDiskAction;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Media\Models\Media;

/** @see docs/stories/dcp-379-foto-nel-dettaglio-della-segnalazione.story.md */
final class TicketMediaObserver
{
    /**
     * Nuovi allegati dei ticket sul disco privato (SEC-04, DB-19): al momento dell'upload il ticket e' quasi sempre `pending`,
     * quindi non pubblico. Il file viene scritto DOPO l'insert del record (Media Library usa `$media->disk`), per questo basta
     * cambiare il disco qui. Si cambia solo il disco predefinito: un disco scelto esplicitamente da chi carica resta com'e'.
     * I media gia' esistenti non vengono toccati (colonna `disk` per riga). Disattivabile con `FIXCITY_TICKET_MEDIA_DISK=`.
     *
     * Qui e non in `Ticket::registerMediaCollections()->useDisk()` per non toccare il modello; vale per ogni percorso di upload
     * (wizard pubblico, back office, seeder) perche' tutti passano da Media Library.
     */
    /** @see docs/stories/dcp-379-foto-nel-dettaglio-della-segnalazione.story.md */
    public function creating(Media $media): void
    {
        if (! $this->isTicketMedia($media)) {
            return;
        }

        if ($media->collection_name === 'completion_photo') {
            $ticket = Ticket::query()->find($media->model_id);
            if (! $ticket instanceof Ticket || ! in_array(
                TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()),
                [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED],
                true,
            )) {
                throw new AuthorizationException('A completion photo requires a resolved ticket.');
            }

            Gate::authorize('changeStatus', $ticket);
        }

        $privateDisk = app(ResolveTicketMediaDiskAction::class)->execute();
        $defaultDisk = config('media-library.disk_name');
        if ($privateDisk === null || $media->disk === $privateDisk || $media->disk !== $defaultDisk) {
            return;
        }

        if ($media->conversions_disk === null || $media->conversions_disk === '' || $media->conversions_disk === $media->disk) {
            $media->conversions_disk = $privateDisk;
        }
        $media->disk = $privateDisk;
    }

    public function created(Media $media): void
    {
        if (! $this->isTicketMedia($media)) {
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

    private function isTicketMedia(Media $media): bool
    {
        $ticketClass = (new Ticket)->getMorphClass();

        return in_array($media->model_type, [Ticket::class, $ticketClass], true);
    }
}
