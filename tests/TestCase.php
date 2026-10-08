<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Modules\Xot\Tests\XotBaseTestCase;
use PHPUnit\Framework\Assert;

/**
 * Base test case Fixcity — DatabaseTransactions, no RefreshDatabase (dati sacri).
 *
 * @property User|null $user
 * @property User|null $admin
 * @property Ticket|null $ticket
 * @property \Closure|null $callStatic
 */
abstract class TestCase extends XotBaseTestCase
{
    use DatabaseTransactions;

    public ?User $user = null;

    public ?User $admin = null;

    public ?Ticket $ticket = null;

    public ?\Closure $callStatic = null;

    /** @var list<string> */
    protected $connectionsToTransact = ['fixcity', 'user', 'comment', 'media'];

    /**
     * Fixcity models run on the dedicated connection; keep assertions inside
     * the same transaction/PDO instead of the application's generic default.
     *
     * @param  array<string, mixed>  $data
     */
    public function assertDatabaseHasRow(string $table, array $data, ?string $connection = 'fixcity'): void
    {
        parent::assertDatabaseHasRow($table, $data, $connection);
    }

    public function authUser(): User
    {
        Assert::assertNotNull($this->user);

        return $this->user;
    }

    public function authAdmin(): User
    {
        Assert::assertNotNull($this->admin);

        return $this->admin;
    }

    public function ticket(): Ticket
    {
        Assert::assertNotNull($this->ticket);

        return $this->ticket;
    }

    protected function setUp(): void
    {
        // Prepare the shared SQLite PDO before Laravel's DatabaseTransactions trait
        // opens its transactions; purging named connections afterwards would detach
        // them from the transaction manager and cause leaks or SQLite locks.
        $this->refreshApplication();
        $this->prepareSharedSqliteForTesting();

        parent::setUp();

        config(['auth.providers.users.model' => User::class]);
    }
}
