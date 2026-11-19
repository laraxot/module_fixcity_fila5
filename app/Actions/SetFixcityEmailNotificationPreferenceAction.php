<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

final class SetFixcityEmailNotificationPreferenceAction
{
    use QueueableAction;

    public function execute(UserContract $user, bool $enabled): bool
    {
        return DB::connection($user->getConnectionName())->transaction(function () use ($user, $enabled): bool {
            $profile = $user->profile()->firstOrCreate([]);
            $preferences = $profile->getAttribute('preferences');
            if (! is_array($preferences)) {
                $preferences = [];
            }

            $fixcityPreferences = $preferences['fixcity'] ?? null;
            if (! is_array($fixcityPreferences)) {
                $fixcityPreferences = [];
            }

            $fixcityPreferences['email_ticket_updates'] = $enabled;
            $preferences['fixcity'] = $fixcityPreferences;

            $profile->setAttribute('preferences', $preferences);
            $profile->saveOrFail();

            return $enabled;
        });
    }
}
