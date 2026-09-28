<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Faker\Generator;
use Modules\Geo\Models\Comune;
use Modules\Xot\Actions\Cast\SafeIntCastAction;

/**
 * Comuni italiani per la demo investitori: coordinate WGS84, CAP, provincia, regione.
 *
 * Il dataset del modulo Geo (`Modules/Geo/resources/json/comuni.json`) contiene solo
 * 4 record, di cui uno corrotto: quando il nome del comune e' presente in Geo si usa
 * la lat/lng di Geo (SSoT del modulo), altrimenti si cade al catalogo curato qui.
 * In questo modo la demo resta completa anche quando il dataset Geo e' parziale.
 */
final class DemoMunicipalityProvider
{
    /**
     * Jitter massimo in gradi attorno al centro del comune: ~2 km, evita che tutte
     * le segnalazioni di uno stesso comune cadano sullo stesso punto.
     */
    private const float MAX_JITTER_DEGREES = 0.021;

    /**
     * @var list<string>
     */
    private const array STREETS = [
        'Via Roma', 'Via Garibaldi', 'Via Mazzini', 'Via Dante Alighieri', 'Via Giuseppe Verdi',
        'Corso Italia', 'Via XX Settembre', 'Via Guglielmo Marconi', 'Via Alessandro Volta',
        'Via Michelangelo Buonarroti', 'Via Leonardo da Vinci', 'Via Antonio Gramsci',
        'Via Enrico Fermi', 'Via Alessandro Manzoni', 'Viale della Vittoria', 'Via Cristoforo Colombo',
        'Via San Francesco', 'Via del Popolo', 'Via Giacomo Matteotti', 'Via Fratelli Bandiera',
        'Via Nino Bixio', 'Via Cairoli', 'Via Umberto I', 'Via Cavour', 'Via Vittorio Emanuele II',
        'Via delle Industrie', 'Via dei Giardini', 'Via del Parco', 'Via delle Fontane',
        'Viale Europa', 'Via Trieste', 'Via Verona', 'Via Bergamo', 'Via Ravenna',
    ];

    /**
     * @var list<non-empty-string>
     */
    private const array LANDMARKS = [
        'Piazza del Municipio', 'Piazza della Chiesa', 'Piazza Garibaldi', 'Largo San Giovanni',
        'Viale della Stazione', 'Piazza Dante', 'Viale dei Pini', 'Largo della Chiesa',
    ];

    /**
     * Catalogo curato: nome, provincia (denominazione, sigla, CAP), regione, centro.
     *
     * @var list<array{
     *     name: non-empty-string,
     *     region: non-empty-string,
     *     province: non-empty-string,
     *     province_code: non-empty-string,
     *     postal_code: non-empty-string,
     *     lat: float,
     *     lng: float,
     *     population: int
     * }>
     */
    private const array MUNICIPALITIES = [
        [
            'name' => 'Roma',
            'region' => 'Lazio',
            'province' => 'Roma',
            'province_code' => 'RM',
            'postal_code' => '00100',
            'lat' => 41.9028,
            'lng' => 12.4964,
            'population' => 2_755_000,
        ],
        [
            'name' => 'Milano',
            'region' => 'Lombardia',
            'province' => 'Milano',
            'province_code' => 'MI',
            'postal_code' => '20100',
            'lat' => 45.4642,
            'lng' => 9.19,
            'population' => 1_400_000,
        ],
        [
            'name' => 'Napoli',
            'region' => 'Campania',
            'province' => 'Napoli',
            'province_code' => 'NA',
            'postal_code' => '80100',
            'lat' => 40.8518,
            'lng' => 14.2681,
            'population' => 913_000,
        ],
        [
            'name' => 'Torino',
            'region' => 'Piemonte',
            'province' => 'Torino',
            'province_code' => 'TO',
            'postal_code' => '10100',
            'lat' => 45.0703,
            'lng' => 7.6869,
            'population' => 870_000,
        ],
        [
            'name' => 'Palermo',
            'region' => 'Sicilia',
            'province' => 'Palermo',
            'province_code' => 'PA',
            'postal_code' => '90100',
            'lat' => 38.1157,
            'lng' => 13.3615,
            'population' => 630_000,
        ],
        [
            'name' => 'Genova',
            'region' => 'Liguria',
            'province' => 'Genova',
            'province_code' => 'GE',
            'postal_code' => '16100',
            'lat' => 44.4056,
            'lng' => 8.9463,
            'population' => 566_000,
        ],
        [
            'name' => 'Bologna',
            'region' => 'Emilia-Romagna',
            'province' => 'Bologna',
            'province_code' => 'BO',
            'postal_code' => '40100',
            'lat' => 44.4949,
            'lng' => 11.3426,
            'population' => 390_000,
        ],
        [
            'name' => 'Firenze',
            'region' => 'Toscana',
            'province' => 'Firenze',
            'province_code' => 'FI',
            'postal_code' => '50100',
            'lat' => 43.7696,
            'lng' => 11.2558,
            'population' => 360_000,
        ],
        [
            'name' => 'Venezia',
            'region' => 'Veneto',
            'province' => 'Venezia',
            'province_code' => 'VE',
            'postal_code' => '30100',
            'lat' => 45.4408,
            'lng' => 12.3155,
            'population' => 250_000,
        ],
        [
            'name' => 'Verona',
            'region' => 'Veneto',
            'province' => 'Verona',
            'province_code' => 'VR',
            'postal_code' => '37100',
            'lat' => 45.4384,
            'lng' => 10.9916,
            'population' => 257_000,
        ],
        [
            'name' => 'Padova',
            'region' => 'Veneto',
            'province' => 'Padova',
            'province_code' => 'PD',
            'postal_code' => '35100',
            'lat' => 45.4064,
            'lng' => 11.8768,
            'population' => 210_000,
        ],
        [
            'name' => 'Mogliano Veneto',
            'region' => 'Veneto',
            'province' => 'Treviso',
            'province_code' => 'TV',
            'postal_code' => '31020',
            'lat' => 45.5597,
            'lng' => 12.2077,
            'population' => 26_000,
        ],
        [
            'name' => 'Brescia',
            'region' => 'Lombardia',
            'province' => 'Brescia',
            'province_code' => 'BS',
            'postal_code' => '25100',
            'lat' => 45.5396,
            'lng' => 10.22,
            'population' => 196_000,
        ],
        [
            'name' => 'Bergamo',
            'region' => 'Lombardia',
            'province' => 'Bergamo',
            'province_code' => 'BG',
            'postal_code' => '24100',
            'lat' => 45.6983,
            'lng' => 9.6773,
            'population' => 121_000,
        ],
        [
            'name' => 'Monza',
            'region' => 'Lombardia',
            'province' => 'Monza e della Brianza',
            'province_code' => 'MB',
            'postal_code' => '20900',
            'lat' => 45.5845,
            'lng' => 9.2736,
            'population' => 123_000,
        ],
        [
            'name' => 'Parma',
            'region' => 'Emilia-Romagna',
            'province' => 'Parma',
            'province_code' => 'PR',
            'postal_code' => '43100',
            'lat' => 44.8015,
            'lng' => 10.3279,
            'population' => 195_000,
        ],
        [
            'name' => 'Modena',
            'region' => 'Emilia-Romagna',
            'province' => 'Modena',
            'province_code' => 'MO',
            'postal_code' => '41100',
            'lat' => 44.6471,
            'lng' => 10.9254,
            'population' => 185_000,
        ],
        [
            'name' => 'Ravenna',
            'region' => 'Emilia-Romagna',
            'province' => 'Ravenna',
            'province_code' => 'RA',
            'postal_code' => '48100',
            'lat' => 44.4184,
            'lng' => 12.2035,
            'population' => 86_000,
        ],
        [
            'name' => 'Trieste',
            'region' => 'Friuli-Venezia Giulia',
            'province' => 'Trieste',
            'province_code' => 'TS',
            'postal_code' => '34100',
            'lat' => 45.6495,
            'lng' => 13.7768,
            'population' => 200_000,
        ],
        [
            'name' => 'Udine',
            'region' => 'Friuli-Venezia Giulia',
            'province' => 'Udine',
            'province_code' => 'UD',
            'postal_code' => '33100',
            'lat' => 46.0639,
            'lng' => 13.2357,
            'population' => 99_000,
        ],
        [
            'name' => 'Livorno',
            'region' => 'Toscana',
            'province' => 'Livorno',
            'province_code' => 'LI',
            'postal_code' => '57100',
            'lat' => 43.5483,
            'lng' => 10.3108,
            'population' => 155_000,
        ],
        [
            'name' => 'Prato',
            'region' => 'Toscana',
            'province' => 'Prato',
            'province_code' => 'PO',
            'postal_code' => '59100',
            'lat' => 43.8777,
            'lng' => 11.1022,
            'population' => 195_000,
        ],
        [
            'name' => 'Ancona',
            'region' => 'Marche',
            'province' => 'Ancona',
            'province_code' => 'AN',
            'postal_code' => '60100',
            'lat' => 43.6158,
            'lng' => 13.5189,
            'population' => 100_000,
        ],
        [
            'name' => 'Pescara',
            'region' => 'Abruzzo',
            'province' => 'Pescara',
            'province_code' => 'PE',
            'postal_code' => '65100',
            'lat' => 42.464,
            'lng' => 14.2141,
            'population' => 120_000,
        ],
        [
            'name' => 'Lecce',
            'region' => 'Puglia',
            'province' => 'Lecce',
            'province_code' => 'LE',
            'postal_code' => '73100',
            'lat' => 40.3527,
            'lng' => 18.1741,
            'population' => 95_000,
        ],
        [
            'name' => 'Bari',
            'region' => 'Puglia',
            'province' => 'Bari',
            'province_code' => 'BA',
            'postal_code' => '70100',
            'lat' => 41.1171,
            'lng' => 16.8719,
            'population' => 315_000,
        ],
        [
            'name' => 'Catania',
            'region' => 'Sicilia',
            'province' => 'Catania',
            'province_code' => 'CT',
            'postal_code' => '95100',
            'lat' => 37.5079,
            'lng' => 15.083,
            'population' => 300_000,
        ],
        [
            'name' => 'Messina',
            'region' => 'Sicilia',
            'province' => 'Messina',
            'province_code' => 'ME',
            'postal_code' => '98100',
            'lat' => 38.1938,
            'lng' => 15.554,
            'population' => 220_000,
        ],
        [
            'name' => 'Cagliari',
            'region' => 'Sardegna',
            'province' => 'Cagliari',
            'province_code' => 'CA',
            'postal_code' => '09100',
            'lat' => 39.2238,
            'lng' => 9.1217,
            'population' => 154_000,
        ],
        [
            'name' => 'Reggio Calabria',
            'region' => 'Calabria',
            'province' => 'Reggio Calabria',
            'province_code' => 'RC',
            'postal_code' => '89100',
            'lat' => 38.1115,
            'lng' => 15.647,
            'population' => 180_000,
        ],
        [
            'name' => 'Perugia',
            'region' => 'Umbria',
            'province' => 'Perugia',
            'province_code' => 'PG',
            'postal_code' => '06100',
            'lat' => 43.1107,
            'lng' => 12.3908,
            'population' => 165_000,
        ],
    ];

    /**
     * @var list<array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}>|null
     */
    private static ?array $resolved = null;

    /**
     * Catalogo completo, con le coordinate di Geo quando disponibili.
     *
     * @return list<array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}>
     */
    public static function all(): array
    {
        if (self::$resolved !== null) {
            return self::$resolved;
        }

        $resolved = [];
        foreach (self::MUNICIPALITIES as $municipality) {
            $resolved[] = self::applyGeoCoordinates($municipality);
        }

        return self::$resolved = $resolved;
    }

    /**
     * @return array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}
     */
    public static function pick(Generator $faker): array
    {
        $all = self::all();

        return $all[SafeIntCastAction::cast($faker->randomElement(array_keys($all)), 0)];
    }

    /**
     * Comune estratto in proporzione alla popolazione: i centri grandi generano
     * piu' segnalazioni, come nella realta'.
     *
     * @return array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}
     */
    public static function pickWeighted(Generator $faker): array
    {
        /** @var non-empty-list<array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}> $municipalities */
        $municipalities = self::all();
        /** @var non-empty-list<int> $weights */
        $weights = array_map(
            static fn (array $municipality): int => max(1, (int) round($municipality['population'] / 1000)),
            $municipalities,
        );

        return DemoWeightedChoice::of($faker, $municipalities, $weights);
    }

    /**
     * Indirizzo stradale coerente con il comune (via/piazza + civico + CAP).
     *
     * @param  array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}  $municipality
     * @return array{address: non-empty-string, street: non-empty-string, street_number: non-empty-string, postal_code: non-empty-string}
     */
    public static function streetAddress(Generator $faker, array $municipality): array
    {
        $street = $faker->boolean(22)
            ? DemoText::pick($faker, self::LANDMARKS)
            : DemoText::pick($faker, self::STREETS);
        $streetNumber = $faker->numberBetween(1, 218);
        $postalCode = $faker->boolean(70)
            ? $municipality['postal_code']
            // Stesso CAP di zona con le due cifre finali diverse: resta un CAP valido.
            : substr($municipality['postal_code'], 0, 3).str_pad((string) $faker->numberBetween(10, 99), 2, '0', STR_PAD_LEFT);

        return [
            'address' => $street.' '.$streetNumber.', '.$municipality['name'],
            'street' => $street,
            'street_number' => (string) $streetNumber,
            'postal_code' => $postalCode,
        ];
    }

    /**
     * Punto casuale dentro il tessuto urbano del comune.
     *
     * @param  array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}  $municipality
     * @return array{lat: float, lng: float}
     */
    public static function nearbyCoordinates(Generator $faker, array $municipality): array
    {
        return [
            'lat' => round($municipality['lat'] + $faker->randomFloat(6, -self::MAX_JITTER_DEGREES, self::MAX_JITTER_DEGREES), 6),
            'lng' => round($municipality['lng'] + $faker->randomFloat(6, -self::MAX_JITTER_DEGREES, self::MAX_JITTER_DEGREES), 6),
        ];
    }

    /**
     * Geo e' la SSoT quando il comune esiste nel dataset del modulo Geo.
     *
     * @param  array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}  $municipality
     * @return array{name: non-empty-string, region: non-empty-string, province: non-empty-string, province_code: non-empty-string, postal_code: non-empty-string, lat: float, lng: float, population: int}
     */
    private static function applyGeoCoordinates(array $municipality): array
    {
        $geo = self::lookupGeo($municipality['name']);
        if ($geo === null) {
            return $municipality;
        }

        $municipality['lat'] = $geo['lat'];
        $municipality['lng'] = $geo['lng'];

        return $municipality;
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private static function lookupGeo(string $name): ?array
    {
        try {
            $comune = Comune::query()->where('nome', $name)->first();
        } catch (\Throwable) {
            // Geo non disponibile: il catalogo curato resta la fonte.
            return null;
        }

        if ($comune === null) {
            return null;
        }

        $lat = is_numeric($comune->lat) ? (float) $comune->lat : null;
        $lng = is_numeric($comune->lng) ? (float) $comune->lng : null;

        if ($lat === null || $lng === null || $lat === 0.0 || $lng === 0.0) {
            return null;
        }

        return ['lat' => $lat, 'lng' => $lng];
    }
}
