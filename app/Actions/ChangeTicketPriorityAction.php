<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\Activity\Actions\LogActivityAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Webmozart\Assert\Assert;

final class ChangeTicketPriorityAction
{
    public function execute(Ticket $ticket, string $priority): Ticket
    {
        Gate::authorize('changePriority', $ticket);

        Assert::true(TicketPriorityEnum::tryFrom($priority) instanceof TicketPriorityEnum);

        $previous = SafeStringCastAction::cast($ticket->getRawOriginal('priority'));
        $ticket->forceFill(['priority' => $priority])->save();

        if ($previous !== $priority) {
            (new LogActivityAction(
                'priority_changed',
                subject: $ticket,
                properties: ['from' => $previous, 'to' => $priority],
            ))->execute();
        }

        return $ticket->refresh();
    }
}
