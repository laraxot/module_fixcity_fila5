<?php

declare(strict_types=1);

namespace Modules\Fixcity\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Fixcity\Models\Ticket;

/**
 * Segnalazione appena creata. Parte solo dopo il commit della transazione aperta (DB-06): se la creazione viene annullata
 * (allegato rifiutato, errore del database) nessun listener manda la ricevuta.
 */
class TicketCreatedEvent implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Ticket $ticket
    ) {}

    /*
     * Get the channels the event should be broadcast on.
     */
    // public function broadcastOn(): array
    // {
    //     return [
    //         new PrivateChannel('channel-name'),
    //     ];
    // }
}
