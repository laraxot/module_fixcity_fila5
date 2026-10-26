<?php

declare(strict_types=1);

namespace Modules\Fixcity\Listeners;

use Modules\Fixcity\Actions\NotifyTicketReceivedAction;
use Modules\Fixcity\Events\TicketCreatedEvent;

/**
 * Registrato in `Providers/EventServiceProvider::$listen` (DCP-284). Sottile di proposito: la logica e' nell'Action; il listener
 * resta sincrono perche' sono le notifiche a essere accodate dopo il commit.
 */
final class NotifyTicketReceivedListener
{
    public function handle(TicketCreatedEvent $event): void
    {
        app(NotifyTicketReceivedAction::class)->execute($event->ticket);
    }
}
