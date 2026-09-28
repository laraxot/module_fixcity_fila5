<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\SetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('notifies the owner and followers once for a public status change', function (): void {
    Notification::fake();

    $owner = UserFactory::new()->createOne();
    $follower = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'code' => 'TCK-0123456789ABCDEF',
    ]);
    $ticket->ticketSubscribers()->attach($follower->getAuthIdentifier());
    $ticket->ticketSubscribers()->attach($owner->getAuthIdentifier());

    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::IN_PROGRESS->value, 'to' => TicketStatusEnum::RESOLVED->value],
        'visibility' => TicketActivityVisibilityEnum::Public->value,
    ]);

    Notification::assertSentTo($owner, TicketStatusChangedNotification::class);
    Notification::assertSentTo($follower, TicketStatusChangedNotification::class);
    Notification::assertSentToTimes($owner, TicketStatusChangedNotification::class, 1);
    Notification::assertSentToTimes($follower, TicketStatusChangedNotification::class, 1);
    Notification::assertSentTo($follower, TicketStatusChangedNotification::class, function (TicketStatusChangedNotification $notification) use ($follower, $ticket): bool {
        $data = $notification->toArray($follower);
        $actionUrl = $data['action_url'] ?? '';

        return is_string($actionUrl)
            && str_contains($actionUrl, 'code='.rawurlencode((string) $ticket->code))
            && ! str_contains($actionUrl, 'ticket_id=')
            && ($data['ticket_code'] ?? null) === $ticket->code
            && ! array_key_exists('ticket_id', $data);
    });
});

it('emails public status updates only to users with a verified email address', function (): void {
    $verifiedUser = UserFactory::new()->createOne();
    $unverifiedUser = UserFactory::new()->createOne(['email_verified_at' => null]);
    $emptyEmailUser = UserFactory::new()->createOne(['email' => '']);
    $notification = new TicketStatusChangedNotification('TCK-0123456789ABCDEF', TicketStatusEnum::RESOLVED);

    expect($notification->via($verifiedUser))->toBe(['database', 'mail'])
        ->and($notification->via($unverifiedUser))->toBe(['database'])
        ->and($notification->via($emptyEmailUser))->toBe(['database']);

    $mail = $notification->toMail($verifiedUser);

    expect($mail->subject)->toBe(__('fixcity::ticket.notification.title'))
        ->and($mail->introLines)->toContain(__('fixcity::ticket.notification.status_changed', [
            'ticket_code' => 'TCK-0123456789ABCDEF',
            'status' => TicketStatusEnum::RESOLVED->getLabel(),
        ]))
        ->and($mail->actionUrl)->toContain('code=TCK-0123456789ABCDEF');
});

it('does not notify followers about internal status changes', function (): void {
    Notification::fake();

    $owner = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne(['owner_id' => $owner->getAuthIdentifier()]);

    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::OPEN->value, 'to' => TicketStatusEnum::PENDING->value],
        'visibility' => TicketActivityVisibilityEnum::Internal->value,
    ]);

    Notification::assertNothingSent();
});

it('keeps in-app status updates when the citizen opts out of email', function (): void {
    $user = UserFactory::new()->createOne();
    app(SetFixcityEmailNotificationPreferenceAction::class)->execute($user, false);
    $notification = new TicketStatusChangedNotification('TCK-0123456789ABCDEF', TicketStatusEnum::RESOLVED);

    expect($notification->via($user))->toBe(['database']);
});

it('does not send an unusable tracking notification for legacy tickets without a code', function (): void {
    Notification::fake();

    $owner = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'code' => null,
    ]);

    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::IN_PROGRESS->value, 'to' => TicketStatusEnum::RESOLVED->value],
        'visibility' => TicketActivityVisibilityEnum::Public->value,
    ]);

    Notification::assertNothingSent();
});
