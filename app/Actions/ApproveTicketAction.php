<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Moderazione: pending → in_review con evento `approved` pubblico.
 */
final class ApproveTicketAction
{
    use QueueableAction;

    public function execute(Ticket $ticket, string $reason): void
    {
        Gate::authorize('changeStatus', $ticket);

        $reason = trim($reason);
        if ($reason === '') {
            throw new \InvalidArgumentException('La motivazione di approvazione è obbligatoria.');
        }

        $actorId = auth()->id();
        $userId = is_int($actorId) || is_string($actorId) ? $actorId : null;

        // Stato riletto con la riga bloccata (DB-07): due approvazioni concorrenti non registrano due transizioni dallo stesso stato.
        app(LockTicketForStatusChangeAction::class)->execute($ticket, static function (Ticket $locked) use ($reason, $userId): void {
            $current = TicketStatusEnum::tryFrom($locked->resolveTicketStatusValue());
            if (! $current instanceof TicketStatusEnum) {
                throw new \DomainException('Lo stato corrente della segnalazione non è riconosciuto.');
            }

            $target = TicketStatusEnum::IN_REVIEW;
            $current->assertCanTransitionTo($target);

            $locked->setStatus($target);

            app(RecordTicketActivityAction::class)->execute(
                $locked,
                TicketActivityEventTypeEnum::Approved,
                $current,
                $target,
                $reason,
                TicketActivityVisibilityEnum::Public,
                $userId,
            );
        });
    }
}
