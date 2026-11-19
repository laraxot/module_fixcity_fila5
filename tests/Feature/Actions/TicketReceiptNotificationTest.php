<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * La pagina di conferma dice al cittadino «Abbiamo inviato il riepilogo all'email». La ricevuta e' UNA sola: la notifica
 * Received del listener di TicketCreatedEvent (DB-05). Prima partiva anche una TicketStatusChangedNotification pending:
 * due email e due righe in "Notifiche". La conferma mostra l'indirizzo solo se la ricevuta va davvero per email.
 */
it('sends the owner exactly one receipt when a ticket is created', function (): void {
    /** @var TestCase $this */
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);

    $ticket = app(CreateTicketAction::class)->execute([
        'name' => 'Lampione spento',
        'content' => 'Il lampione davanti al civico 3 non funziona.',
        'status' => TicketStatusEnum::PENDING,
        'owner_id' => $owner->id,
    ]);

    // una sola notifica al proprietario, di qualunque classe
    /** @var array<class-string, array<string, array<class-string, list<mixed>>>> $sent */
    $sent = Notification::sentNotifications();
    $sentToOwner = $sent[$owner::class][SafeStringCastAction::cast($owner->getKey())] ?? [];
    Assert::assertSame([TicketLifecycleNotification::class], array_keys($sentToOwner));
    Assert::assertCount(1, $sentToOwner[TicketLifecycleNotification::class]);
    Notification::assertNotSentTo($owner, TicketStatusChangedNotification::class);
    Notification::assertSentTo(
        $owner,
        TicketLifecycleNotification::class,
        static function (TicketLifecycleNotification $notification, array $channels) use ($owner, $ticket): bool {
            $data = $notification->toArray($owner);

            return in_array('mail', $channels, true)
                && $data['event'] === TicketLifecycleEventEnum::Received->value
                && $data['ticket_code'] === $ticket->code
                && $data['status'] === TicketStatusEnum::PENDING->value;
        },
    );
});

it('promises the email on the confirmation page only when the receipt is mailed', function (): void {
    /** @var TestCase $this */
    Notification::fake();
    $verified = UserFactory::new()->createOne();
    $unverified = UserFactory::new()->unverified()->createOne();

    $this->actingAs($verified);
    $mailed = app(CreateTicketAction::class)->execute([
        'name' => 'Buca in via Roma',
        // tickets.content e' NOT NULL senza default: MariaDB lo esige (SQLite lo lasciava passare)
        'content' => 'Buca profonda sul marciapiede.',
        'status' => TicketStatusEnum::PENDING,
        'owner_id' => $verified->id,
    ]);
    $this->actingAs($unverified);
    $notMailed = app(CreateTicketAction::class)->execute([
        'name' => 'Rifiuti abbandonati',
        // tickets.content e' NOT NULL senza default: MariaDB lo esige (SQLite lo lasciava passare)
        'content' => 'Sacchi abbandonati vicino al cassonetto.',
        'status' => TicketStatusEnum::PENDING,
        'owner_id' => $unverified->id,
    ]);

    $confirmation = app(BuildTicketConfirmationDataAction::class);
    Assert::assertSame($verified->email, $confirmation->execute($mailed, 'it')['email']);
    Assert::assertSame('', $confirmation->execute($notMailed, 'it')['email']);
    Notification::assertSentTo(
        $unverified,
        TicketLifecycleNotification::class,
        static fn (TicketLifecycleNotification $notification, array $channels): bool => ! in_array('mail', $channels, true),
    );
});
