<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Faker\Generator;

/**
 * Estrazione a peso deterministica.
 *
 * Faker\support\Provider\Base::randomElement() accetta un solo argomento: il secondo
 * parametro (i pesi) verrebbe ignorato in silenzio e la scelta risulterebbe uniforme.
 * Questa classe espone il sorteggio pesato che la demo si aspetta, usando lo stesso
 * generatore pseudocasuale: fissato il seed, l'elenco estratto e' riproducibile.
 */
final class DemoWeightedChoice
{
    /**
     * Indice dell'elemento estratto, proporzionale al peso.
     *
     * @param  non-empty-list<int>  $weights  pesi positivi, gia' allineati alla lista
     */
    public static function index(Generator $faker, array $weights): int
    {
        $roll = $faker->numberBetween(1, max(1, array_sum($weights)));

        foreach ($weights as $index => $weight) {
            $roll -= $weight;
            if ($roll <= 0) {
                return $index;
            }
        }

        return array_key_last($weights);
    }

    /**
     * Elemento estratto da una lista non vuota, con i pesi allineati per posizione.
     *
     * @template T
     *
     * @param  non-empty-list<T>  $rows
     * @param  non-empty-list<int>  $weights
     * @return T
     */
    public static function of(Generator $faker, array $rows, array $weights): mixed
    {
        return $rows[self::index($faker, $weights)];
    }
}
