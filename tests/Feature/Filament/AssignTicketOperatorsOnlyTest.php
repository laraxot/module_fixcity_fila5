<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Modules\Fixcity\Actions\ListAssignableTicketOperatorsAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ViewTicket;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Policies\BasePolicy;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/*
 * DCP-281 (A-14): il campo «assegna a» di ViewTicket elencava tutti gli utenti, cittadini compresi. Gli assegnatari
 * possibili sono gli utenti con un ruolo PA (`BasePolicy::PA_ROLES`), cioe' gli stessi che `isPaOperator` riconosce.
 */

uses(TestCase::class);

/**
 * @return array{actor: User, operator: User, supervisor: User, citizen: User, panelOnly: User, ticket: Ticket}
 */
function dcp281StaffAndCitizen(): array
{
    $withRole = static function (string $name, string $role): User {
        $user = UserFactory::new()->createOne(['name' => $name]);
        $user->assignRole(Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']));

        return $user;
    };

    $actor = $withRole('Zeta Amministratore DCP281', 'admin');
    $actor->givePermissionTo(collect([
        'media.viewAny', 'media.view', 'media.create', 'media.update', 'media.delete', 'media.restore', 'media.forceDelete',
    ])->map(static fn (string $name): Permission => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'])));
    Filament::setCurrentPanel('fixcity::admin');
    Auth::login($actor);

    return [
        'actor' => $actor,
        'operator' => $withRole('Alfa Operatore DCP281', 'operator'),
        'supervisor' => $withRole('Beta Supervisore DCP281', 'supervisor'),
        'citizen' => UserFactory::new()->createOne(['name' => 'Gamma Cittadino DCP281']),
        'panelOnly' => $withRole('Delta Pannello DCP281', 'fixcity::admin'),
        'ticket' => TicketFactory::new()->createOne(['responsible_id' => null]),
    ];
}

function dcp281Id(User $user): string
{
    return SafeStringCastAction::cast($user->getAuthIdentifier());
}

it('uses the PA roles that the policy already defines as the single source', function (): void {
    expect(BasePolicy::PA_ROLES)->toBe(['operator', 'supervisor', 'admin']);
});

it('lists only users holding a PA role, ordered by name', function (): void {
    ['actor' => $actor, 'operator' => $operator, 'supervisor' => $supervisor] = dcp281StaffAndCitizen();

    $options = app(ListAssignableTicketOperatorsAction::class)->execute();
    $ours = array_filter($options, static fn (string $name): bool => str_contains($name, 'DCP281'));

    expect(array_keys($ours))->toBe([dcp281Id($operator), dcp281Id($supervisor), dcp281Id($actor)]);
});

it('does not offer citizens or panel-only users as assignees in the ticket view', function (): void {
    ['operator' => $operator, 'citizen' => $citizen, 'panelOnly' => $panelOnly, 'ticket' => $ticket] = dcp281StaffAndCitizen();

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->mountAction('assign')
        ->assertSchemaComponentExists('responsible_id', null, static function (Select $field) use ($operator, $citizen, $panelOnly): bool {
            $ids = array_map(strval(...), array_keys($field->getOptions()));

            return in_array(dcp281Id($operator), $ids, true)
                && ! in_array(dcp281Id($citizen), $ids, true)
                && ! in_array(dcp281Id($panelOnly), $ids, true);
        });
});

it('refuses a citizen submitted as the assignee', function (): void {
    ['citizen' => $citizen, 'ticket' => $ticket] = dcp281StaffAndCitizen();

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->callAction('assign', ['responsible_id' => dcp281Id($citizen)])
        ->assertHasActionErrors(['responsible_id']);

    expect($ticket->fresh()?->responsible_id)->toBeNull();
});

it('assigns a PA operator', function (): void {
    Notification::fake();
    ['operator' => $operator, 'ticket' => $ticket] = dcp281StaffAndCitizen();

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->callAction('assign', ['responsible_id' => dcp281Id($operator)])
        ->assertHasNoActionErrors();

    expect(SafeStringCastAction::cast($ticket->fresh()?->responsible_id))->toBe(dcp281Id($operator));
});
