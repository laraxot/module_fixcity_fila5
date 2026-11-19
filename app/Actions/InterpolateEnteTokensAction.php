<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Tenant\Actions\Ente\GetEnteDataAction;
use Modules\Tenant\Datas\EnteData;
use Spatie\QueueableAction\QueueableAction;

use function Safe\preg_replace;
use function Safe\preg_replace_callback;

/**
 * Sostituisce nei documenti legali del tenant i segnaposto `{{ente.<campo>}}` con i dati dell'ente (`config('ente')`, letti da
 * GetEnteDataAction). Cosi' titolare, DPO e recapiti non sono mai scritti nel testo: li fornisce l'ente una volta sola.
 *
 * Un valore vuoto, o un campo sconosciuto, diventa il segnaposto visibile `[[ente.<campo>]]`, che AssessPrivacyPolicyAction segnala:
 * la sezione resta "da completare" finche' l'ente non fornisce il dato, mai un valore inventato. Ogni valore e' reso testo Markdown
 * letterale (punteggiatura ASCII con backslash, righe unite): non puo' aggiungere link, enfasi, intestazioni o HTML. Le email (gia'
 * validate da GetEnteDataAction su un insieme di caratteri sicuro) restano senza escape, perche' il Markdown le renda link `mailto:`.
 */
final class InterpolateEnteTokensAction
{
    use QueueableAction;

    private const string TOKEN_PATTERN = '/\{\{\s*ente\.([a-z_]+)\s*\}\}/';

    /** Campi con un insieme di caratteri gia' sicuro per il Markdown. */
    private const array EMAIL_FIELDS = ['pec', 'email', 'dpo_email'];

    public function execute(string $markdown): string
    {
        $ente = app(GetEnteDataAction::class)->execute();

        return preg_replace_callback(
            self::TOKEN_PATTERN,
            fn (array $matches): string => $this->replacement($ente, is_string($matches[1] ?? null) ? $matches[1] : ''),
            $markdown,
        );
    }

    private function replacement(EnteData $ente, string $field): string
    {
        $value = $this->value($ente, $field);
        if ($value === '') {
            return '[[ente.'.$field.']]';
        }

        if (in_array($field, self::EMAIL_FIELDS, true)) {
            return $value;
        }

        $singleLine = preg_replace('/\s*\R\s*/u', ', ', $value);

        return preg_replace('/[!-\/:-@\[-`{-~]/', '\\\\$0', $singleLine);
    }

    private function value(EnteData $ente, string $field): string
    {
        return match ($field) {
            'name' => $ente->name,
            'region_name' => $ente->region_name,
            'address' => $ente->address,
            'pec' => $ente->pec,
            'email' => $ente->email,
            'phone' => $ente->phone,
            'toll_free' => $ente->toll_free,
            'tax_code' => $ente->tax_code,
            'vat' => $ente->vat,
            'dpo_name' => $ente->dpo_name,
            'dpo_email' => $ente->dpo_email,
            'hours' => $ente->hours,
            default => '',
        };
    }
}
