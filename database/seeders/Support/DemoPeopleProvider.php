<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Faker\Generator;
use Illuminate\Database\Eloquent\Model;
use Modules\Fixcity\Database\Seeders\DemoUsersSeeder;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;

/**
 * Utenze demo per la demo investitori: pool di cittadini segnalatori e di operatori
 * comunali, uno per ogni ufficio di DemoCategoryCatalog::DEPARTMENTS.
 *
 * Le utenze sono create una volta sola e riusate da tutti i seeder: un seeder
 * richiamato due volte non duplica nulla, quindi l'orchestratore resta idempotente.
 * L'ufficio di competenza non ha una colonna in `users`: viene portato nel payload
 * delle attivita' del ticket, dove l'SLA lo rende visibile al backoffice.
 *
 * @phpstan-type PersonRecord array{id: int|string, email: non-empty-string, department: non-empty-string|null}
 */
final class DemoPeopleProvider
{
    public const string CITIZEN_PREFIX = 'demo.cittadino.';

    public const string STAFF_PREFIX = 'demo.operatore.';

    public const int CITIZEN_POOL_SIZE = 60;

    /**
     * @var list<PersonRecord>|null
     */
    private static ?array $citizens = null;

    /**
     * @var list<PersonRecord>|null
     */
    private static ?array $staff = null;

    /**
     * Pool dei cittadini segnalatori, creato alla prima invocazione.
     *
     * @return list<PersonRecord>
     */
    public static function citizens(Generator $faker): array
    {
        if (self::$citizens !== null) {
            return self::$citizens;
        }

        $records = [];
        for ($index = 1; $index <= self::CITIZEN_POOL_SIZE; ++$index) {
            $records[] = self::ensureUser(
                $faker,
                self::CITIZEN_PREFIX.$index.'@fixcity.demo',
                'customer_user',
                null,
            );
        }

        return self::$citizens = $records;
    }

    /**
     * Pool degli operatori comunali: un utente per ogni ufficio.
     *
     * @return list<PersonRecord>
     */
    public static function staff(Generator $faker): array
    {
        if (self::$staff !== null) {
            return self::$staff;
        }

        $records = [];
        foreach (DemoCategoryCatalog::DEPARTMENTS as $offset => $department) {
            $records[] = self::ensureUser(
                $faker,
                self::STAFF_PREFIX.($offset + 1).'@fixcity.demo',
                // Un quarto degli uffici opera sul campo: tecnico, non impiegato.
                $offset % 4 === 0 ? 'technician' : 'backoffice_user',
                $department,
            );
        }

        return self::$staff = $records;
    }

    /**
     * Operatore assegnato all'ufficio indicato; ripiega sul primo se non trovato.
     *
     * @return PersonRecord
     */
    public static function staffFor(Generator $faker, string $department): array
    {
        $staff = self::staff($faker);

        foreach ($staff as $record) {
            if ($record['department'] === $department) {
                return $record;
            }
        }

        return $staff[0];
    }

    /**
     * Account operatore "ufficiale" creato da DemoUsersSeeder: e' quello con cui si
     * fa login nella demo, quindi non va mai rimosso dal pool.
     */
    public static function primaryOperatorId(): int|string|null
    {
        /** @var class-string<Model&UserContract> $userModel */
        $userModel = XotData::make()->getUserClass();

        return $userModel::query()->where('email', DemoUsersSeeder::OPERATOR_EMAIL)->value('id');
    }

    /**
     * Azzera le cache dei pool: usato dai seeder che rigenerano l'intero dataset.
     */
    public static function flush(): void
    {
        self::$citizens = null;
        self::$staff = null;
    }

    /**
     * @return PersonRecord
     */
    private static function ensureUser(
        Generator $faker,
        string $email,
        string $type,
        ?string $department,
    ): array {
        /** @var class-string<Model&UserContract> $userModel */
        $userModel = XotData::make()->getUserClass();

        $existing = $userModel::query()->where('email', $email)->first();
        if ($existing !== null) {
            return ['id' => $existing->getKey(), 'email' => $email, 'department' => $department];
        }

        $firstName = self::cleanName($faker->firstName());
        $lastName = self::cleanName($faker->lastName());

        /** @var Model&UserContract $user */
        $user = UserFactory::new()->createOne([
            'email' => $email,
            'name' => $firstName.' '.$lastName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'type' => $type,
            'lang' => 'it',
            'is_active' => true,
            'state' => 'active',
        ]);

        return ['id' => $user->getKey(), 'email' => $email, 'department' => $department];
    }

    /**
     * Faker it_IT prepende talvolta titoli onorifici ("Dr.", "Sig.ra"): non ci
     * stanno in un elenco di operatori comunali.
     */
    private static function cleanName(string $name): string
    {
        $name = trim((string) preg_replace('/\b(Dr|Dott|Sig|Sigra|Sigg|Ing|Avv|Rag|Mssa|Prof)\.?\s+/u', '', $name));

        return $name !== '' ? $name : 'Utente';
    }
}
