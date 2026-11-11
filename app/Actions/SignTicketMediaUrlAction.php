<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Firma a scadenza per le foto del disco privato dei ticket (SEC-04).
 *
 * Serve a chi ha gia' superato un controllo d'accesso che la route dei media non puo' ripetere: il possessore del codice
 * di tracciamento (pagina /tickets/track?code=...) vede le foto della propria segnalazione anche se non e' ancora pubblica e
 * non ha fatto login. L'URL resta relativo (DCP-379). HMAC propria con `app.key`: nessuna dipendenza dai nomi delle route Folio.
 */
final class SignTicketMediaUrlAction
{
    use QueueableAction;

    public const int DEFAULT_TTL_SECONDS = 3600;

    /**
     * @param  string  $path  percorso relativo, es. `/api/ticket-media/12/foto.jpg`
     */
    public function execute(string $path, int $ttlSeconds = self::DEFAULT_TTL_SECONDS): string
    {
        $expires = now()->addSeconds(max(1, $ttlSeconds))->getTimestamp();

        return $path.'?'.http_build_query([
            'expires' => $expires,
            'signature' => $this->signature($path, $expires),
        ]);
    }

    public function isValid(string $path, mixed $expires, mixed $signature): bool
    {
        if (! is_numeric($expires) || ! is_string($signature) || $signature === '') {
            return false;
        }

        $expiresAt = (int) $expires;
        if ($expiresAt < now()->getTimestamp()) {
            return false;
        }

        return hash_equals($this->signature($path, $expiresAt), $signature);
    }

    private function signature(string $path, int $expires): string
    {
        return hash_hmac('sha256', 'fixcity-ticket-media|'.$path.'|'.$expires, SafeStringCastAction::cast(config('app.key')));
    }
}
