<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

class ChangeStatus
{
    use QueueableAction;

    /**
     * Execute the change status action.
     *
     * Lo stato corrente si legge e si valida con la riga bloccata ({@see LockTicketForStatusChangeAction}, DB-07).
     *
     * @param  Ticket  $ticket  The ticket to update
     * @param  string  $status  The new status (string value)
     * @param  string  $reason  The reason for the change
     */
    public function execute(Ticket $ticket, string $status, string $reason): void
    {
        $target = TicketStatusEnum::tryFrom($status);
        if (! $target instanceof TicketStatusEnum) {
            throw new \DomainException('Stato segnalazione non valido: '.$status);
        }

        $visibility = in_array($target, TicketStatusEnum::canViewByAll(), true)
            ? TicketActivityVisibilityEnum::Public
            : TicketActivityVisibilityEnum::Internal;

        $actorId = auth()->id();
        $userId = is_int($actorId) || is_string($actorId) ? $actorId : null;

        app(LockTicketForStatusChangeAction::class)->execute($ticket, static function (Ticket $locked) use ($target, $reason, $visibility, $userId): void {
            $currentValue = $locked->resolveTicketStatusValue();
            $current = $currentValue !== '' ? TicketStatusEnum::tryFrom($currentValue) : null;

            if ($currentValue !== '' && ! $current instanceof TicketStatusEnum) {
                throw new \DomainException('Lo stato corrente della segnalazione non è riconosciuto: '.$currentValue);
            }

            if ($current === $target) {
                return;
            }

            $current?->assertCanTransitionTo($target);

            $locked->setStatus($target);

            app(RecordTicketActivityAction::class)->execute(
                $locked,
                TicketActivityEventTypeEnum::StatusChange,
                $current,
                $target,
                trim($reason) !== '' ? trim($reason) : null,
                $visibility,
                $userId,
            );
        });
    }
}
