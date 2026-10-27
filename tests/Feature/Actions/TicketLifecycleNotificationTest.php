<?php

declare(strict_types=1);

use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Fixcity\Actions\ApproveTicketAction;
use Modules\Fixcity\Actions\AssignTicketAction;
use Modules\Fixcity\Actions\CloseTicketAction;
use Modules\Fixcity\Actions\RejectTicketAction;
use Modules\Fixcity\Actions\SetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Notifications\TicketAssignedNotification;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

use function Safe\class_implements;

/*
 * DCP-284 (A-01): approvata, scartata, presa in carico e chiusa notificano il proprietario. Prima nessuno di questi eventi
 * (event_type diverso da `status_change`) produceva un messaggio. Il canale `status_change` pubblico resta di
 * NotifyTicketSubscribersOfStatusChangeAction: i due insiemi sono disgiunti, quindi un cambio di stato non da' due messaggi.
 */

uses(TestCase::class);

function dcp284ActAsOperator(): User
{
    $operator = UserFactory::new()->createOne();
    $operator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    Auth::login($operator);

    return $operator;
}

function dcp284Ticket(User $owner, TicketStatusEnum $status, ?string $code = 'TCK-DCP284-0001'): Ticket
{
    return TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'responsible_id' => null,
        'status' => $status,
        'code' => $code,
    ]);
}

/**
 * @return array<string, mixed>
 */
function dcp284Data(TicketLifecycleNotification $notification, User $recipient): array
{
    // Quello che fa NotificationSender al momento dell'invio: la lingua della notifica vale solo per il suo testo.
    $previous = app()->getLocale();
    app()->setLocale($notification->locale ?? $previous);

    try {
        return $notification->toArray($recipient);
    } finally {
        app()->setLocale($previous);
    }
}

it('tells the owner once that the report was approved', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $ticket = dcp284Ticket($owner, TicketStatusEnum::PENDING);
    dcp284ActAsOperator();

    app(ApproveTicketAction::class)->execute($ticket, 'Verificata sul posto');

    Notification::assertSentToTimes($owner, TicketLifecycleNotification::class, 1);
    Notification::assertSentTo($owner, TicketLifecycleNotification::class, function (TicketLifecycleNotification $notification) use ($owner): bool {
        $data = dcp284Data($notification, $owner);

        return $data['event'] === TicketLifecycleEventEnum::Approved->value
            && $data['status'] === TicketStatusEnum::IN_REVIEW->value
            && $data['ticket_code'] === 'TCK-DCP284-0001'
            && is_string($data['action_url'])
            && str_contains($data['action_url'], 'code=TCK-DCP284-0001');
    });
    Notification::assertNotSentTo($owner, TicketStatusChangedNotification::class);
});

it('tells the owner why the report was rejected and nobody else', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $follower = UserFactory::new()->createOne();
    $ticket = dcp284Ticket($owner, TicketStatusEnum::PENDING);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $follower->getAuthIdentifier()]);
    $operator = dcp284ActAsOperator();

    app(RejectTicketAction::class)->execute($ticket, 'Foto non leggibile');

    Notification::assertSentToTimes($owner, TicketLifecycleNotification::class, 1);
    Notification::assertSentTo($owner, TicketLifecycleNotification::class, function (TicketLifecycleNotification $notification) use ($owner): bool {
        $data = dcp284Data($notification, $owner);

        return $data['event'] === TicketLifecycleEventEnum::Rejected->value
            && is_string($data['body'])
            && str_contains($data['body'], 'Foto non leggibile');
    });
    Notification::assertNotSentTo($follower, TicketLifecycleNotification::class);
    Notification::assertNotSentTo($operator, TicketLifecycleNotification::class);
});

it('tells the owner only about the first assignment and never names the operator', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $first = UserFactory::new()->createOne(['name' => 'Operatore Segreto Uno']);
    $second = UserFactory::new()->createOne(['name' => 'Operatore Segreto Due']);
    $ticket = dcp284Ticket($owner, TicketStatusEnum::PENDING);
    dcp284ActAsOperator();
    $assign = app(AssignTicketAction::class);

    $assign->execute($ticket, SafeStringCastAction::cast($first->getAuthIdentifier()));

    Notification::assertSentToTimes($owner, TicketLifecycleNotification::class, 1);
    Notification::assertSentToTimes($first, TicketAssignedNotification::class, 1);
    Notification::assertSentTo($owner, TicketLifecycleNotification::class, function (TicketLifecycleNotification $notification) use ($owner, $first): bool {
        $data = dcp284Data($notification, $owner);
        $text = json_encode($data, JSON_THROW_ON_ERROR);

        return $data['event'] === TicketLifecycleEventEnum::Assigned->value
            && ! str_contains($text, 'Operatore Segreto')
            && ! str_contains($text, SafeStringCastAction::cast($first->getAuthIdentifier()));
    });

    $assign->execute($ticket->refresh(), SafeStringCastAction::cast($second->getAuthIdentifier()));
    $assign->execute($ticket->refresh(), null);

    Notification::assertSentToTimes($owner, TicketLifecycleNotification::class, 1);
    Notification::assertSentToTimes($second, TicketAssignedNotification::class, 1);
});

it('tells the owner once that the report was closed', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $ticket = dcp284Ticket($owner, TicketStatusEnum::RESOLVED);
    dcp284ActAsOperator();

    app(CloseTicketAction::class)->execute($ticket, 'Lavori conclusi');

    Notification::assertSentToTimes($owner, TicketLifecycleNotification::class, 1);
    Notification::assertSentTo($owner, TicketLifecycleNotification::class, function (TicketLifecycleNotification $notification) use ($owner): bool {
        $data = dcp284Data($notification, $owner);

        return $data['event'] === TicketLifecycleEventEnum::Closed->value
            && $data['status'] === TicketStatusEnum::CLOSED->value;
    });
});

it('keeps the public status change on its own notification without a second message', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $ticket = dcp284Ticket($owner, TicketStatusEnum::IN_PROGRESS);

    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::IN_PROGRESS->value, 'to' => TicketStatusEnum::RESOLVED->value],
        'visibility' => TicketActivityVisibilityEnum::Public->value,
    ]);

    Notification::assertSentToTimes($owner, TicketStatusChangedNotification::class, 1);
    Notification::assertNotSentTo($owner, TicketLifecycleNotification::class);
});

it('does not message anyone for an added attachment', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $ticket = dcp284Ticket($owner, TicketStatusEnum::PENDING);

    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::MediaAdded->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::PENDING->value, 'to' => TicketStatusEnum::PENDING->value, 'file_name' => 'foto.jpg'],
        'visibility' => TicketActivityVisibilityEnum::AuthorOnly->value,
    ]);

    Notification::assertNothingSent();
});

it('does not fail or send when the ticket has no tracking code or no owner', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $withoutCode = dcp284Ticket($owner, TicketStatusEnum::PENDING, null);
    $orphan = TicketFactory::new()->createOne([
        'owner_id' => (string) Str::uuid(),
        'responsible_id' => null,
        'status' => TicketStatusEnum::PENDING,
        'code' => 'TCK-DCP284-ORPHAN',
    ]);
    dcp284ActAsOperator();

    app(ApproveTicketAction::class)->execute($withoutCode, 'ok');
    app(ApproveTicketAction::class)->execute($orphan, 'ok');

    Notification::assertNothingSent();
});

it('carries the language of the recipient, even when an operator in another language triggers it', function (): void {
    Notification::fake();
    $german = UserFactory::new()->createOne(['lang' => 'de']);
    $unknown = UserFactory::new()->createOne(['lang' => 'xx']);
    $first = dcp284Ticket($german, TicketStatusEnum::PENDING, 'TCK-DCP284-DE');
    $second = dcp284Ticket($unknown, TicketStatusEnum::PENDING, 'TCK-DCP284-XX');
    dcp284ActAsOperator();
    App::setLocale('it');

    app(ApproveTicketAction::class)->execute($first, 'ok');
    app(ApproveTicketAction::class)->execute($second, 'ok');

    Notification::assertSentTo($german, TicketLifecycleNotification::class, function (TicketLifecycleNotification $notification) use ($german): bool {
        $title = dcp284Data($notification, $german)['title'];

        return $notification->locale === 'de' && is_string($title) && str_contains($title, 'genehmigt');
    });
    Notification::assertSentTo($unknown, TicketLifecycleNotification::class, static fn (TicketLifecycleNotification $notification): bool => $notification->locale === 'it');
});

it('puts a rejection reason in the email without letting it inject markup or links', function (): void {
    $owner = UserFactory::new()->createOne();
    $reason = '<script>alert(1)</script> [accedi qui](https://evil.example/login) ![p](https://evil.example/p.png)';
    $notification = new TicketLifecycleNotification('TCK-DCP284-MAIL', TicketLifecycleEventEnum::Rejected, TicketStatusEnum::PENDING, $reason);

    $html = (string) $notification->toMail($owner)->render();

    expect($html)->not->toContain('<script')
        ->and($html)->not->toContain('<img src="https://evil.example')
        ->and($html)->not->toContain('href="https://evil.example')
        ->and($html)->toContain('alert(1)')
        ->and($html)->toContain('code=TCK-DCP284-MAIL');
});

it('stores a bounded reason and chooses mail only for a verified opted-in address', function (): void {
    $verified = UserFactory::new()->createOne();
    $unverified = UserFactory::new()->createOne(['email_verified_at' => null]);
    $optedOut = UserFactory::new()->createOne();
    app(SetFixcityEmailNotificationPreferenceAction::class)->execute($optedOut, false);
    $notification = new TicketLifecycleNotification('TCK-DCP284-VIA', TicketLifecycleEventEnum::Rejected, TicketStatusEnum::PENDING, str_repeat('a', 900));

    expect($notification->via($verified))->toBe(['database', 'mail'])
        ->and($notification->via($unverified))->toBe(['database'])
        ->and($notification->via($optedOut))->toBe(['database'])
        ->and(mb_strlen(SafeStringCastAction::cast(dcp284Data($notification, $verified)['body'])))->toBeLessThan(500);
});

it('queues after commit with bounded retries', function (): void {
    $notification = new TicketLifecycleNotification('TCK-DCP284-Q', TicketLifecycleEventEnum::Closed, TicketStatusEnum::CLOSED);
    $job = new SendQueuedNotifications(collect(), $notification);

    expect(class_implements($notification))->toContain(ShouldQueueAfterCommit::class)
        ->and($job->tries)->toBe(3)
        ->and($job->backoff())->toBe([60, 300])
        ->and($job->afterCommit)->toBeTrue();
});
