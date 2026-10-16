<?php

declare(strict_types=1);

use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\SendQueuedNotifications;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Notifications\TicketAssignedNotification;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;

use function Safe\class_implements;

it('configures bounded retry and backoff after the database commit', function (): void {
    $notifications = [
        new TicketAssignedNotification(42),
        new TicketStatusChangedNotification('TCK-0123456789ABCDEF', TicketStatusEnum::RESOLVED),
    ];

    foreach ($notifications as $notification) {
        expect(class_implements($notification))->toContain(ShouldQueueAfterCommit::class);

        $job = new SendQueuedNotifications(collect(), $notification);

        expect($job->tries)->toBe(3)
            ->and($job->backoff())->toBe([60, 300])
            ->and($job->afterCommit)->toBeTrue();
    }
});
