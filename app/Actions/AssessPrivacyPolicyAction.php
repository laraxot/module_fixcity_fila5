<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

use function Safe\preg_match;
use function Safe\preg_replace;

/**
 * Verifica che il testo dell'informativa privacy del tenant contenga le sezioni dell'art. 13 GDPR e nessun segnaposto (DCP-130).
 *
 * Il codice non scrive mai i valori (titolare, DPO, base giuridica, termini): li fornisce l'ente. Qui si controlla solo che ci siano:
 * ogni sezione obbligatoria e' una intestazione Markdown (livello 1-6) uguale al titolo in `fixcity::privacy.sections.<chiave>.title`
 * (lingua corrente) con un corpo non vuoto e senza segnaposto (`[[...]]`, "Edit this file", "lorem ipsum", TODO/TBD, example.it, xxxx).
 * Il modello da compilare e' in `resources/templates/privacy-policy.<lingua>.md`.
 *
 * Restituisce le sole sezioni non pubblicabili: chiave => 'missing' | 'placeholder'. La chiave 'document' indica un segnaposto
 * fuori dalle sezioni obbligatorie. Array vuoto = informativa completa.
 */
final class AssessPrivacyPolicyAction
{
    use QueueableAction;

    public const MISSING = 'missing';

    public const PLACEHOLDER = 'placeholder';

    /** Sezioni obbligatorie, nell'ordine di art. 13 par. 1 e 2. */
    public const SECTIONS = ['controller', 'dpo', 'purposes', 'recipients', 'transfers', 'retention', 'rights', 'complaint', 'mandatory', 'automated'];

    private const PLACEHOLDER_PATTERN = '/\[\[.*?\]\]|Edit this file|lorem ipsum|\b(?:TODO|TBD|FIXME)\b|example\.(?:com|org|it)|x{4,}/iu';

    /**
     * @return array<string, string>
     */
    public function execute(?string $markdown): array
    {
        if ($markdown === null) {
            return array_fill_keys(self::SECTIONS, self::MISSING);
        }

        $bodies = $this->bodiesBySection($markdown);
        $issues = [];
        foreach (self::SECTIONS as $key) {
            $body = $bodies[$key] ?? '';
            if (trim($body) === '') {
                $issues[$key] = self::MISSING;
            } elseif (preg_match(self::PLACEHOLDER_PATTERN, $body) === 1) {
                $issues[$key] = self::PLACEHOLDER;
            }
        }

        if (! in_array(self::PLACEHOLDER, $issues, true) && preg_match(self::PLACEHOLDER_PATTERN, $markdown) === 1) {
            $issues['document'] = self::PLACEHOLDER;
        }

        return $issues;
    }

    /**
     * Corpo di ogni sezione obbligatoria presente: dalla sua intestazione fino alla successiva di livello pari o superiore
     * (le sotto-intestazioni fanno parte del corpo).
     *
     * @return array<string, string>
     */
    private function bodiesBySection(string $markdown): array
    {
        $keyByTitle = [];
        foreach (self::SECTIONS as $key) {
            $keyByTitle[$this->normalize((string) __('fixcity::privacy.sections.'.$key.'.title'))] = $key;
        }

        /** @var array<string, list<string>> $lines */
        $lines = [];
        $current = null;
        $level = 0;
        foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", $markdown)) as $line) {
            $heading = $this->parseHeading($line);
            if ($heading === null) {
                if ($current !== null) {
                    $lines[$current][] = $line;
                }

                continue;
            }

            [$headingLevel, $title] = $heading;
            $key = $keyByTitle[$this->normalize($title)] ?? null;
            if ($key !== null) {
                [$current, $level, $lines[$key]] = [$key, $headingLevel, []];
            } elseif ($current !== null && $headingLevel > $level) {
                $lines[$current][] = $line;
            } else {
                $current = null;
            }
        }

        $bodies = [];
        foreach ($lines as $key => $sectionLines) {
            $bodies[$key] = implode("\n", $sectionLines);
        }

        return $bodies;
    }

    /**
     * @return array{0: int, 1: string}|null livello e testo di una intestazione ATX, null se la riga non lo e'
     */
    private function parseHeading(string $line): ?array
    {
        if (preg_match('/^(#{1,6})\s+(.+?)\s*#*\s*$/u', $line, $matches) !== 1) {
            return null;
        }

        return [strlen($matches[1] ?? ''), $matches[2] ?? ''];
    }

    private function normalize(string $heading): string
    {
        return mb_strtolower(trim(preg_replace('/^\d+[.)]\s*|[\s:.]+$/u', '', trim($heading))));
    }
}
