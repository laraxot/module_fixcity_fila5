<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Fixcity\Notifications\TicketReceivedStaffNotification;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;
use Spatie\QueueableAction\QueueableAction;

/**
 * Segnalazione appena creata ({@see TicketCreatedEvent}): conferma al proprietario e avviso alla PA (DCP-284, A-01).
 *
 * PA = gli utenti con un ruolo di `BasePolicy::PA_ROLES`, ottenuti da {@see ListAssignableTicketOperatorsAction} (stessa fonte di chi si
 * puo' scegliere come responsabile). Sono gli stessi che `TicketPolicy` autorizza a vedere ogni segnalazione, quindi l'avviso non
 * rivela nulla che non vedano gia'. Il comune non e' una colonna del ticket: lo scoping per comune e' DA DECIDERE (story DCP-284, DD-1).
 * Il proprietario non riceve l'avviso destinato alla PA anche se ha un ruolo PA.
 */
final class NotifyTicketReceivedAction
{
    use QueueableAction;

    public function execute(Ticket $ticket): void
    {
        $this->confirmToOwner($ticket);
        $this->alertStaff($ticket);
    }

    private function confirmToOwner(Ticket $ticket): void
    {
        $code = $ticket->code;
        $owner = $ticket->owner;
        if (! is_string($code) || trim($code) === '' || ! $owner instanceof UserContract) {
            Log::warning('Fixcity reception notification skipped: ticket has no tracking code or no owner.', ['ticket_id' => $ticket->id]);

            return;
        }

        $status = $ticket->status instanceof TicketStatusEnum ? $ticket->status : TicketStatusEnum::PENDING;

        app(SendTicketNotificationAction::class)->execute(
            $owner,
            new TicketLifecycleNotification($code, TicketLifecycleEventEnum::Received, $status),
            $ticket->id,
        );
    }

    private function alertStaff(Ticket $ticket): void
    {
        $ownerId = (string) $ticket->owner_id;
        $staffIds = array_values(array_filter(
            array_map(static fn (int|string $id): string => (string) $id, array_keys(app(ListAssignableTicketOperatorsAction::class)->execute())),
            static fn (string $id): bool => $id !== $ownerId,
        ));
        if ($staffIds === []) {
            return;
        }

        $userClass = XotData::make()->getUserClass();
        foreach ($userClass::query()->whereIn('id', $staffIds)->get() as $member) {
            if ($member instanceof UserContract) {
                app(SendTicketNotificationAction::class)->execute($member, new TicketReceivedStaffNotification($ticket->id), $ticket->id);
            }
        }
    }
}
