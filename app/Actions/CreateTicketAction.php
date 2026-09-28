<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

class CreateTicketAction
{
    use QueueableAction;

    /**
     * Executes the ticket creation logic.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?string $locale = null): Ticket
    {
        if (! isset($data['owner_id'])) {
            $data['owner_id'] = auth()->id();
        }

        $existingCode = SafeStringCastAction::cast($data['code'] ?? null);
        if ($existingCode === '') {
            $data['code'] = app(AllocateTicketCodeAction::class)->execute(
                SafeStringCastAction::cast($data['ticket_prefix'] ?? 'TCK') ?: 'TCK'
            );
        }

        $ticket = Ticket::create($data);
        TicketCreatedEvent::dispatch($ticket);
        app(BuildTicketConfirmationDataAction::class)->flash($ticket, $locale);

        return $ticket;
    }
}
