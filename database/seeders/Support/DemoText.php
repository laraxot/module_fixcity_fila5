<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Faker\Generator;

use function Safe\preg_replace;

/**
 * Estrazione di testi dal catalogo demo.
 *
 * Faker\Provider\Base::randomElement() e' dichiarato come `mixed`: se il
 * valore finisce in un array o in una stringa vuota, il dataset si spacca molto
 * piu' a valle (titoli, slug, payload SLA, JSON delle attivita'). Questa classe
 * centralizza la scelta e garantisce sempre una stringa non vuota.
 */
final class DemoText
{
    /**
     * @param  non-empty-list<non-empty-string>  $options
     * @return non-empty-string
     */
    public static function pick(Generator $faker, array $options): string
    {
        $value = $faker->randomElement($options);
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return $options[0];
    }

    /**
     * Versione pesata: la scelta segue il peso indicato per ogni voce.
     *
     * @param  non-empty-list<non-empty-string>  $options
     * @param  non-empty-list<int>  $weights  allineati per posizione a $options
     * @return non-empty-string
     */
    public static function pickWeighted(Generator $faker, array $options, array $weights): string
    {
        return $options[DemoWeightedChoice::index($faker, $weights)];
    }

    /**
     * Slug di sola lettera e cifra, con separatore `_`: i titoli demo contengono
     * accenti, apostrofi e punteggiatura, e lo slug non puo' accoglierli.
     */
    public static function slugify(string $value, int $maxLength): string
    {
        $slug = preg_replace('/[^a-z0-9]+/u', '_', mb_strtolower($value, 'UTF-8'));
        $slug = trim($slug, '_');

        return mb_substr($slug !== '' ? $slug : 'segnalazione', 0, $maxLength, 'UTF-8');
    }
}
