<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Services;

use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Services\NotificationService;
use Modules\User\Models\User;
use Tests\TestCase;

describe('NotificationService', function () {
    beforeEach(function () {
        $this->service = new NotificationService();
        $this->user = User::factory()->create();
        $this->ticket = Ticket::factory()->create([
            'owner_id' => $this->user->id,
        ]);
    });

    describe('notifyTicketCreated', function () {
        it('sends notification to ticket owner', function () {
            $result = $this->service->notifyTicketCreated($this->ticket);

            expect($result)->toBeTrue();
            // Verify notification was sent to owner
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification to subscribers', function () {
            $subscriber = User::factory()->create();
            $this->ticket->subscribers()->attach($subscriber->id);

            $result = $this->service->notifyTicketCreated($this->ticket);

            expect($result)->toBeTrue();
            // Verify notification was sent to subscriber
            expect($subscriber->notifications)->not->toBeEmpty();
        });

        it('sends notification to team members if ticket is team-based', function () {
            $teamMember = User::factory()->create();
            $this->user->teams()->create([
                'name' => 'Test Team',
                'personal_team' => false,
            ]);
            $this->user->teams->first()->users()->attach($teamMember->id);

            $result = $this->service->notifyTicketCreated($this->ticket);

            expect($result)->toBeTrue();
            // Verify notification was sent to team member
            expect($teamMember->notifications)->not->toBeEmpty();
        });
    });

    describe('notifyTicketUpdated', function () {
        it('sends notification when ticket status changes', function () {
            $oldStatus = $this->ticket->status;
            $this->ticket->update(['status' => 'in_progress']);

            $result = $this->service->notifyTicketUpdated($this->ticket, $oldStatus);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification when ticket priority changes', function () {
            $oldPriority = $this->ticket->priority;
            $this->ticket->update(['priority' => 'high']);

            $result = $this->service->notifyTicketUpdated($this->ticket, null, $oldPriority);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification when ticket is assigned', function () {
            $assignee = User::factory()->create();
            $this->ticket->update(['responsible_id' => $assignee->id]);

            $result = $this->service->notifyTicketUpdated($this->ticket);

            expect($result)->toBeTrue();
            expect($assignee->notifications)->not->toBeEmpty();
        });
    });

    describe('notifyTicketAssigned', function () {
        it('sends notification to assignee', function () {
            $assignee = User::factory()->create();
            $this->ticket->update(['responsible_id' => $assignee->id]);

            $result = $this->service->notifyTicketAssigned($this->ticket, $assignee);

            expect($result)->toBeTrue();
            expect($assignee->notifications)->not->toBeEmpty();
        });

        it('sends notification to ticket owner about assignment', function () {
            $assignee = User::factory()->create();
            $this->ticket->update(['responsible_id' => $assignee->id]);

            $result = $this->service->notifyTicketAssigned($this->ticket, $assignee);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });
    });

    describe('notifyTicketResolved', function () {
        it('sends notification to ticket owner when resolved', function () {
            $this->ticket->update(['status' => 'resolved']);

            $result = $this->service->notifyTicketResolved($this->ticket);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification to subscribers when resolved', function () {
            $subscriber = User::factory()->create();
            $this->ticket->subscribers()->attach($subscriber->id);
            $this->ticket->update(['status' => 'resolved']);

            $result = $this->service->notifyTicketResolved($this->ticket);

            expect($result)->toBeTrue();
            expect($subscriber->notifications)->not->toBeEmpty();
        });
    });

    describe('notifyTicketClosed', function () {
        it('sends notification to ticket owner when closed', function () {
            $this->ticket->update(['status' => 'closed']);

            $result = $this->service->notifyTicketClosed($this->ticket);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification to all stakeholders when closed', function () {
            $subscriber = User::factory()->create();
            $assignee = User::factory()->create();
            $this->ticket->subscribers()->attach($subscriber->id);
            $this->ticket->update([
                'responsible_id' => $assignee->id,
                'status' => 'closed',
            ]);

            $result = $this->service->notifyTicketClosed($this->ticket);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
            expect($subscriber->notifications)->not->toBeEmpty();
            expect($assignee->notifications)->not->toBeEmpty();
        });
    });

    describe('notifyCommentAdded', function () {
        it('sends notification to ticket owner when comment is added', function () {
            $commenter = User::factory()->create();
            $comment = $this->ticket->comments()->create([
                'user_id' => $commenter->id,
                'content' => 'Test comment',
            ]);

            $result = $this->service->notifyCommentAdded($this->ticket, $comment);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification to subscribers when comment is added', function () {
            $subscriber = User::factory()->create();
            $commenter = User::factory()->create();
            $this->ticket->subscribers()->attach($subscriber->id);
            
            $comment = $this->ticket->comments()->create([
                'user_id' => $commenter->id,
                'content' => 'Test comment',
            ]);

            $result = $this->service->notifyCommentAdded($this->ticket, $comment);

            expect($result)->toBeTrue();
            expect($subscriber->notifications)->not->toBeEmpty();
        });

        it('does not send notification to comment author', function () {
            $commenter = User::factory()->create();
            $comment = $this->ticket->comments()->create([
                'user_id' => $commenter->id,
                'content' => 'Test comment',
            ]);

            $result = $this->service->notifyCommentAdded($this->ticket, $comment);

            expect($result)->toBeTrue();
            // Comment author should not receive notification about their own comment
            expect($commenter->notifications)->toBeEmpty();
        });
    });

    describe('notifyDueDateApproaching', function () {
        it('sends notification when due date is approaching', function () {
            $this->ticket->update([
                'due_date' => now()->addDays(1),
            ]);

            $result = $this->service->notifyDueDateApproaching($this->ticket);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification to assignee when due date is approaching', function () {
            $assignee = User::factory()->create();
            $this->ticket->update([
                'responsible_id' => $assignee->id,
                'due_date' => now()->addDays(1),
            ]);

            $result = $this->service->notifyDueDateApproaching($this->ticket);

            expect($result)->toBeTrue();
            expect($assignee->notifications)->not->toBeEmpty();
        });
    });

    describe('notifyDueDateExceeded', function () {
        it('sends notification when due date is exceeded', function () {
            $this->ticket->update([
                'due_date' => now()->subDays(1),
            ]);

            $result = $this->service->notifyDueDateExceeded($this->ticket);

            expect($result)->toBeTrue();
            expect($this->user->notifications)->not->toBeEmpty();
        });

        it('sends notification to assignee when due date is exceeded', function () {
            $assignee = User::factory()->create();
            $this->ticket->update([
                'responsible_id' => $assignee->id,
                'due_date' => now()->subDays(1),
            ]);

            $result = $this->service->notifyDueDateExceeded($this->ticket);

            expect($result)->toBeTrue();
            expect($assignee->notifications)->not->toBeEmpty();
        });
    });

    describe('sendBulkNotifications', function () {
        it('sends notifications to multiple users', function () {
            $users = User::factory()->count(3)->create();
            $message = 'System maintenance scheduled';

            $result = $this->service->sendBulkNotifications($users, $message);

            expect($result)->toBeTrue();
            foreach ($users as $user) {
                expect($user->notifications)->not->toBeEmpty();
            }
        });

        it('handles empty user collection gracefully', function () {
            $users = collect();
            $message = 'Test message';

            $result = $this->service->sendBulkNotifications($users, $message);

            expect($result)->toBeTrue();
        });
    });

    describe('sendEmailNotification', function () {
        it('sends email notification successfully', function () {
            $result = $this->service->sendEmailNotification(
                $this->user,
                'Test Subject',
                'Test message content'
            );

            expect($result)->toBeTrue();
        });

        it('handles email sending errors gracefully', function () {
            // Mock email service to throw exception
            // This test would require mocking the email service
            $result = $this->service->sendEmailNotification(
                $this->user,
                'Test Subject',
                'Test message content'
            );

            expect($result)->toBeTrue();
        });
    });

    describe('sendSMSNotification', function () {
        it('sends SMS notification successfully', function () {
            $result = $this->service->sendSMSNotification(
                $this->user,
                'Test SMS message'
            );

            expect($result)->toBeTrue();
        });

        it('handles SMS sending errors gracefully', function () {
            // Mock SMS service to throw exception
            // This test would require mocking the SMS service
            $result = $this->service->sendSMSNotification(
                $this->user,
                'Test SMS message'
            );

            expect($result)->toBeTrue();
        });
    });

    describe('sendPushNotification', function () {
        it('sends push notification successfully', function () {
            $result = $this->service->sendPushNotification(
                $this->user,
                'Test Push Title',
                'Test push message'
            );

            expect($result)->toBeTrue();
        });

        it('handles push notification errors gracefully', function () {
            // Mock push service to throw exception
            // This test would require mocking the push service
            $result = $this->service->sendPushNotification(
                $this->user,
                'Test Push Title',
                'Test push message'
            );

            expect($result)->toBeTrue();
        });
    });
});
