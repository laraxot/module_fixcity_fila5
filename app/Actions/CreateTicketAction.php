<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Validation\ValidationException;
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
            $user = auth()->user();
            if (! $user) {
                throw new ValidationException(
                    validator([], ['owner_id' => 'required'])
                )->withMessages(['owner_id' => __('fixcity::ticket.owner.auth_required')]);
            }
            $data['owner_id'] = $user->id;
        }

        $existingCode = SafeStringCastAction::cast($data['code'] ?? null);
        if ($existingCode === '') {
            $data['code'] = app(AllocateTicketCodeAction::class)->execute(
                SafeStringCastAction::cast($data['ticket_prefix'] ?? 'TCK') ?: 'TCK'
            );
        }

        $ticket = Ticket::create($data);
        TicketCreatedEvent::dispatch($ticket);
        app(SendTicketReceiptAction::class)->execute($ticket);
        app(BuildTicketConfirmationDataAction::class)->flash($ticket, $locale);

        return $ticket;
    }
}
