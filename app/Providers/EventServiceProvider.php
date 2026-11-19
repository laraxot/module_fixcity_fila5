<?php

declare(strict_types=1);

namespace Modules\Fixcity\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Listeners\NotifyTicketReceivedListener;
use Modules\Fixcity\Listeners\StripTicketPhotoMetadataListener;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Listener della segnalazione appena creata (DCP-284): conferma al cittadino e avviso alla PA.
     *
     * Registrazione esplicita e NON tramite `$listen`: con la cache degli eventi (`event:cache`, anche via `optimize`, e
     * `bootstrap/cache/events.php` esiste in questo checkout) Laravel legge `$listen` dal file e questo provider vi compare con un
     * array vuoto, quindi un listener dichiarato in `$listen` sparisce finche' qualcuno non rigenera la cache. Misurato.
     *
     * `Listeners\TicketCreatedListener` NON e' registrato: richiama `ChangeStatus` e non e' mai stato provato.
     */
    public function boot(): void
    {
        Event::listen(TicketCreatedEvent::class, NotifyTicketReceivedListener::class);
        // Privacy notice: report photos are stored without EXIF/GPS metadata.
        Event::listen(MediaHasBeenAddedEvent::class, StripTicketPhotoMetadataListener::class);
    }

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
