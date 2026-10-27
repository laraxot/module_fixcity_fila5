<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Tenant\Actions\Ente\GetEnteDataAction;
use Spatie\QueueableAction\QueueableAction;

use function Safe\json_encode;
use function Safe\preg_match;

/**
 * Verifica che la dichiarazione di accessibilita' (L. 4/2004 art. 3-quater) sia pubblicabile (DCP-132).
 *
 * Il codice non decide mai lo stato di conformita': lo fornisce l'ente dopo la valutazione. Qui si controlla solo che i dati ci siano e
 * siano coerenti: stato valido, metodo e data della valutazione (nessuna dichiarazione "conforme" senza valutazione), date di
 * pubblicazione e di aggiornamento, denominazione dell'ente (UNICA sorgente: `config/<tenant>/ente.php`, chiave `name`), responsabile
 * dell'accessibilita', indirizzo per le segnalazioni, link alla procedura dell'art. 3-quinquies, e, se il sito non e'
 * conforme, l'elenco delle parti non accessibili con la motivazione. Nessun valore d'esempio (example.it, xxxx, [[...]]).
 * Denominazione e recapiti rispondono al modello della Decisione (UE) 2018/1523: «[Denominazione dell'ente pubblico] si impegna...» e
 * «recapiti [...] della persona responsabile dell'accessibilita'».
 *
 * Restituisce le voci non pubblicabili: chiave => 'missing' | 'invalid' | 'placeholder'. Array vuoto = dichiarazione completa.
 */
final class AssessAccessibilityStatementAction
{
    use QueueableAction;

    public const string MISSING = 'missing';

    public const string INVALID = 'invalid';

    public const string PLACEHOLDER = 'placeholder';

    public const array STATUSES = ['compliant', 'partially_compliant', 'non_compliant'];

    public const array METHODS = ['self', 'third_party'];

    private const string PLACEHOLDER_PATTERN = '/\[\[|Edit this file|lorem ipsum|\b(?:TODO|TBD|FIXME)\b|example\.(?:com|org|it)|x{4,}/iu';

    /**
     * @param  array<string, mixed>  $declaration
     * @return array<string, string>
     */
    public function execute(array $declaration): array
    {
        $issues = [];
        $status = $declaration['status'] ?? null;
        $this->check($issues, 'status', is_string($status) && in_array($status, self::STATUSES, true), $status === null || $status === '');

        $evaluation = is_array($declaration['evaluation'] ?? null) ? $declaration['evaluation'] : [];
        $method = $evaluation['method'] ?? null;
        $evaluationOk = is_string($method) && in_array($method, self::METHODS, true) && $this->isDate($evaluation['date'] ?? null);
        $this->check($issues, 'evaluation', $evaluationOk, $method === null && ($evaluation['date'] ?? null) === null);

        foreach (['published_at', 'updated_at'] as $key) {
            $this->check($issues, $key, $this->isDate($declaration[$key] ?? null), ($declaration[$key] ?? null) === null);
        }

        $entity = app(GetEnteDataAction::class)->execute()->name;
        $this->check($issues, 'entity', $entity !== '', true);

        $officer = $declaration['officer'] ?? null;
        $this->check($issues, 'officer', is_string($officer) && $this->isFilled($officer) && ! str_contains($officer, '[['), $officer === null || $officer === '');

        $email = $declaration['feedback_email'] ?? null;
        $this->check($issues, 'feedback_email', is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false, $email === null || $email === '');

        $procedure = $declaration['procedure_url'] ?? null;
        $this->check($issues, 'procedure_url', is_string($procedure) && str_starts_with($procedure, 'https://'), $procedure === null || $procedure === '');

        if (in_array($status, ['partially_compliant', 'non_compliant'], true) && ! $this->hasNonAccessibleParts($declaration['non_accessible'] ?? null)) {
            $issues['non_accessible'] = self::MISSING;
        }

        // le voci gia' segnalate (per esempio un segnaposto al posto di una data) non vanno ripetute come "valori d'esempio"
        if (! in_array(self::PLACEHOLDER, $issues, true) && preg_match(self::PLACEHOLDER_PATTERN, json_encode(array_diff_key($declaration, $issues))) === 1) {
            $issues['document'] = self::PLACEHOLDER;
        }

        return $issues;
    }

    /**
     * @param  array<string, string>  $issues
     */
    private function check(array &$issues, string $key, bool $isValid, bool $isEmpty): void
    {
        if ($isValid) {
            return;
        }
        $issues[$key] = $isEmpty ? self::MISSING : self::INVALID;
    }

    private function isDate(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function hasNonAccessibleParts(mixed $parts): bool
    {
        if (! is_array($parts) || $parts === []) {
            return false;
        }
        foreach ($parts as $part) {
            if (! is_array($part) || ! $this->isFilled($part['content'] ?? null) || ! $this->isFilled($part['reason'] ?? null)) {
                return false;
            }
        }

        return true;
    }

    private function isFilled(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }
}
