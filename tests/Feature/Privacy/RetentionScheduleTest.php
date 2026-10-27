<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Modules\Activity\Models\Activity;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\AuthenticationLog;

uses(TestCase::class);

/*
 * Privacy notice, "Periodo di conservazione": access and operation logs are kept 12 months, closed accounts are purged.
 * Story: Modules/Fixcity/docs/stories/privacy-notice-retention-exif-account-deletion.story.md
 */

/**
 * Scheduled events of routes/console.php, keyed by command or name.
 *
 * @return array<string, string> label => cron expression
 */
function scheduledPrivacyTasks(): array
{
    Artisan::call('list');
    $tasks = [];
    foreach (app(Schedule::class)->events() as $event) {
        $tasks[(string) ($event->command ?? $event->description)] = $event->expression;
    }

    return $tasks;
}

it('keeps the retention periods of the privacy notice as config values in days', function (): void {
    expect(config('activitylog.clean_after_days'))->toBe(365)
        ->and(config('gdpr.retention.authentication_log_days'))->toBe(365)
        ->and(config('gdpr.retention.deleted_account_days'))->toBeInt()->toBeGreaterThan(0);
});

it('schedules the activity log clean, the access log prune and the deleted account purge daily', function (): void {
    $tasks = scheduledPrivacyTasks();
    $find = static function (string $needle) use ($tasks): ?string {
        foreach ($tasks as $label => $expression) {
            if (str_contains($label, $needle)) {
                return $expression;
            }
        }

        return null;
    };

    expect($find('activitylog:clean'))->toBe('0 0 * * *')
        ->and($find('model:prune'))->toBe('0 0 * * *')
        ->and($find('purge-deleted-accounts'))->toBe('0 0 * * *');
});

it('removes activity log rows older than 12 months and keeps the recent ones', function (): void {
    config(['activitylog.enabled' => true]);
    $old = Activity::query()->create(['log_name' => 'default', 'description' => 'old']);
    $old->forceFill(['created_at' => now()->subDays(366)])->save();
    $recent = Activity::query()->create(['log_name' => 'default', 'description' => 'recent']);
    $recent->forceFill(['created_at' => now()->subDays(300)])->save();

    expect(Artisan::call('activitylog:clean', ['--force' => true]))->toBe(0);

    expect(Activity::query()->whereKey($old->getKey())->exists())->toBeFalse()
        ->and(Activity::query()->whereKey($recent->getKey())->exists())->toBeTrue();
});

it('prunes access log rows older than 12 months and keeps the recent ones', function (): void {
    $user = UserFactory::new()->createOne();
    $old = $user->authentications()->create(['ip_address' => '127.0.0.1', 'login_at' => now()->subDays(400), 'login_successful' => true]);
    $old->forceFill(['created_at' => now()->subDays(400)])->save();
    $recent = $user->authentications()->create(['ip_address' => '127.0.0.1', 'login_at' => now()->subDays(10), 'login_successful' => true]);

    expect(Artisan::call('model:prune', ['--model' => [AuthenticationLog::class]]))->toBe(0);

    expect(AuthenticationLog::query()->whereKey($old->getKey())->exists())->toBeFalse()
        ->and(AuthenticationLog::query()->whereKey($recent->getKey())->exists())->toBeTrue();
});
