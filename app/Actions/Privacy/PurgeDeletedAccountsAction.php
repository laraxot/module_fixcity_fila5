<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\Privacy;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\BaseUser;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Datas\XotData;
use Spatie\QueueableAction\QueueableAction;

/**
 * Deletes the accounts their owners asked to delete (Gdpr DeleteAccountAction sets `deleted_at`), as the privacy notice states:
 * kept only while needed to close the open reports, then deleted.
 *
 * An account is purged when `deleted_at` is older than `gdpr.retention.deleted_account_days` and none of its reports is still open
 * (any status but draft, resolved, closed). Profile, notifications and Google links go with it. Reports, comments and ratings stay:
 * the notice keeps them under the municipality's archive plan. Consents and the access log age out on their own retention.
 * Scheduled daily in routes/console.php.
 */
final class PurgeDeletedAccountsAction
{
    use QueueableAction;

    private const array NOT_OPEN = [TicketStatusEnum::DRAFT, TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED];

    /** @return int number of accounts deleted */
    public function execute(): int
    {
        $cutOff = now()->subDays(config()->integer('gdpr.retention.deleted_account_days', 30));
        $purged = 0;

        XotData::make()->getUserClass()::query()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $cutOff)
            ->chunkById(100, function (Collection $users) use (&$purged): void {
                $blocked = $this->ownersWithOpenTickets($users->modelKeys());
                foreach ($users as $user) {
                    if ($user instanceof BaseUser && ! in_array(SafeStringCastAction::cast($user->getKey()), $blocked, true)) {
                        $this->purge($user);
                        $purged++;
                    }
                }
            });

        return $purged;
    }

    /**
     * Tickets live on another database than users: one query per chunk instead of a cross-database whereDoesntHave.
     *
     * @param  array<int|string>  $userIds
     * @return list<string>
     */
    private function ownersWithOpenTickets(array $userIds): array
    {
        $owners = Ticket::query()
            ->whereIn('owner_id', $userIds)
            ->whereNotIn('status', array_map(static fn (TicketStatusEnum $status): string => $status->value, self::NOT_OPEN))
            ->distinct()
            ->pluck('owner_id')
            ->all();

        return array_values(array_map(SafeStringCastAction::cast(...), $owners));
    }

    private function purge(BaseUser $user): void
    {
        $profile = $user->profile()->first();
        if ($profile instanceof Model) {
            $profile->delete();
        }
        $user->notifications()->delete();
        $user->socialiteUsers()->delete();
        $user->delete();
    }
}
