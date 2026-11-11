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
 * Chiusura formale: solo da `resolved` → `closed`, con evento `closed`.
 */
final class CloseTicketAction
{
    use QueueableAction;

    public function execute(Ticket $ticket, string $reason): void
    {
        Gate::authorize('changeStatus', $ticket);

        $reason = trim($reason);
        if ($reason === '') {
            throw new \InvalidArgumentException('La motivazione di chiusura è obbligatoria.');
        }

        $actorId = auth()->id();
        $userId = is_int($actorId) || is_string($actorId) ? $actorId : null;

        // Stato riletto con la riga bloccata (DB-07).
        app(LockTicketForStatusChangeAction::class)->execute($ticket, static function (Ticket $locked) use ($reason, $userId): void {
            $currentValue = $locked->resolveTicketStatusValue();
            $current = TicketStatusEnum::tryFrom($currentValue);
            if ($current !== TicketStatusEnum::RESOLVED) {
                throw new \DomainException(
                    'La segnalazione può essere chiusa solo dallo stato resolved. Stato corrente: '.($currentValue !== '' ? $currentValue : 'sconosciuto'),
                );
            }

            $target = TicketStatusEnum::CLOSED;
            $current->assertCanTransitionTo($target);

            $locked->setStatus($target);

            app(RecordTicketActivityAction::class)->execute(
                $locked,
                TicketActivityEventTypeEnum::Closed,
                $current,
                $target,
                $reason,
                TicketActivityVisibilityEnum::Public,
                $userId,
            );
        });
    }
}
