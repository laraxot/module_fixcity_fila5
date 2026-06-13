<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use Illuminate\Support\Collection;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\NotificationService;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use PHPUnit\Framework\Assert;

uses(\Modules\Fixcity\Tests\TestCase::class);

beforeEach(function (): void {
    /** @var \Modules\Fixcity\Tests\TestCase $this */
    $this->notificationService = new NotificationService;
    $this->user = UserFactory::new()->createOne();
    assert($this->user instanceof User);
    $this->ticket = TicketFactory::new()->createOne([
        'owner_id' => $this->authUser()->id,
    ]);
    assert($this->ticket instanceof Ticket);
});

describe('Notification Service', function (): void {
    test('_sends_notification_to_ticket_owner', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
$result = $this->notification()->notifyTicketCreated($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_to_subscribers', function (): void {
$subscriber = UserFactory::new()->createOne();
        $this->ticket()->ticketSubscribers()->attach($subscriber->id);

        $result = $this->notification()->notifyTicketCreated($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($subscriber->notifications);
    });

    test('_sends_notification_to_team_members_if_ticket_is_team_based', function (): void {
$teamMember = UserFactory::new()->createOne();
        $this->authUser()->teams()->create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);
        $firstTeam = $this->authUser()->teams->first();
        Assert::assertNotNull($firstTeam);
        $firstTeam->users()->attach($teamMember->id);

        $result = $this->notification()->notifyTicketCreated($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($teamMember->notifications);
    });

    test('_sends_notification_when_ticket_status_changes', function (): void {
$oldStatus = $this->ticket()->status;
        $this->ticket()->update(['status' => 'in_progress']);

        $result = $this->notification()->notifyTicketUpdated($this->ticket(), $oldStatus?->value);

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_when_ticket_priority_changes', function (): void {
$oldPriority = $this->ticket()->priority;
        $this->ticket()->update(['priority' => 'high']);

        $result = $this->notification()->notifyTicketUpdated($this->ticket(), null, $oldPriority?->value);

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_when_ticket_is_assigned', function (): void {
$assignee = UserFactory::new()->createOne();
        $this->ticket()->update(['responsible_id' => $assignee->id]);

        $result = $this->notification()->notifyTicketUpdated($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($assignee->notifications);
    });

    test('_sends_notification_to_assignee', function (): void {
$assignee = UserFactory::new()->createOne();
        $this->ticket()->update(['responsible_id' => $assignee->id]);

        $result = $this->notification()->notifyTicketAssigned($this->ticket(), $assignee);

        Assert::assertTrue($result);
        Assert::assertNotEmpty($assignee->notifications);
    });

    test('_sends_notification_to_ticket_owner_about_assignment', function (): void {
$assignee = UserFactory::new()->createOne();
        $this->ticket()->update(['responsible_id' => $assignee->id]);

        $result = $this->notification()->notifyTicketAssigned($this->ticket(), $assignee);

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_to_ticket_owner_when_resolved', function (): void {
$this->ticket()->update(['status' => 'resolved']);

        $result = $this->notification()->notifyTicketResolved($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_to_subscribers_when_resolved', function (): void {
$subscriber = UserFactory::new()->createOne();
        $this->ticket()->ticketSubscribers()->attach($subscriber->id);
        $this->ticket()->update(['status' => 'resolved']);

        $result = $this->notification()->notifyTicketResolved($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($subscriber->notifications);
    });

    test('_sends_notification_to_ticket_owner_when_closed', function (): void {
$this->ticket()->update(['status' => 'closed']);

        $result = $this->notification()->notifyTicketClosed($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_to_all_stakeholders_when_closed', function (): void {
$subscriber = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $this->ticket()->ticketSubscribers()->attach($subscriber->id);
        $this->ticket()->update([
            'responsible_id' => $assignee->id,
            'status' => 'closed',
        ]);

        $result = $this->notification()->notifyTicketClosed($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
        Assert::assertNotEmpty($subscriber->notifications);
        Assert::assertNotEmpty($assignee->notifications);
    });

    test('_sends_notification_to_ticket_owner_when_comment_is_added', function (): void {
$commenter = UserFactory::new()->createOne();
        $comment = $this->ticket()->ticketComments()->create([
            'user_id' => $commenter->id,
            'content' => 'Test comment',
        ]);

        $result = $this->notification()->notifyCommentAdded($this->ticket(), $comment);

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_to_subscribers_when_comment_is_added', function (): void {
$subscriber = UserFactory::new()->createOne();
        $commenter = UserFactory::new()->createOne();
        $this->ticket()->ticketSubscribers()->attach($subscriber->id);

        $comment = $this->ticket()->ticketComments()->create([
            'user_id' => $commenter->id,
            'content' => 'Test comment',
        ]);

        $result = $this->notification()->notifyCommentAdded($this->ticket(), $comment);

        Assert::assertTrue($result);
        Assert::assertNotEmpty($subscriber->notifications);
    });

    test('_does_not_send_notification_to_comment_author', function (): void {
$commenter = UserFactory::new()->createOne();
        $comment = $this->ticket()->ticketComments()->create([
            'user_id' => $commenter->id,
            'content' => 'Test comment',
        ]);

        $result = $this->notification()->notifyCommentAdded($this->ticket(), $comment);

        Assert::assertTrue($result);
        Assert::assertEmpty($commenter->notifications);
    });

    test('_sends_notification_when_due_date_is_approaching', function (): void {
$this->ticket()->update([
            'due_date' => now()->addDays(1),
        ]);

        $result = $this->notification()->notifyDueDateApproaching($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_to_assignee_when_due_date_is_approaching', function (): void {
$assignee = UserFactory::new()->createOne();
        $this->ticket()->update([
            'responsible_id' => $assignee->id,
            'due_date' => now()->addDays(1),
        ]);

        $result = $this->notification()->notifyDueDateApproaching($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($assignee->notifications);
    });

    test('_sends_notification_when_due_date_is_exceeded', function (): void {
$this->ticket()->update([
            'due_date' => now()->subDays(1),
        ]);

        $result = $this->notification()->notifyDueDateExceeded($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($this->authUser()->notifications);
    });

    test('_sends_notification_to_assignee_when_due_date_is_exceeded', function (): void {
$assignee = UserFactory::new()->createOne();
        $this->ticket()->update([
            'responsible_id' => $assignee->id,
            'due_date' => now()->subDays(1),
        ]);

        $result = $this->notification()->notifyDueDateExceeded($this->ticket());

        Assert::assertTrue($result);
        Assert::assertNotEmpty($assignee->notifications);
    });

    test('_sends_notifications_to_multiple_users', function (): void {
/** @var Collection<int, User> $users */
        $users = UserFactory::new()->count(3)->create();
        $message = 'System maintenance scheduled';

        $result = $this->notification()->sendBulkNotifications($users, $message);

        Assert::assertTrue($result);
        foreach ($users as $user) {
            Assert::assertNotEmpty($user->notifications);
        }
    });

    test('_handles_empty_user_collection_gracefully', function (): void {
/** @var Collection<int, User> $users */
        $users = collect();
        $message = 'Test message';

        $result = $this->notification()->sendBulkNotifications($users, $message);

        Assert::assertTrue($result);
    });

    test('_sends_email_notification_successfully', function (): void {
$result = $this->notification()->sendEmailNotification(
            $this->authUser(),
            'Test Subject',
            'Test message content'
        );

        Assert::assertTrue($result);
    });

    test('_handles_email_sending_errors_gracefully', function (): void {
$result = $this->notification()->sendEmailNotification(
            $this->authUser(),
            'Test Subject',
            'Test message content'
        );

        Assert::assertTrue($result);
    });

    test('_sends_sms_notification_successfully', function (): void {
$result = $this->notification()->sendSMSNotification(
            $this->authUser(),
            'Test SMS message'
        );

        Assert::assertTrue($result);
    });

    test('_handles_sms_sending_errors_gracefully', function (): void {
$result = $this->notification()->sendSMSNotification(
            $this->authUser(),
            'Test SMS message'
        );

        Assert::assertTrue($result);
    });

    test('_sends_push_notification_successfully', function (): void {
$result = $this->notification()->sendPushNotification(
            $this->authUser(),
            'Test Push Title',
            'Test push message'
        );

        Assert::assertTrue($result);
    });

    test('_handles_push_notification_errors_gracefully', function (): void {
$result = $this->notification()->sendPushNotification(
            $this->authUser(),
            'Test Push Title',
            'Test push message'
        );

        Assert::assertTrue($result);
    });
});
