<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

use function Safe\preg_match_all;

/**
 * Dati dell'ente che l'informativa privacy pubblicata richiede ma che `config('ente')` non fornisce (PRD-04).
 *
 * L'informativa arriva gia' interpolata da {@see GetPublishedPrivacyPolicyAction}: ogni `{{ente.<campo>}}` senza valore e' diventato
 * `[[ente.<campo>]]` ({@see InterpolateEnteTokensAction}). Sono richiesti solo i campi che il testo usa davvero (titolare, sede, PEC,
 * DPO...): il wizard non accetta un consenso su un'informativa che non nomina il titolare o il DPO (art. 13 GDPR).
 */
final class GetMissingEnteFieldsAction
{
    use QueueableAction;

    /**
     * @return list<string> campi mancanti, senza duplicati, nell'ordine in cui compaiono; vuoto = informativa completa
     */
    public function execute(string $interpolatedMarkdown): array
    {
        if (preg_match_all('/\[\[ente\.([a-z_]+)\]\]/', $interpolatedMarkdown, $matches) === 0) {
            return [];
        }

        /** @var list<string> $fields */
        $fields = $matches[1];

        return array_values(array_unique($fields));
    }
}
