<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildPersonalAreaNotificationsDataAction;
use Modules\Fixcity\Actions\MarkPersonalAreaNotificationsAsReadAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Notifications\TicketAssignedNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Notification;
use Modules\Xot\Contracts\UserContract;

uses(TestCase::class);

/*
 * DCP-111: la pagina `/area-personale/notifiche` legge le notifiche che il sistema scrive davvero (canale `database` di
 * TicketStatusChangedNotification, tabella `notifications`, colonna `read_at`): nessuna tabella nuova, nessun dato inventato.
 * I dati di partenza nascono dal flusso reale (TicketActivity pubblica -> observer -> notifica), non da righe scritte a mano.
 */
function publicStatusChange(Ticket $ticket, TicketStatusEnum $to): void
{
    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::OPEN->value, 'to' => $to->value],
        'visibility' => TicketActivityVisibilityEnum::Public->value,
    ]);
}

function ownedTicket(UserContract $owner, string $code): Ticket
{
    return TicketFactory::new()->createOne(['owner_id' => $owner->getAuthIdentifier(), 'code' => $code]);
}

it('lists the status updates the real flow stored for the citizen, newest first', function (): void {
    $citizen = UserFactory::new()->createOne();
    $first = ownedTicket($citizen, 'TCK-NOTIF-ONE');
    $second = ownedTicket($citizen, 'TCK-NOTIF-TWO');

    publicStatusChange($first, TicketStatusEnum::IN_PROGRESS);
    $this->travel(5)->minutes();
    publicStatusChange($second, TicketStatusEnum::RESOLVED);

    $data = app(BuildPersonalAreaNotificationsDataAction::class)->execute($citizen);

    expect($data['unread_count'])->toBe(2)
        ->and($data['has_more'])->toBeFalse()
        ->and(array_column($data['items'], 'ticket_code'))->toBe(['TCK-NOTIF-TWO', 'TCK-NOTIF-ONE'])
        ->and($data['items'][0]['status_label'])->toBe(TicketStatusEnum::RESOLVED->getLabel())
        ->and($data['items'][0]['is_read'])->toBeFalse()
        ->and($data['items'][0]['track_url'])->toContain('/tickets/track?code=TCK-NOTIF-TWO')
        ->and($data['items'][0]['occurred_at'])->not->toBe('')
        ->and($data['items'][0]['occurred_iso'])->toMatch('/^\d{4}-\d{2}-\d{2}T/');
});

it('never shows notifications of another citizen or of another type', function (): void {
    $citizen = UserFactory::new()->createOne();
    $stranger = UserFactory::new()->createOne();

    publicStatusChange(ownedTicket($stranger, 'TCK-STRANGER-1'), TicketStatusEnum::RESOLVED);
    $citizen->notify(new TicketAssignedNotification(99));

    $data = app(BuildPersonalAreaNotificationsDataAction::class)->execute($citizen);

    expect($data['items'])->toBe([])
        ->and($data['unread_count'])->toBe(0)
        ->and(Notification::query()->count())->toBeGreaterThanOrEqual(2);
});

it('marks one notification as read and only the citizen own ones', function (): void {
    $citizen = UserFactory::new()->createOne();
    $stranger = UserFactory::new()->createOne();
    publicStatusChange(ownedTicket($citizen, 'TCK-MINE-1'), TicketStatusEnum::RESOLVED);
    publicStatusChange(ownedTicket($stranger, 'TCK-THEIRS-1'), TicketStatusEnum::RESOLVED);

    $mine = app(BuildPersonalAreaNotificationsDataAction::class)->execute($citizen)['items'][0]['id'];
    $theirs = app(BuildPersonalAreaNotificationsDataAction::class)->execute($stranger)['items'][0]['id'];

    $action = app(MarkPersonalAreaNotificationsAsReadAction::class);
    expect($action->execute($citizen, $theirs))->toBe(0)
        ->and(app(BuildPersonalAreaNotificationsDataAction::class)->execute($stranger)['unread_count'])->toBe(1)
        ->and($action->execute($citizen, $mine))->toBe(1)
        ->and($action->execute($citizen, $mine))->toBe(0);

    $after = app(BuildPersonalAreaNotificationsDataAction::class)->execute($citizen);
    expect($after['unread_count'])->toBe(0)
        ->and($after['items'][0]['is_read'])->toBeTrue();
});

it('marks all the citizen notifications as read without touching other users', function (): void {
    $citizen = UserFactory::new()->createOne();
    $stranger = UserFactory::new()->createOne();
    publicStatusChange(ownedTicket($citizen, 'TCK-ALL-1'), TicketStatusEnum::IN_PROGRESS);
    publicStatusChange(ownedTicket($citizen, 'TCK-ALL-2'), TicketStatusEnum::RESOLVED);
    publicStatusChange(ownedTicket($stranger, 'TCK-ALL-3'), TicketStatusEnum::RESOLVED);

    expect(app(MarkPersonalAreaNotificationsAsReadAction::class)->execute($citizen))->toBe(2)
        ->and(app(BuildPersonalAreaNotificationsDataAction::class)->execute($citizen)['unread_count'])->toBe(0)
        ->and(app(BuildPersonalAreaNotificationsDataAction::class)->execute($stranger)['unread_count'])->toBe(1);
});

it('caps the list and says when older notifications exist', function (): void {
    $citizen = UserFactory::new()->createOne();
    $ticket = ownedTicket($citizen, 'TCK-CAP-1');
    for ($index = 0; $index < BuildPersonalAreaNotificationsDataAction::LIMIT + 1; $index++) {
        publicStatusChange($ticket, TicketStatusEnum::IN_PROGRESS);
    }

    $data = app(BuildPersonalAreaNotificationsDataAction::class)->execute($citizen);

    expect($data['items'])->toHaveCount(BuildPersonalAreaNotificationsDataAction::LIMIT)
        ->and($data['has_more'])->toBeTrue()
        ->and($data['unread_count'])->toBe(BuildPersonalAreaNotificationsDataAction::LIMIT + 1);
});

it('renders the status label and the tracking link in the current locale', function (string $locale): void {
    $expected = trans('fixcity::ticket_status_enum.values.resolved.label', [], $locale);
    if ($locale !== 'it') {
        expect($expected)->not->toBe(trans('fixcity::ticket_status_enum.values.resolved.label', [], 'it'));
    }
    app()->setLocale($locale);
    $citizen = UserFactory::new()->createOne();
    publicStatusChange(ownedTicket($citizen, 'TCK-LOCALE-1'), TicketStatusEnum::RESOLVED);

    $item = app(BuildPersonalAreaNotificationsDataAction::class)->execute($citizen)['items'][0];

    expect($item['status_label'])->toBe($expected)
        ->and($item['track_url'])->toContain('/'.$locale.'/tickets/track?code=TCK-LOCALE-1');
})->with(['it', 'en', 'de', 'es']);
