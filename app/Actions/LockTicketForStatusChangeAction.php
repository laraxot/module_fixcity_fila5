<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Closure;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Percorso comune dei cambi di stato (DB-07): transazione, riga del ticket riletta con `SELECT ... FOR UPDATE`, poi `$change` sul
 * ticket bloccato. Il controllo della transizione va fatto DENTRO `$change` sullo stato appena riletto: due operatori (o un doppio
 * clic) non possono piu' superare entrambi il controllo sullo stesso stato vecchio. Il secondo trova lo stato aggiornato e, se la
 * transizione non e' piu' consentita, riceve la DomainException del workflow.
 *
 * Dopo il commit l'istanza del chiamante prende gli attributi salvati, cosi' la pagina mostra lo stato reale.
 * Stesso schema di AssignTicketAction.
 */
final class LockTicketForStatusChangeAction
{
    use QueueableAction;

    /**
     * @param  Closure(Ticket): void  $change  riceve il ticket bloccato e riletto; scrive stato e attivita' su quello
     */
    public function execute(Ticket $ticket, Closure $change): void
    {
        $ticket->getConnection()->transaction(static function () use ($ticket, $change): void {
            $locked = $ticket->newQuery()
                ->whereKey($ticket->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $change($locked);

            $ticket->setRawAttributes($locked->getAttributes(), true);
        });
    }
}
