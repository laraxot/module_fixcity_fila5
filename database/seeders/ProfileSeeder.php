<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Fixcity\Models\Profile;
use Modules\User\Models\User;
use Modules\Xot\Datas\XotData;

/**
 * Profili Fixcity collegati agli utenti demo (connessione fixcity).
 */
class ProfileSeeder extends Seeder
{
    /**
     * @var list<array{email: string, first_name: string, last_name: string, type: string}>
     */
    private const DEMO_PROFILES = [
        [
            'email' => 'marco.sottana@gmail.com',
            'first_name' => 'Marco',
            'last_name' => 'Sottana',
            'type' => 'admin',
        ],
        [
            'email' => 'cittadino@fixcity.demo',
            'first_name' => 'Cittadino',
            'last_name' => 'Demo',
            'type' => 'citizen',
        ],
    ];

    public function run(): void
    {
        $userClass = XotData::make()->getUserClass();
        \assert(is_subclass_of($userClass, User::class));

        foreach (self::DEMO_PROFILES as $demo) {
            /** @var User|null $user */
            $user = $userClass::query()->where('email', $demo['email'])->first();
            if ($user === null) {
                continue;
            }

            $slug = Str::slug($demo['first_name'].'-'.$demo['last_name']);

            /** @var Profile $profile */
            $profile = Profile::query()->firstOrCreate(
                ['user_id' => (string) $user->getKey()],
                ['uuid' => (string) Str::uuid()],
            );

            $profile->fill([
                'type' => $demo['type'],
                'first_name' => $demo['first_name'],
                'last_name' => $demo['last_name'],
                'email' => $demo['email'],
                'slug' => $slug,
                'locale' => 'it',
                'timezone' => 'Europe/Rome',
                'is_active' => true,
                'status' => 'active',
            ])->save();
        }

        if ($this->command !== null) {
            $this->command->info('ProfileSeeder: profili demo Fixcity allineati.');
        }
    }
}
