<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Notifications\TicketStatusChangedNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * La pagina di conferma dice al cittadino «Abbiamo inviato il riepilogo all'email». Prima nessuna email partiva:
 * TicketCreatedListener non era registrato e lo stato PENDING e' escluso dalle notifiche di cambio stato.
 * Ora la creazione invia la ricevuta al proprietario con la notifica esistente, e la conferma mostra l'indirizzo
 * solo se la ricevuta va davvero per email.
 */
it('sends the owner one receipt when a ticket is created', function (): void {
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

    Notification::assertSentToTimes($owner, TicketStatusChangedNotification::class, 1);
    Notification::assertSentTo(
        $owner,
        TicketStatusChangedNotification::class,
        static function (TicketStatusChangedNotification $notification, array $channels) use ($owner, $ticket): bool {
            $data = $notification->toArray($owner);

            return in_array('mail', $channels, true)
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
        'status' => TicketStatusEnum::PENDING,
        'owner_id' => $verified->id,
    ]);
    $this->actingAs($unverified);
    $notMailed = app(CreateTicketAction::class)->execute([
        'name' => 'Rifiuti abbandonati',
        'status' => TicketStatusEnum::PENDING,
        'owner_id' => $unverified->id,
    ]);

    $confirmation = app(BuildTicketConfirmationDataAction::class);
    Assert::assertSame($verified->email, $confirmation->execute($mailed, 'it')['email']);
    Assert::assertSame('', $confirmation->execute($notMailed, 'it')['email']);
    Notification::assertSentTo(
        $unverified,
        TicketStatusChangedNotification::class,
        static fn (TicketStatusChangedNotification $notification, array $channels): bool => ! in_array('mail', $channels, true),
    );
});
