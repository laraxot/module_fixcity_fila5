<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Datas\XotData;

/**
 * Utenti demo FO/BO — prerequisito di ProfileSeeder e TicketDatabaseSeeder.
 *
 * Non usa UserDatabaseSeeder completo (DeviceProfile e OAuth non servono alla demo).
 */
class DemoUsersSeeder extends Seeder
{
    public const string CITIZEN_EMAIL = 'cittadino@fixcity.demo';

    public const string OPERATOR_EMAIL = 'operatore@fixcity.demo';

    /**
     * @var list<array{
     *     email: non-empty-string,
     *     first_name: non-empty-string,
     *     last_name: non-empty-string,
     *     type: non-empty-string,
     *     password: non-empty-string
     * }>
     */
    private const array DEMO_USERS = [
        [
            'email' => 'marco.sottana@gmail.com',
            'first_name' => 'Marco',
            'last_name' => 'Sottana',
            // STI User (parental): solo alias in User::$childTypes
            'type' => 'master_admin',
            'password' => 'password',
        ],
        [
            'email' => self::CITIZEN_EMAIL,
            'first_name' => 'Cittadino',
            'last_name' => 'Demo',
            'type' => 'customer_user',
            'password' => 'password',
        ],
        [
            'email' => self::OPERATOR_EMAIL,
            'first_name' => 'Operatore',
            'last_name' => 'Demo',
            'type' => 'customer_user',
            'password' => 'password',
        ],
    ];

    public function run(): void
    {
        $userClass = XotData::make()->getUserClass();

        foreach (self::DEMO_USERS as $demo) {
            $existing = $userClass::query()->where('email', $demo['email'])->first();
            $attributes = [
                'name' => $demo['first_name'].' '.$demo['last_name'],
                'first_name' => $demo['first_name'],
                'last_name' => $demo['last_name'],
                'email_verified_at' => now(),
                'password' => Hash::make($demo['password']),
                'lang' => 'it',
                'is_active' => true,
                'type' => $demo['type'],
                'state' => 'active',
                'is_otp' => false,
            ];

            if ($existing !== null) {
                $existing->fill($attributes)->save();

                continue;
            }

            UserFactory::new()->createOne([
                'email' => $demo['email'],
                ...$attributes,
            ]);
        }

        if ($this->command !== null) {
            $this->command->info('DemoUsersSeeder: utenti demo allineati (password: password).');
        }
    }
}
