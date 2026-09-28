<?php

declare(strict_types=1);

namespace Modules\Fixcity\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Modules\Fixcity\Actions\NotifyTicketSubscribersOfStatusChangeAction;
use Modules\Fixcity\Models\TicketActivity;

final class TicketActivityObserver implements ShouldHandleEventsAfterCommit
{
    public function created(TicketActivity $activity): void
    {
        app(NotifyTicketSubscribersOfStatusChangeAction::class)->execute($activity);
    }
}
