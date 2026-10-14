<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Recupera un ticket tramite codice di tracciamento.
 *
 * Il codice è un capability token: chi lo conosce può vedere stato e timeline
 * pubblica minima, anche se lo stato non è ancora in canViewByAll (es. PENDING).
 *
 * Proprio perche' e' un token, indovinarlo e' l'attacco: i tentativi a vuoto sono limitati per indirizzo (DCP-149). Il freno sta qui perche'
 * da qui passano la pagina di tracciamento e quella di conferma (`?code=`); sulla pagina Folio del modulo era oscurato dalla pagina del tema.
 */
final class GetPublicTicketByCodeAction
{
    use QueueableAction;

    /** Codici sbagliati ammessi per indirizzo nella finestra, poi 429. */
    public const MAX_MISSES = 10;

    /** Durata della finestra e del blocco, in secondi. */
    public const DECAY_SECONDS = 60;

    public static function throttleKey(string $ip): string
    {
        return 'ticket-code-lookup:'.$ip;
    }

    public function execute(?string $code = null): ?Ticket
    {
        if ($code === null || trim($code) === '') {
            return null;
        }

        $key = self::throttleKey((string) request()->ip());

        if (RateLimiter::tooManyAttempts($key, self::MAX_MISSES)) {
            throw new ThrottleRequestsException('Troppi tentativi di ricerca del codice.', null, [
                'Retry-After' => (string) RateLimiter::availableIn($key),
            ]);
        }

        $ticket = Ticket::query()
            ->where('code', trim($code))
            ->first();

        if (! $ticket instanceof Ticket) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
        }

        return $ticket;
    }
}
