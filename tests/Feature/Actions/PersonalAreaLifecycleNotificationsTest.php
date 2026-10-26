<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Modules\Fixcity\Actions\ApproveTicketAction;
use Modules\Fixcity\Actions\BuildPersonalAreaNotificationsDataAction;
use Modules\Fixcity\Actions\MarkPersonalAreaNotificationsAsReadAction;
use Modules\Fixcity\Actions\RejectTicketAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Notifications\TicketReceivedStaffNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use Modules\User\Models\User;

/*
 * DCP-284: il feed `/area-personale/notifiche` mostrava solo `TicketStatusChangedNotification`. Le notifiche del ciclo di vita
 * (database) devono comparire con l'etichetta dell'evento, essere contate come non lette solo se elencate, e restare del
 * solo proprietario. Il flusso e' quello reale (Action -> attivita' -> observer -> notifica), non righe scritte a mano.
 */

uses(TestCase::class);

/**
 * @return array{0: User, 1: Ticket}
 */
function dcp284FeedOwnerWithTicket(string $code): array
{
    $owner = UserFactory::new()->createOne();

    return [$owner, TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'responsible_id' => null,
        'status' => TicketStatusEnum::PENDING,
        'code' => $code,
    ])];
}

function dcp284FeedOperator(): User
{
    $operator = UserFactory::new()->createOne();
    $operator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    Auth::login($operator);

    return $operator;
}

it('lists lifecycle notifications of the owner with the event label and a coherent unread count', function (): void {
    [$owner, $ticket] = dcp284FeedOwnerWithTicket('TCK-DCP284-FEED1');
    dcp284FeedOperator();

    app(ApproveTicketAction::class)->execute($ticket, 'ok');

    $data = app(BuildPersonalAreaNotificationsDataAction::class)->execute($owner);

    expect($data['items'])->toHaveCount(1)
        ->and($data['unread_count'])->toBe(1)
        ->and($data['items'][0]['ticket_code'])->toBe('TCK-DCP284-FEED1')
        ->and($data['items'][0]['status_label'])->toBe(TicketLifecycleEventEnum::Approved->label())
        ->and($data['items'][0]['is_read'])->toBeFalse()
        ->and($data['items'][0]['track_url'])->toContain('/tickets/track?code=TCK-DCP284-FEED1');
});

it('marks a lifecycle notification as read only for its owner', function (): void {
    [$owner, $ticket] = dcp284FeedOwnerWithTicket('TCK-DCP284-FEED2');
    $stranger = UserFactory::new()->createOne();
    dcp284FeedOperator();
    app(RejectTicketAction::class)->execute($ticket, 'Foto non leggibile');

    $feed = app(BuildPersonalAreaNotificationsDataAction::class);
    $mine = $feed->execute($owner)['items'][0]['id'];
    $mark = app(MarkPersonalAreaNotificationsAsReadAction::class);

    expect($mark->execute($stranger, $mine))->toBe(0)
        ->and($feed->execute($owner)['unread_count'])->toBe(1)
        ->and($mark->execute($owner, $mine))->toBe(1)
        ->and($feed->execute($owner)['unread_count'])->toBe(0)
        ->and($feed->execute($owner)['items'][0]['status_label'])->toBe(TicketLifecycleEventEnum::Rejected->label())
        ->and($feed->execute($stranger)['items'])->toBe([]);
});

it('never shows the PA notifications in the citizen feed', function (): void {
    $operator = UserFactory::new()->createOne();
    $operator->notify(new TicketReceivedStaffNotification(4242));

    $data = app(BuildPersonalAreaNotificationsDataAction::class)->execute($operator);

    expect($data['items'])->toBe([])
        ->and($data['unread_count'])->toBe(0);
});
