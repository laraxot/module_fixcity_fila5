<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\AssignTicketAction;
use Modules\Fixcity\Actions\ChangeStatus;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/*
 * DCP-281 (A-02): `ticket_activities.user_id` e' uuid; gli utenti hanno id stringa. Le Action che salvavano l'attore con
 * `is_int($actorId) ? $actorId : null` perdevano sempre l'attore della decisione PA.
 */

uses(TestCase::class);

/**
 * Operatore PA autenticato (ruolo `admin`: PA_ROLES di BasePolicy) con il ticket su cui agisce.
 *
 * @return array{actor: User, ticket: Ticket}
 */
function dcp281ActorWithTicket(TicketStatusEnum $status): array
{
    $actor = UserFactory::new()->createOne();
    $actor->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    Auth::login($actor);

    return [
        'actor' => $actor,
        'ticket' => TicketFactory::new()->createOne([
            'owner_id' => UserFactory::new()->createOne()->getAuthIdentifier(),
            'responsible_id' => null,
            'status' => $status,
        ]),
    ];
}

function dcp281RecordedActorId(Ticket $ticket, TicketActivityEventTypeEnum $eventType): ?string
{
    $activity = TicketActivity::query()
        ->where('ticket_id', $ticket->getKey())
        ->where('event_type', $eventType->value)
        ->firstOrFail();

    $userId = $activity->getAttribute('user_id');

    return $userId === null ? null : SafeStringCastAction::cast($userId);
}

it('records the UUID of the acting operator when a ticket is assigned', function (): void {
    Notification::fake();
    ['actor' => $actor, 'ticket' => $ticket] = dcp281ActorWithTicket(TicketStatusEnum::PENDING);
    $assignee = UserFactory::new()->createOne();

    app(AssignTicketAction::class)->execute($ticket, SafeStringCastAction::cast($assignee->getAuthIdentifier()));

    expect(dcp281RecordedActorId($ticket, TicketActivityEventTypeEnum::Assignment))
        ->toBe(SafeStringCastAction::cast($actor->getAuthIdentifier()));
});

it('records the UUID of the acting operator when the status changes', function (): void {
    ['actor' => $actor, 'ticket' => $ticket] = dcp281ActorWithTicket(TicketStatusEnum::PENDING);

    app(ChangeStatus::class)->execute($ticket, TicketStatusEnum::IN_REVIEW->value, 'presa in carico');

    expect(dcp281RecordedActorId($ticket, TicketActivityEventTypeEnum::StatusChange))
        ->toBe(SafeStringCastAction::cast($actor->getAuthIdentifier()));
});
