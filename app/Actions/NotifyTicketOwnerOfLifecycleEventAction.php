<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Fixcity\Observers\TicketActivityObserver;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Avvisa il PROPRIETARIO quando la sua segnalazione viene approvata, scartata, presa in carico o chiusa (DCP-284, A-01).
 *
 * Si aggancia alla riga di audit appena creata ({@see TicketActivityObserver}): ogni Action del ciclo di
 * vita passa da `RecordTicketActivityAction`, quindi un evento = una riga = al piu' un messaggio. Gestisce SOLO gli eventi di
 * {@see TicketLifecycleEventEnum}; il cambio di stato pubblico resta di {@see NotifyTicketSubscribersOfStatusChangeAction}, cosi' lo
 * stesso cambio non produce due messaggi. Destinatario: solo il proprietario, anche se la segnalazione non e' pubblica.
 */
final class NotifyTicketOwnerOfLifecycleEventAction
{
    use QueueableAction;

    public function execute(TicketActivity $activity): void
    {
        $event = TicketLifecycleEventEnum::fromActivityEventType($activity->event_type);
        if (! $event instanceof TicketLifecycleEventEnum) {
            return;
        }

        $ticket = $activity->ticket()->first();
        if (! $ticket instanceof Ticket) {
            return;
        }

        if ($event === TicketLifecycleEventEnum::Assigned && ! $this->isFirstAssignment($activity, $ticket)) {
            return;
        }

        $code = $ticket->code;
        if (! is_string($code) || trim($code) === '') {
            Log::warning('Fixcity lifecycle notification skipped: ticket has no public tracking code.', [
                'ticket_id' => $ticket->id,
                'event' => $event->value,
            ]);

            return;
        }

        $owner = $ticket->owner;
        if (! $owner instanceof UserContract) {
            Log::warning('Fixcity lifecycle notification skipped: ticket has no owner.', [
                'ticket_id' => $ticket->id,
                'event' => $event->value,
            ]);

            return;
        }

        app(SendTicketNotificationAction::class)->execute(
            $owner,
            new TicketLifecycleNotification($code, $event, $this->resultingStatus($activity, $ticket), $activity->reason),
            $ticket->id,
        );
    }

    /**
     * Solo la prima assegnazione a un responsabile avvisa il cittadino: la rimozione e le riassegnazioni no (rumore). Lo storico
     * e' quello delle attivita' di assegnazione precedenti.
     */
    private function isFirstAssignment(TicketActivity $activity, Ticket $ticket): bool
    {
        if (! $this->assignsSomeone($activity)) {
            return false;
        }

        $earlier = TicketActivity::query()
            ->where('ticket_id', $ticket->getKey())
            ->where('event_type', TicketActivityEventTypeEnum::Assignment->value)
            ->where('id', '<', $activity->getKey())
            ->get();

        return ! $earlier->contains(fn (TicketActivity $previous): bool => $this->assignsSomeone($previous));
    }

    private function assignsSomeone(TicketActivity $activity): bool
    {
        $responsible = ($activity->payload ?? [])['responsible_id'] ?? null;

        return is_string($responsible) && $responsible !== '';
    }

    /** Lo stato in cui la segnalazione si trova dopo l'evento: quello registrato nell'attivita', altrimenti quello corrente. */
    private function resultingStatus(TicketActivity $activity, Ticket $ticket): TicketStatusEnum
    {
        $recorded = ($activity->payload ?? [])['to'] ?? null;

        return (is_string($recorded) ? TicketStatusEnum::tryFrom($recorded) : null)
            ?? TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue())
            ?? TicketStatusEnum::PENDING;
    }
}
