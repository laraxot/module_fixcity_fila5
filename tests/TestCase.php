<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\User;
use Modules\Fixcity\Services\NotificationService;
use Modules\Fixcity\Services\TicketService;
use Modules\Fixcity\Services\WorkflowService;
use Modules\User\Models\User as AuthUser;
use Modules\Xot\Tests\XotBaseTestCase;
use PHPUnit\Framework\Assert;

/**
 * Base test case Fixcity — DatabaseTransactions, no RefreshDatabase (dati sacri).
 *
 * @property \Modules\User\Models\User|null $user
 * @property \Modules\User\Models\User|null $admin
 * @property \Modules\Fixcity\Models\Ticket|null $ticket
 * @property \Closure|null $callStatic
 * @property WorkflowService $workflowService
 * @property TicketService|null $ticketService
 * @property NotificationService|null $notificationService
 */
abstract class TestCase extends XotBaseTestCase
{
    use DatabaseTransactions;

    public ?\Modules\User\Models\User $user = null;

    public ?\Modules\User\Models\User $admin = null;

    public ?\Modules\Fixcity\Models\Ticket $ticket = null;

    /** @var \Closure|null */
    public ?\Closure $callStatic = null;

    public WorkflowService $workflowService;

    public ?TicketService $ticketService = null;

    public ?NotificationService $notificationService = null;

    /** @var list<string> */
    protected $connectionsToTransact = ['fixcity', 'user', 'comment', 'media'];

    public function authUser(): AuthUser
    {
        Assert::assertNotNull($this->user);

        return $this->user;
    }

    public function authAdmin(): AuthUser
    {
        Assert::assertNotNull($this->admin);

        return $this->admin;
    }

    public function ticket(): Ticket
    {
        Assert::assertNotNull($this->ticket);

        return $this->ticket;
    }

    public function workflow(): WorkflowService
    {
        Assert::assertNotNull($this->workflowService);

        return $this->workflowService;
    }

    public function ticketService(): TicketService
    {
        Assert::assertNotNull($this->ticketService);

        return $this->ticketService;
    }

    public function notification(): NotificationService
    {
        Assert::assertNotNull($this->notificationService);

        return $this->notificationService;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $database = database_path('fixcity_data.sqlite');

        /** @var array<string, array<string, mixed>> $connections */
        $connections = config('database.connections', []);

        foreach (array_keys($connections) as $connection) {
            if (config("database.connections.{$connection}.driver") !== 'sqlite') {
                continue;
            }

            $this->app['config']->set("database.connections.{$connection}.database", $database);
            DB::purge($connection);
        }

        config(['auth.providers.users.model' => User::class]);
    }
}
