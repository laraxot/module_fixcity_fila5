<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Esegue la creazione di una segnalazione al piu' una volta per (proprietario, token di invio) (DCP-286, FO-3-02).
 *
 * Il token lo genera il server al mount del wizard e il client non puo' cambiarlo: un secondo invio dello stesso modulo (doppio clic, richiesta
 * Livewire ripetuta) trova la segnalazione gia' creata e la restituisce, invece di crearne un'altra. Il blocco evita che due richieste
 * contemporanee con lo stesso token creino entrambe. Nessuna tabella: l'esito vive nella cache per un'ora.
 */
class SubmitTicketOnceAction
{
    use QueueableAction;

    private const int REMEMBER_SECONDS = 3600;

    private const int LOCK_SECONDS = 60;

    public static function lockKey(string $ownerId, string $token): string
    {
        return 'fixcity:ticket-submission-lock:'.$ownerId.':'.$token;
    }

    private static function resultKey(string $ownerId, string $token): string
    {
        return 'fixcity:ticket-submission:'.$ownerId.':'.$token;
    }

    /** La segnalazione gia' creata da questo invio, se c'e'. Il proprietario e' sempre parte della chiave e della ricerca. */
    public function existing(string $ownerId, string $token): ?Ticket
    {
        $ticketId = $token === '' ? null : Cache::get(self::resultKey($ownerId, $token));

        return is_string($ticketId) && $ticketId !== ''
            ? Ticket::query()->whereKey($ticketId)->where('owner_id', $ownerId)->first()
            : null;
    }

    /**
     * @param  Closure(): Ticket  $create  crea la segnalazione (e i suoi allegati); eseguita al piu' una volta per token
     *
     * @throws LockTimeoutException se un'altra richiesta con lo stesso token non finisce entro `$waitSeconds`
     */
    public function execute(string $ownerId, string $token, Closure $create, int $waitSeconds = 5): Ticket
    {
        // Senza token (componente non montato) non si ricorda nulla: una chiave vuota farebbe coincidere tutti gli invii dello stesso utente.
        if ($token === '') {
            return $create();
        }

        $already = $this->existing($ownerId, $token);
        if ($already instanceof Ticket) {
            return $already;
        }

        $lock = Cache::lock(self::lockKey($ownerId, $token), self::LOCK_SECONDS);
        $lock->block($waitSeconds);

        try {
            // un'altra richiesta puo' aver finito mentre aspettavo il blocco
            $already = $this->existing($ownerId, $token);
            if ($already instanceof Ticket) {
                return $already;
            }

            $ticket = $create();
            Cache::put(self::resultKey($ownerId, $token), SafeStringCastAction::cast($ticket->getKey()), self::REMEMBER_SECONDS);

            return $ticket;
        } finally {
            $lock->release();
        }
    }
}
