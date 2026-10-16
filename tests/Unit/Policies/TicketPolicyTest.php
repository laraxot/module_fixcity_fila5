<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Policies\TicketPolicy;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Contracts\UserContract;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * @param  list<string>  $roles
 * @param  list<string>  $permissions
 * @return MockInterface&UserContract
 */
function ticketPolicyUser(array $roles = [], array $permissions = [], ?string $identifier = null): UserContract
{
    /** @var MockInterface&UserContract $user */
    $user = Mockery::mock(UserContract::class);
    $user->shouldReceive('hasRole')
        ->andReturnUsing(static function (mixed $requested) use ($roles): bool {
            $requestedRoles = is_string($requested) ? [$requested] : $requested;
            if (! is_array($requestedRoles)) {
                return false;
            }
            $requestedRoles = array_values(array_filter($requestedRoles, is_string(...)));

            return array_intersect($requestedRoles, $roles) !== [];
        });
    $user->shouldReceive('hasPermissionToOrCreate')
        ->andReturnUsing(static fn (string $permission): bool => in_array($permission, $permissions, true));
    $user->shouldReceive('getAuthIdentifier')->andReturn($identifier);

    return $user;
}

it('allows PA operators to assign tickets and change their status and priority', function (): void {
    $policy = new TicketPolicy;
    $ticket = new Ticket;
    $user = ticketPolicyUser(roles: ['operator']);

    Assert::assertTrue($policy->assign($user, $ticket));
    Assert::assertTrue($policy->changeStatus($user, $ticket));
    Assert::assertTrue($policy->changePriority($user, $ticket));
});

it('allows supervisors and admins the same PA ticket capabilities', function (): void {
    $policy = new TicketPolicy;
    $ticket = new Ticket;

    foreach (['supervisor', 'admin'] as $role) {
        $user = ticketPolicyUser(roles: [$role]);

        Assert::assertTrue($policy->viewAny($user));
        Assert::assertTrue($policy->assign($user, $ticket));
        Assert::assertTrue($policy->changeStatus($user, $ticket));
        Assert::assertTrue($policy->changePriority($user, $ticket));
    }
});

it('allows ticket deletion only to administrators or an explicit delete permission', function (): void {
    $policy = new TicketPolicy;
    $ticket = new Ticket;

    foreach (['citizen', 'operator', 'supervisor'] as $role) {
        Assert::assertFalse($policy->delete(ticketPolicyUser(roles: [$role]), $ticket));
        Assert::assertFalse($policy->deleteAny(ticketPolicyUser(roles: [$role])));
    }

    Assert::assertTrue($policy->delete(ticketPolicyUser(roles: ['admin']), $ticket));
    Assert::assertTrue($policy->deleteAny(ticketPolicyUser(roles: ['admin'])));
    Assert::assertTrue($policy->delete(ticketPolicyUser(permissions: ['ticket.delete']), $ticket));
    Assert::assertTrue($policy->deleteAny(ticketPolicyUser(permissions: ['ticket.delete'])));
});

it('denies citizens ticket assignment, status changes, and priority changes', function (): void {
    $policy = new TicketPolicy;
    $ticket = new Ticket;
    $user = ticketPolicyUser(roles: ['citizen']);

    Assert::assertFalse($policy->assign($user, $ticket));
    Assert::assertFalse($policy->changeStatus($user, $ticket));
    Assert::assertFalse($policy->changePriority($user, $ticket));
});

it('allows explicit assignment permission without granting status or priority changes', function (): void {
    $policy = new TicketPolicy;
    $ticket = new Ticket;
    $user = ticketPolicyUser(permissions: ['ticket.assign']);

    Assert::assertTrue($policy->assign($user, $ticket));
    Assert::assertFalse($policy->changeStatus($user, $ticket));
    Assert::assertFalse($policy->changePriority($user, $ticket));
});

it('grants a citizen access to their own ticket but not a ticket identified only by audit fields', function (): void {
    $policy = new TicketPolicy;
    $ticket = new class extends Ticket
    {
        public bool $public = false;

        public function isVisibleOnPublicFrontoffice(): bool
        {
            return $this->public;
        }
    };
    $ticket->owner_id = 'citizen-owner';
    $ticket->created_by = 'citizen-auditor';
    $ticket->updated_by = 'citizen-auditor';

    $owner = ticketPolicyUser(identifier: 'citizen-owner');
    $auditActor = ticketPolicyUser(identifier: 'citizen-auditor');

    Assert::assertTrue($policy->view($owner, $ticket));
    Assert::assertTrue($policy->update($owner, $ticket));
    Assert::assertFalse($policy->delete($owner, $ticket));

    Assert::assertFalse($policy->view($auditActor, $ticket));
    Assert::assertFalse($policy->update($auditActor, $ticket));
    Assert::assertFalse($policy->delete($auditActor, $ticket));

    $ticket->public = true;
    Assert::assertTrue($policy->view($auditActor, $ticket));
    Assert::assertFalse($policy->update($auditActor, $ticket));
    Assert::assertFalse($policy->delete($auditActor, $ticket));
});
