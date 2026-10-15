<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Spatie\QueueableAction\QueueableAction;

final class AssignTicketAction
{
    use QueueableAction;

    /**
     * Assign a ticket to a PA operator, or remove its assignment.
     */
    public function execute(Ticket $ticket, ?string $responsibleId): Ticket
    {
        Gate::authorize('assign', $ticket);

        /** @var array{ticket: Ticket, activity: TicketActivity|null} $result */
        $result = $ticket->getConnection()->transaction(function () use ($ticket, $responsibleId): array {
            $lockedTicket = $ticket->newQuery()
                ->whereKey($ticket->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $previous = $lockedTicket->responsible_id;
            $lockedTicket->responsible_id = $responsibleId;
            $lockedTicket->save();

            $activity = null;
            if ($previous !== $responsibleId) {
                $statusValue = $lockedTicket->resolveTicketStatusValue();
                $status = TicketStatusEnum::tryFrom($statusValue) ?? TicketStatusEnum::PENDING;
                $actorId = auth()->id();
                $userId = is_int($actorId) ? $actorId : null;
                $reason = $responsibleId === null
                    ? 'Rimossa assegnazione'
                    : 'Assegnato a responsabile #'.$responsibleId;

                $activity = app(RecordTicketActivityAction::class)->execute(
                    $lockedTicket,
                    TicketActivityEventTypeEnum::Assignment,
                    $status,
                    $status,
                    $reason,
                    TicketActivityVisibilityEnum::Internal,
                    $userId,
                    ['responsible_id' => $responsibleId],
                );
            }

            return [
                'ticket' => $lockedTicket->refresh(),
                'activity' => $activity,
            ];
        });

        if ($result['activity'] instanceof TicketActivity) {
            app(NotifyTicketAssigneeOfAssignmentAction::class)->execute($result['activity']);
        }

        return $result['ticket'];
    }
}
