<?php

declare(strict_types=1);

namespace Modules\Fixcity\Listeners;

use Modules\Fixcity\Models\Ticket;
use Modules\Media\Actions\Image\StripImageMetadataAction;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

/**
 * Privacy notice: photos attached to a report are stored without EXIF and GPS data. Media Library fires the event right after
 * copying the original to its disk, for every upload path (citizen wizard, back office, API). Registered in EventServiceProvider.
 */
final class StripTicketPhotoMetadataListener
{
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        if ($event->media->model_type !== (new Ticket)->getMorphClass()) {
            return;
        }

        app(StripImageMetadataAction::class)->execute($event->media);
    }
}
