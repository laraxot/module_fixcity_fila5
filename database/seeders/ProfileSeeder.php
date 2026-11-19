<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Fixcity\Models\Profile;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Datas\XotData;

/**
 * Profili Fixcity collegati agli utenti demo (connessione fixcity).
 */
class ProfileSeeder extends Seeder
{
    /**
     * @var list<array{email: non-empty-string, first_name: non-empty-string, last_name: non-empty-string}>
     */
    private const array DEMO_PROFILES = [
        [
            'email' => 'marco.sottana@gmail.com',
            'first_name' => 'Marco',
            'last_name' => 'Sottana',
        ],
        [
            'email' => 'cittadino@fixcity.demo',
            'first_name' => 'Cittadino',
            'last_name' => 'Demo',
        ],
    ];

    public function run(): void
    {
        $userClass = XotData::make()->getUserClass();
        $profile = new Profile;

        // Parental hydrates the concrete class from the type column; repair
        // legacy aliases before reading rows so the demo seeder is rerunnable.
        DB::connection($profile->getConnectionName())
            ->table($profile->getTable())
            ->whereIn('type', ['admin', 'citizen'])
            ->update(['type' => Profile::class]);

        foreach (self::DEMO_PROFILES as $demo) {
            $user = $userClass::query()->where('email', $demo['email'])->first();
            if ($user === null) {
                continue;
            }

            $slug = Str::slug($demo['first_name'].'-'.$demo['last_name']);
            $userId = SafeStringCastAction::cast($user->getKey());

            /** @var Profile $profile */
            $profile = Profile::query()->firstOrNew(['user_id' => $userId]);
            if ($profile->uuid === null || $profile->uuid === '') {
                $profile->uuid = SafeStringCastAction::cast((string) Str::uuid());
            }

            $profile->forceFill([
                'user_id' => $userId,
                'type' => Profile::class,
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
