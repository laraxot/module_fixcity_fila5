<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Livewire\Livewire;
use Modules\Fixcity\Actions\DeleteTicketAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\EditTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ListTickets;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ViewTicket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

uses(TestCase::class);

function grantFixcityMediaPermissions(User $user): void
{
    $permissions = collect([
        'media.viewAny',
        'media.view',
        'media.create',
        'media.update',
        'media.delete',
        'media.restore',
        'media.forceDelete',
    ])->map(static fn (string $name): Permission => Permission::firstOrCreate([
        'name' => $name,
        'guard_name' => 'web',
    ]));

    $user->givePermissionTo($permissions);
}

it('hides ticket deletion from an ordinary PA operator', function (): void {
    $operator = UserFactory::new()->createOne();
    $operator->assignRole(Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']));
    grantFixcityMediaPermissions($operator);
    $ticket = TicketFactory::new()->createOne();
    Filament::setCurrentPanel('fixcity::admin');
    $this->actingAs($operator);

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertSuccessful()
        ->assertActionHidden('delete');

    Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertSuccessful()
        ->assertActionHidden('delete');

    Livewire::test(ListTickets::class)
        ->assertTableBulkActionHidden('delete');
});

it('shows soft-delete to an administrator', function (): void {
    $administrator = UserFactory::new()->createOne();
    $administrator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    grantFixcityMediaPermissions($administrator);
    $ticket = TicketFactory::new()->createOne();
    Filament::setCurrentPanel('fixcity::admin');
    $this->actingAs($administrator);

    Livewire::test(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertSuccessful()
        ->assertActionVisible('delete');

    Livewire::test(EditTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertSuccessful()
        ->assertActionVisible('delete');
});

it('records the deleting administrator when applying soft delete', function (): void {
    $administrator = UserFactory::new()->createOne();
    $administrator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    $ticket = TicketFactory::new()->createOne();
    Filament::setCurrentPanel('fixcity::admin');
    $this->actingAs($administrator);

    app(DeleteTicketAction::class)->execute($ticket);

    expect($ticket->fresh()?->deleted_at)->not->toBeNull()
        ->and(SafeStringCastAction::cast($ticket->fresh()?->deleted_by))
        ->toBe(SafeStringCastAction::cast($administrator->getAuthIdentifier()));
});

it('records the deleting administrator for each bulk soft delete', function (): void {
    $administrator = UserFactory::new()->createOne();
    $administrator->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    grantFixcityMediaPermissions($administrator);
    $ticket = TicketFactory::new()->createOne();
    Filament::setCurrentPanel('fixcity::admin');
    $this->actingAs($administrator);

    Livewire::test(ListTickets::class)
        ->assertTableBulkActionVisible('delete')
        ->callTableBulkAction('delete', [$ticket->getKey()]);

    expect($ticket->fresh()?->deleted_at)->not->toBeNull()
        ->and(SafeStringCastAction::cast($ticket->fresh()?->deleted_by))
        ->toBe(SafeStringCastAction::cast($administrator->getAuthIdentifier()));
});
