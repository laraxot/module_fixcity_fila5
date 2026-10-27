<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ViewTicket;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;

/*
 * DCP-281 (A-13): l'azione `changeStatus` del back office offriva tutti i nove stati dell'enum. Deve offrire solo i target
 * della matrice `TicketStatusEnum::allowedTransitions()` dello stato corrente, e non esserci per gli stati terminali.
 */

uses(TestCase::class);

/**
 * Apre la pagina di dettaglio di un ticket nello stato dato come operatore PA.
 */
function dcp281OpenTicketAs(TicketStatusEnum $status): Ticket
{
    $operator = UserFactory::new()->createOne();
    $operator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    $operator->givePermissionTo(collect([
        'media.viewAny', 'media.view', 'media.create', 'media.update', 'media.delete', 'media.restore', 'media.forceDelete',
    ])->map(static fn (string $name): Permission => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'])));
    Filament::setCurrentPanel('fixcity::admin');
    Auth::login($operator);

    return TicketFactory::new()->createOne(['status' => $status]);
}

/**
 * @return array<string, string> value => label, come li costruisce la Select
 */
function dcp281LabelsOf(TicketStatusEnum ...$statuses): array
{
    $labels = [];
    foreach ($statuses as $status) {
        $labels[$status->value] = $status->getLabel();
    }

    return $labels;
}

it('offers exactly the targets the state machine allows from the current state', function (TicketStatusEnum $current): void {
    $ticket = dcp281OpenTicketAs($current);
    $expected = dcp281LabelsOf(...$current->allowedTargets());

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->mountAction('changeStatus')
        ->assertSchemaComponentExists('status', null, static fn (Select $field): bool => $field->getOptions() === $expected);
})->with([
    'in_progress' => TicketStatusEnum::IN_PROGRESS,
    'resolved' => TicketStatusEnum::RESOLVED,
    'pending' => TicketStatusEnum::PENDING,
]);

it('rejects a target the state machine does not allow and leaves the status untouched', function (): void {
    $ticket = dcp281OpenTicketAs(TicketStatusEnum::IN_PROGRESS);

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->callAction('changeStatus', ['status' => TicketStatusEnum::CLOSED->value, 'reason' => 'salto di stato'])
        ->assertHasActionErrors(['status']);

    expect($ticket->fresh()?->resolveTicketStatusValue())->toBe(TicketStatusEnum::IN_PROGRESS->value);
});

it('applies a target the state machine allows', function (): void {
    $ticket = dcp281OpenTicketAs(TicketStatusEnum::IN_PROGRESS);

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->callAction('changeStatus', ['status' => TicketStatusEnum::ON_HOLD->value, 'reason' => 'in attesa di materiali'])
        ->assertHasNoActionErrors();

    expect($ticket->fresh()?->resolveTicketStatusValue())->toBe(TicketStatusEnum::ON_HOLD->value);
});

it('does not offer the action when the current state has no way out', function (TicketStatusEnum $terminal): void {
    $ticket = dcp281OpenTicketAs($terminal);

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertActionHidden('changeStatus');
})->with([
    'closed' => TicketStatusEnum::CLOSED,
    'draft' => TicketStatusEnum::DRAFT,
]);

it('still offers the action while the state has a way out', function (): void {
    $ticket = dcp281OpenTicketAs(TicketStatusEnum::IN_PROGRESS);

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertActionVisible('changeStatus');
});
