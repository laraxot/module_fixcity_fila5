<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

final class BuildTicketNextStepsAction
{
    use QueueableAction;

    /** @return array{title: string, description: string, deadline: string|null} */
    public function execute(Ticket $ticket): array
    {
        $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;
        $key = match ($status) {
            TicketStatusEnum::PENDING, TicketStatusEnum::DRAFT => 'moderation',
            TicketStatusEnum::IN_REVIEW => 'review',
            TicketStatusEnum::OPEN, TicketStatusEnum::IN_PROGRESS => 'processing',
            TicketStatusEnum::REOPENED => 'reopened',
            TicketStatusEnum::ON_HOLD => 'waiting',
            TicketStatusEnum::RESOLVED => 'confirmation',
            TicketStatusEnum::CLOSED => 'closed',
        };

        return [
            'title' => (string) __('fixcity::ticket_detail.next_steps.'.$key.'.title'),
            'description' => (string) __('fixcity::ticket_detail.next_steps.'.$key.'.description'),
            // shortcut: no tenant SLA, no invented date; configure per municipality before showing one.
            'deadline' => null,
        ];
    }
}
