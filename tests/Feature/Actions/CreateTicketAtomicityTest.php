<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DB-06: ticket, allegati e codice nascono insieme. Se il salvataggio degli allegati fallisce non resta un ticket a meta' e il cittadino
 * non riceve una ricevuta per una segnalazione che non esiste. DB-10: ogni percorso di creazione ha il codice di tracciamento.
 */
it('rolls back the ticket and sends nothing when saving the attachments fails', function (): void {
    /** @var TestCase $this */
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);

    expect(fn () => app(CreateTicketAction::class)->execute(
        ['name' => 'Buca con foto rifiutata', 'content' => 'La foto non viene accettata.', 'owner_id' => $owner->id],
        'it',
        static function (Ticket $ticket): void {
            throw new RuntimeException('upload failed');
        },
    ))->toThrow(RuntimeException::class, 'upload failed');

    Assert::assertFalse(Ticket::query()->where('name', 'Buca con foto rifiutata')->exists());
    Notification::assertNothingSentTo($owner);
    Assert::assertFalse(Session::has(BuildTicketConfirmationDataAction::SESSION_KEY));
});

it('runs the attachment callback on the persisted ticket before the event', function (): void {
    /** @var TestCase $this */
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);
    $seenInCallback = null;

    $ticket = app(CreateTicketAction::class)->execute(
        ['name' => 'Lampione spento', 'content' => 'Il lampione non si accende.', 'owner_id' => $owner->id],
        'it',
        static function (Ticket $created) use (&$seenInCallback): void {
            $seenInCallback = $created->exists ? $created->getKey() : null;
        },
    );

    Assert::assertSame($ticket->getKey(), $seenInCallback);
    Assert::assertStringStartsWith('TCK-', (string) $ticket->code);
    Notification::assertSentToTimes($owner, TicketLifecycleNotification::class, 1);
});

it('dispatches the created event only after the surrounding transaction commits', function (): void {
    /** @var TestCase $this */
    Event::fake([TicketCreatedEvent::class]);
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);

    DB::connection((new Ticket)->getConnectionName())->transaction(function () use ($owner): void {
        app(CreateTicketAction::class)->execute(
            ['name' => 'Rifiuti abbandonati', 'content' => 'Sacchi abbandonati vicino al cassonetto.', 'status' => TicketStatusEnum::PENDING, 'owner_id' => $owner->id],
            null,
            null,
            false,
        );

        Event::assertNotDispatched(TicketCreatedEvent::class);
    });

    Event::assertDispatched(TicketCreatedEvent::class);
});

it('does not flash the citizen confirmation when asked not to (back office)', function (): void {
    /** @var TestCase $this */
    Notification::fake();
    $operator = UserFactory::new()->createOne();
    $this->actingAs($operator);

    $ticket = app(CreateTicketAction::class)->execute(
        ['name' => 'Segnalazione da sportello', 'content' => 'Telefonata del cittadino.', 'owner_id' => $operator->id],
        null,
        null,
        false,
    );

    Assert::assertNotSame('', (string) $ticket->code);
    Assert::assertFalse(Session::has(BuildTicketConfirmationDataAction::SESSION_KEY));
});
