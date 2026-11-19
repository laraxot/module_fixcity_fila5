<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildPersonalAreaNotificationsDataAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use PHPUnit\Framework\Assert;

use function Safe\json_encode;

uses(TestCase::class);

/*
 * DCP-287 (FO-2-18): il codice di tracciamento e' una capability. Chi segue una segnalazione di altri riceve gli aggiornamenti di
 * stato, ma la notifica (database e mail) porta il titolo e il link al dettaglio pubblico, mai il codice. Il proprietario
 * continua a ricevere il codice e il link di tracciamento. Il flusso e' quello reale: attivita' pubblica -> observer -> notifica.
 */
function followerNotificationUser(): User
{
    $user = UserFactory::new()->createOne();
    Assert::assertInstanceOf(User::class, $user);

    return $user;
}

/**
 * Contenuto (JSON, senza escape degli slash) di ogni notifica registrata per l'utente.
 *
 * @return list<string>
 */
function followerNotificationPayloads(User $user): array
{
    $payloads = [];
    foreach ($user->notifications()->get() as $notification) {
        $payloads[] = json_encode($notification->getAttribute('data'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    return $payloads;
}

function followerNotificationTicket(User $owner, User $follower): Ticket
{
    $ticket = TicketFactory::new()->createOne([
        'name' => 'Lampione spento in via Verdi',
        'owner_id' => $owner->getKey(),
        'status' => TicketStatusEnum::IN_PROGRESS,
        'code' => 'TCK-FOLLOWER-NOTIF1',
    ]);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $follower->getKey()]);

    return $ticket;
}

function followerNotificationStatusChange(Ticket $ticket): void
{
    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::IN_PROGRESS->value, 'to' => TicketStatusEnum::RESOLVED->value],
        'visibility' => TicketActivityVisibilityEnum::Public->value,
    ]);
}

it('stores for a follower a notification without the tracking code and with the link to the public detail', function (): void {
    $owner = followerNotificationUser();
    $follower = followerNotificationUser();
    $ticket = followerNotificationTicket($owner, $follower);

    followerNotificationStatusChange($ticket);

    $payloads = followerNotificationPayloads($follower);
    Assert::assertCount(1, $payloads);
    Assert::assertStringNotContainsString('TCK-FOLLOWER-NOTIF1', $payloads[0]);
    Assert::assertStringNotContainsString('code=', $payloads[0]);
    Assert::assertStringContainsString('/tickets/'.$ticket->id, $payloads[0]);
    Assert::assertStringContainsString('Lampione spento in via Verdi', $payloads[0]);
    Assert::assertStringContainsString('TCK-FOLLOWER-NOTIF1', followerNotificationPayloads($owner)[0] ?? '', 'il proprietario riceve ancora il codice');
});

it('lists the follower notification by title, linked to the detail, and the owner one by code, linked to the tracking', function (): void {
    $owner = followerNotificationUser();
    $follower = followerNotificationUser();
    $ticket = followerNotificationTicket($owner, $follower);
    followerNotificationStatusChange($ticket);
    $feed = app(BuildPersonalAreaNotificationsDataAction::class);

    $followerItems = $feed->execute($follower)['items'];
    $ownerItems = $feed->execute($owner)['items'];

    Assert::assertCount(1, $followerItems);
    Assert::assertSame('', $followerItems[0]['ticket_code']);
    Assert::assertStringContainsString('Lampione spento in via Verdi', $followerItems[0]['ticket_label']);
    Assert::assertStringEndsWith('/tickets/'.$ticket->id, $followerItems[0]['track_url']);
    Assert::assertStringNotContainsString('TCK-FOLLOWER-NOTIF1', json_encode($followerItems));
    Assert::assertSame(TicketStatusEnum::RESOLVED->getLabel(), $followerItems[0]['status_label']);

    Assert::assertCount(1, $ownerItems);
    Assert::assertSame('TCK-FOLLOWER-NOTIF1', $ownerItems[0]['ticket_code']);
    Assert::assertSame('TCK-FOLLOWER-NOTIF1', $ownerItems[0]['ticket_label']);
    Assert::assertStringContainsString('/tickets/track?code=TCK-FOLLOWER-NOTIF1', $ownerItems[0]['track_url']);
});

it('keeps the tracking code out of the notifications page and of the email of a follower', function (): void {
    /** @var TestCase $this */
    $owner = followerNotificationUser();
    $follower = followerNotificationUser();
    $ticket = followerNotificationTicket($owner, $follower);
    followerNotificationStatusChange($ticket);

    $this->actingAs($follower)->get('/it/area-personale/notifiche')
        ->assertOk()
        ->assertSee('Lampione spento in via Verdi')
        ->assertDontSee('TCK-FOLLOWER-NOTIF1')
        ->assertDontSee('/tickets/track?code=', false);

    $mail = TicketStatusChangedNotification::forFollower($ticket, TicketStatusEnum::RESOLVED)->toMail($follower);
    Assert::assertStringContainsString('/tickets/'.$ticket->id, (string) $mail->actionUrl);
    Assert::assertStringNotContainsString('code=', (string) $mail->actionUrl);
    Assert::assertStringNotContainsString('TCK-FOLLOWER-NOTIF1', json_encode($mail->introLines).' '.$mail->subject);
});

it('sends the owner who also follows the own report one notification, with the code', function (): void {
    $owner = followerNotificationUser();
    $ticket = followerNotificationTicket($owner, $owner);

    followerNotificationStatusChange($ticket);

    $payloads = followerNotificationPayloads($owner);
    Assert::assertCount(1, $payloads);
    Assert::assertStringContainsString('TCK-FOLLOWER-NOTIF1', $payloads[0]);
});
