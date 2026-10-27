<?php

declare(strict_types=1);

namespace Modules\Fixcity\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Modules\Fixcity\Actions\NotifyTicketOwnerOfLifecycleEventAction;
use Modules\Fixcity\Actions\NotifyTicketSubscribersOfStatusChangeAction;
use Modules\Fixcity\Models\TicketActivity;

final class TicketActivityObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Due Action con insiemi di eventi DISGIUNTI (DCP-284): il cambio di stato pubblico avvisa proprietario e follower; approvata,
     * scartata, presa in carico e chiusa avvisano il proprietario. Una riga di audit non puo' produrre due messaggi.
     */
    public function created(TicketActivity $activity): void
    {
        app(NotifyTicketSubscribersOfStatusChangeAction::class)->execute($activity);
        app(NotifyTicketOwnerOfLifecycleEventAction::class)->execute($activity);
    }
}
