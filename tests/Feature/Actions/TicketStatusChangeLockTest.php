<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\ChangeStatus;
use Modules\Fixcity\Actions\CloseTicketAction;
use Modules\Fixcity\Actions\RejectTicketAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Role;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DB-07: due operatori sullo stesso ticket (o un doppio clic) caricano il modello con lo stesso stato. Prima entrambi superavano il
 * controllo sullo stato vecchio e vinceva l'ultima scrittura. Ora lo stato si rilegge con la riga bloccata: il secondo trova lo stato
 * aggiornato e la transizione non consentita fallisce.
 */

function db07Ticket(TicketStatusEnum $status): Ticket
{
    return TicketFactory::new()->createOne(['status' => $status, 'code' => null]);
}

function db07TransitionsOf(Ticket $ticket): int
{
    return TicketActivity::query()->where('ticket_id', $ticket->getKey())->count();
}

beforeEach(function (): void {
    Notification::fake();
    $operator = UserFactory::new()->createOne();
    $operator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    Auth::login($operator);
});

it('rejects a transition decided on a stale copy of the ticket', function (): void {
    $ticket = db07Ticket(TicketStatusEnum::IN_PROGRESS);
    $first = Ticket::query()->whereKey($ticket->getKey())->firstOrFail();
    $second = Ticket::query()->whereKey($ticket->getKey())->firstOrFail();
    $before = db07TransitionsOf($ticket);

    app(ChangeStatus::class)->execute($first, TicketStatusEnum::RESOLVED->value, 'Lavoro concluso');

    // `$second` crede ancora in_progress (in_progress -> on_hold e' consentita), ma il ticket ora e' resolved.
    expect(fn () => app(ChangeStatus::class)->execute($second, TicketStatusEnum::ON_HOLD->value, 'In attesa di materiale'))
        ->toThrow(DomainException::class);

    Assert::assertSame(TicketStatusEnum::RESOLVED, $ticket->refresh()->status);
    Assert::assertSame($before + 1, db07TransitionsOf($ticket));
});

it('does not record the same transition twice on a double click', function (): void {
    $ticket = db07Ticket(TicketStatusEnum::RESOLVED);
    $first = Ticket::query()->whereKey($ticket->getKey())->firstOrFail();
    $second = Ticket::query()->whereKey($ticket->getKey())->firstOrFail();
    $before = db07TransitionsOf($ticket);

    app(CloseTicketAction::class)->execute($first, 'Chiusa dopo verifica');

    expect(fn () => app(CloseTicketAction::class)->execute($second, 'Chiusa dopo verifica'))
        ->toThrow(DomainException::class);
    Assert::assertSame($before + 1, db07TransitionsOf($ticket));
});

it('refuses to reject a ticket that another operator has just put in progress', function (): void {
    $ticket = db07Ticket(TicketStatusEnum::IN_REVIEW);
    $stale = Ticket::query()->whereKey($ticket->getKey())->firstOrFail();

    app(ChangeStatus::class)->execute($ticket, TicketStatusEnum::IN_PROGRESS->value, 'Presa in lavorazione');

    expect(fn () => app(RejectTicketAction::class)->execute($stale, 'Duplicata'))
        ->toThrow(DomainException::class);
    Assert::assertSame(TicketStatusEnum::IN_PROGRESS, $ticket->refresh()->status);
});

it('updates the caller instance with the saved status', function (): void {
    $ticket = db07Ticket(TicketStatusEnum::PENDING);

    app(ChangeStatus::class)->execute($ticket, TicketStatusEnum::IN_REVIEW->value, 'Presa in carico');

    Assert::assertSame(TicketStatusEnum::IN_REVIEW, $ticket->status);
    Assert::assertSame(TicketStatusEnum::IN_REVIEW->value, $ticket->resolveTicketStatusValue());
});
