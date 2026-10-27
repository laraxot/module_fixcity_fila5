<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\Privacy\PurgeDeletedAccountsAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;

uses(TestCase::class);

/*
 * Privacy notice: a deleted account is deactivated at once, kept only while its open reports are handled, then deleted.
 * Story: Modules/Fixcity/docs/stories/privacy-notice-retention-exif-account-deletion.story.md
 */
beforeEach(function (): void {
    config(['activitylog.enabled' => false, 'gdpr.retention.deleted_account_days' => 30]);
});

function deletedCitizen(int $daysAgo): UserContract
{
    $user = UserFactory::new()->createOne();
    $user->forceFill(['deleted_at' => now()->subDays($daysAgo)])->save();
    XotData::make()->getProfileClass()::query()->create(['user_id' => $user->getKey(), 'email' => $user->email]);

    return $user;
}

function userExists(UserContract $user): bool
{
    return XotData::make()->getUserClass()::query()->whereKey($user->getKey())->exists();
}

it('deletes an account deleted longer ago than the retention, with its profile, and keeps its closed reports', function (): void {
    $user = deletedCitizen(31);
    $ticket = TicketFactory::new()->createOne(['owner_id' => $user->getKey(), 'status' => TicketStatusEnum::CLOSED]);

    $purged = app(PurgeDeletedAccountsAction::class)->execute();

    expect($purged)->toBe(1)
        ->and(userExists($user))->toBeFalse()
        ->and(XotData::make()->getProfileClass()::query()->where('user_id', $user->getKey())->exists())->toBeFalse()
        ->and(Ticket::query()->whereKey($ticket->getKey())->exists())->toBeTrue();
});

it('keeps an account deleted less than the retention ago', function (): void {
    $user = deletedCitizen(29);

    app(PurgeDeletedAccountsAction::class)->execute();

    expect(userExists($user))->toBeTrue();
});

it('keeps a deleted account while one of its reports is still open', function (): void {
    $user = deletedCitizen(90);
    TicketFactory::new()->createOne(['owner_id' => $user->getKey(), 'status' => TicketStatusEnum::IN_PROGRESS]);

    app(PurgeDeletedAccountsAction::class)->execute();

    expect(userExists($user))->toBeTrue();
});

it('never touches an active account', function (): void {
    $user = UserFactory::new()->createOne();

    app(PurgeDeletedAccountsAction::class)->execute();

    expect(userExists($user))->toBeTrue();
});
