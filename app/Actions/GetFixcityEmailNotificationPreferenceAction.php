<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

final class GetFixcityEmailNotificationPreferenceAction
{
    use QueueableAction;

    public function execute(UserContract $user): bool
    {
        $profile = $user->profile()->first();
        if ($profile === null) {
            return true;
        }

        $preferences = $profile->getAttribute('preferences');
        if (! is_array($preferences)) {
            return true;
        }

        $fixcityPreferences = $preferences['fixcity'] ?? null;
        if (! is_array($fixcityPreferences)) {
            return true;
        }

        $enabled = $fixcityPreferences['email_ticket_updates'] ?? null;

        return is_bool($enabled) ? $enabled : true;
    }
}
