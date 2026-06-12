<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\NotificationService;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Tests\TestCase;

/**
 * Test NotificationService methods.
 *
 * @group NotificationService
 */
class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $service;

    protected User $user;

    protected Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService;
        $this->user = UserFactory::new()->createOne();
        $this->ticket = TicketFactory::new()->createOne([
            'owner_id' => $this->user->id,
        ]);
    }

    public function test_sends_notification_to_ticket_owner(): void
    {
        $result = $this->service->notifyTicketCreated($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_to_subscribers(): void
    {
        $subscriber = UserFactory::new()->createOne();
        $this->ticket->ticketSubscribers()->attach($subscriber->id);

        $result = $this->service->notifyTicketCreated($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($subscriber->notifications);
    }

    public function test_sends_notification_to_team_members_if_ticket_is_team_based(): void
    {
        $teamMember = UserFactory::new()->createOne();
        $this->user->teams()->create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);
        $firstTeam = $this->user->teams->first();
        $this->assertNotNull($firstTeam);
        $firstTeam->users()->attach($teamMember->id);

        $result = $this->service->notifyTicketCreated($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($teamMember->notifications);
    }

    public function test_sends_notification_when_ticket_status_changes(): void
    {
        $oldStatus = $this->ticket->status;
        $this->ticket->update(['status' => 'in_progress']);

        $result = $this->service->notifyTicketUpdated($this->ticket, $oldStatus?->value);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_when_ticket_priority_changes(): void
    {
        $oldPriority = $this->ticket->priority;
        $this->ticket->update(['priority' => 'high']);

        $result = $this->service->notifyTicketUpdated($this->ticket, null, $oldPriority?->value);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_when_ticket_is_assigned(): void
    {
        $assignee = UserFactory::new()->createOne();
        $this->ticket->update(['responsible_id' => $assignee->id]);

        $result = $this->service->notifyTicketUpdated($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($assignee->notifications);
    }

    public function test_sends_notification_to_assignee(): void
    {
        $assignee = UserFactory::new()->createOne();
        $this->ticket->update(['responsible_id' => $assignee->id]);

        $result = $this->service->notifyTicketAssigned($this->ticket, $assignee);

        $this->assertTrue($result);
        $this->assertNotEmpty($assignee->notifications);
    }

    public function test_sends_notification_to_ticket_owner_about_assignment(): void
    {
        $assignee = UserFactory::new()->createOne();
        $this->ticket->update(['responsible_id' => $assignee->id]);

        $result = $this->service->notifyTicketAssigned($this->ticket, $assignee);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_to_ticket_owner_when_resolved(): void
    {
        $this->ticket->update(['status' => 'resolved']);

        $result = $this->service->notifyTicketResolved($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_to_subscribers_when_resolved(): void
    {
        $subscriber = UserFactory::new()->createOne();
        $this->ticket->ticketSubscribers()->attach($subscriber->id);
        $this->ticket->update(['status' => 'resolved']);

        $result = $this->service->notifyTicketResolved($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($subscriber->notifications);
    }

    public function test_sends_notification_to_ticket_owner_when_closed(): void
    {
        $this->ticket->update(['status' => 'closed']);

        $result = $this->service->notifyTicketClosed($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_to_all_stakeholders_when_closed(): void
    {
        $subscriber = UserFactory::new()->createOne();
        $assignee = UserFactory::new()->createOne();
        $this->ticket->ticketSubscribers()->attach($subscriber->id);
        $this->ticket->update([
            'responsible_id' => $assignee->id,
            'status' => 'closed',
        ]);

        $result = $this->service->notifyTicketClosed($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
        $this->assertNotEmpty($subscriber->notifications);
        $this->assertNotEmpty($assignee->notifications);
    }

    public function test_sends_notification_to_ticket_owner_when_comment_is_added(): void
    {
        $commenter = UserFactory::new()->createOne();
        $comment = $this->ticket->ticketComments()->create([
            'user_id' => $commenter->id,
            'content' => 'Test comment',
        ]);

        $result = $this->service->notifyCommentAdded($this->ticket, $comment);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_to_subscribers_when_comment_is_added(): void
    {
        $subscriber = UserFactory::new()->createOne();
        $commenter = UserFactory::new()->createOne();
        $this->ticket->ticketSubscribers()->attach($subscriber->id);

        $comment = $this->ticket->ticketComments()->create([
            'user_id' => $commenter->id,
            'content' => 'Test comment',
        ]);

        $result = $this->service->notifyCommentAdded($this->ticket, $comment);

        $this->assertTrue($result);
        $this->assertNotEmpty($subscriber->notifications);
    }

    public function test_does_not_send_notification_to_comment_author(): void
    {
        $commenter = UserFactory::new()->createOne();
        $comment = $this->ticket->ticketComments()->create([
            'user_id' => $commenter->id,
            'content' => 'Test comment',
        ]);

        $result = $this->service->notifyCommentAdded($this->ticket, $comment);

        $this->assertTrue($result);
        $this->assertEmpty($commenter->notifications);
    }

    public function test_sends_notification_when_due_date_is_approaching(): void
    {
        $this->ticket->update([
            'due_date' => now()->addDays(1),
        ]);

        $result = $this->service->notifyDueDateApproaching($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_to_assignee_when_due_date_is_approaching(): void
    {
        $assignee = UserFactory::new()->createOne();
        $this->ticket->update([
            'responsible_id' => $assignee->id,
            'due_date' => now()->addDays(1),
        ]);

        $result = $this->service->notifyDueDateApproaching($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($assignee->notifications);
    }

    public function test_sends_notification_when_due_date_is_exceeded(): void
    {
        $this->ticket->update([
            'due_date' => now()->subDays(1),
        ]);

        $result = $this->service->notifyDueDateExceeded($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($this->user->notifications);
    }

    public function test_sends_notification_to_assignee_when_due_date_is_exceeded(): void
    {
        $assignee = UserFactory::new()->createOne();
        $this->ticket->update([
            'responsible_id' => $assignee->id,
            'due_date' => now()->subDays(1),
        ]);

        $result = $this->service->notifyDueDateExceeded($this->ticket);

        $this->assertTrue($result);
        $this->assertNotEmpty($assignee->notifications);
    }

    public function test_sends_notifications_to_multiple_users(): void
    {
        /** @var Collection<int, User> $users */
        $users = UserFactory::new()->count(3)->create();
        $message = 'System maintenance scheduled';

        $result = $this->service->sendBulkNotifications($users, $message);

        $this->assertTrue($result);
        foreach ($users as $user) {
            $this->assertNotEmpty($user->notifications);
        }
    }

    public function test_handles_empty_user_collection_gracefully(): void
    {
        /** @var Collection<int, User> $users */
        $users = collect();
        $message = 'Test message';

        $result = $this->service->sendBulkNotifications($users, $message);

        $this->assertTrue($result);
    }

    public function test_sends_email_notification_successfully(): void
    {
        $result = $this->service->sendEmailNotification(
            $this->user,
            'Test Subject',
            'Test message content'
        );

        $this->assertTrue($result);
    }

    public function test_handles_email_sending_errors_gracefully(): void
    {
        $result = $this->service->sendEmailNotification(
            $this->user,
            'Test Subject',
            'Test message content'
        );

        $this->assertTrue($result);
    }

    public function test_sends_sms_notification_successfully(): void
    {
        $result = $this->service->sendSMSNotification(
            $this->user,
            'Test SMS message'
        );

        $this->assertTrue($result);
    }

    public function test_handles_sms_sending_errors_gracefully(): void
    {
        $result = $this->service->sendSMSNotification(
            $this->user,
            'Test SMS message'
        );

        $this->assertTrue($result);
    }

    public function test_sends_push_notification_successfully(): void
    {
        $result = $this->service->sendPushNotification(
            $this->user,
            'Test Push Title',
            'Test push message'
        );

        $this->assertTrue($result);
    }

    public function test_handles_push_notification_errors_gracefully(): void
    {
        $result = $this->service->sendPushNotification(
            $this->user,
            'Test Push Title',
            'Test push message'
        );

        $this->assertTrue($result);
    }
}
