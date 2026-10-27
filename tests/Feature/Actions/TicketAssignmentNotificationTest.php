<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\AssignTicketAction;
use Modules\Fixcity\Actions\SetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Notifications\TicketAssignedNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

uses(TestCase::class);

it('notifies the newly assigned operator after the assignment commits', function (): void {
    Notification::fake();

    $owner = UserFactory::new()->createOne();
    $actor = UserFactory::new()->createOne();
    $operator = UserFactory::new()->createOne();
    $actor->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    $this->actingAs($actor);
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'responsible_id' => null,
    ]);

    app(AssignTicketAction::class)->execute(
        $ticket,
        SafeStringCastAction::cast($operator->getAuthIdentifier()),
    );

    Notification::assertSentToTimes($operator, TicketAssignedNotification::class, 1);
    Notification::assertNotSentTo($owner, TicketAssignedNotification::class);
});

it('emails an assignment only to an operator with a verified email address', function (): void {
    $operator = UserFactory::new()->createOne();
    $unverifiedOperator = UserFactory::new()->createOne(['email_verified_at' => null]);
    $emptyEmailOperator = UserFactory::new()->createOne(['email' => '']);
    $notification = new TicketAssignedNotification(123);

    expect($notification->via($operator))->toBe(['database', 'mail'])
        ->and($notification->via($unverifiedOperator))->toBe(['database'])
        ->and($notification->via($emptyEmailOperator))->toBe(['database']);

    Filament::setCurrentPanel('fixcity::admin');

    $mail = $notification->toMail($operator);

    expect($mail->subject)->toBe(__('fixcity::ticket.notification.assignment_title'))
        ->and($mail->introLines)->toContain(__('fixcity::ticket.notification.assigned_to_you', ['ticket_id' => 123]))
        ->and($mail->actionUrl)->toContain('/123');
});

it('keeps in-app assignment notifications when an operator opts out of email', function (): void {
    $operator = UserFactory::new()->createOne();
    app(SetFixcityEmailNotificationPreferenceAction::class)->execute($operator, false);
    $notification = new TicketAssignedNotification(123);

    expect($notification->via($operator))->toBe(['database']);
});
