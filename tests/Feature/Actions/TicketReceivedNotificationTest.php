<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Fixcity\Notifications\TicketReceivedStaffNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/*
 * DCP-284 (A-01): alla creazione il cittadino riceve la conferma e la PA sa che e' arrivata una segnalazione. L'evento
 * `TicketCreatedEvent` era emesso da CreateTicketAction ma nessun listener era registrato.
 */

uses(TestCase::class);

function dcp284StaffMember(string $role): User
{
    $user = UserFactory::new()->createOne();
    $user->assignRole(Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']));

    return $user;
}

function dcp284Create(User $citizen): Ticket
{
    Auth::login($citizen);

    return app(CreateTicketAction::class)->execute([
        'name' => 'Buca in via Roma',
        'content' => 'Buca pericolosa sul marciapiede',
        'status' => TicketStatusEnum::PENDING,
        'owner_id' => $citizen->getAuthIdentifier(),
    ]);
}

it('confirms the reception to the citizen and alerts each PA user once', function (): void {
    Notification::fake();
    $citizen = UserFactory::new()->createOne();
    $operator = dcp284StaffMember('operator');
    $supervisor = dcp284StaffMember('supervisor');
    $bystander = UserFactory::new()->createOne();

    $ticket = dcp284Create($citizen);

    Notification::assertSentToTimes($citizen, TicketLifecycleNotification::class, 1);
    Notification::assertSentTo($citizen, TicketLifecycleNotification::class, function (TicketLifecycleNotification $notification) use ($citizen, $ticket): bool {
        $data = $notification->toArray($citizen);

        return $data['event'] === TicketLifecycleEventEnum::Received->value
            && $data['status'] === TicketStatusEnum::PENDING->value
            && $data['ticket_code'] === $ticket->code;
    });
    Notification::assertSentToTimes($operator, TicketReceivedStaffNotification::class, 1);
    Notification::assertSentToTimes($supervisor, TicketReceivedStaffNotification::class, 1);
    Notification::assertNotSentTo($bystander, TicketReceivedStaffNotification::class);
    Notification::assertNotSentTo($citizen, TicketReceivedStaffNotification::class);
    Notification::assertNotSentTo($operator, TicketLifecycleNotification::class);
});

it('does not alert a PA user about the report that same user filed', function (): void {
    Notification::fake();
    $operator = dcp284StaffMember('operator');
    $other = dcp284StaffMember('admin');

    dcp284Create($operator);

    Notification::assertSentToTimes($operator, TicketLifecycleNotification::class, 1);
    Notification::assertNotSentTo($operator, TicketReceivedStaffNotification::class);
    Notification::assertSentToTimes($other, TicketReceivedStaffNotification::class, 1);
});

it('gives the PA only the ticket number and the back office link', function (): void {
    Notification::fake();
    $citizen = UserFactory::new()->createOne(['email' => 'cittadina.privata@example.test']);
    $operator = dcp284StaffMember('operator');

    $ticket = dcp284Create($citizen);

    Notification::assertSentTo($operator, TicketReceivedStaffNotification::class, function (TicketReceivedStaffNotification $notification) use ($operator, $ticket, $citizen): bool {
        $data = $notification->toArray($operator);
        $text = json_encode($data, JSON_THROW_ON_ERROR);
        $keys = array_keys($data);
        sort($keys);

        return $keys === ['action_url', 'body', 'ticket_id', 'title']
            && $data['ticket_id'] === $ticket->id
            && is_string($data['action_url'])
            && str_contains($data['action_url'], '/'.$ticket->id)
            && ! str_contains($text, (string) $ticket->code)
            && ! str_contains($text, 'cittadina.privata')
            && ! str_contains($text, SafeStringCastAction::cast($citizen->getAuthIdentifier()))
            && ! str_contains($text, 'Buca');
    });
});

it('still confirms to the citizen when nobody holds a PA role', function (): void {
    Notification::fake();
    $citizen = UserFactory::new()->createOne();

    dcp284Create($citizen);

    Notification::assertSentToTimes($citizen, TicketLifecycleNotification::class, 1);
    Notification::assertNotSentTo($citizen, TicketReceivedStaffNotification::class);
});
