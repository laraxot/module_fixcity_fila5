<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Fixcity\Actions\ResolveTicketMediaDiskAction;
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
    use DatabaseTransactions {
        beginDatabaseTransaction as openDatabaseTransactions;
    }

    /**
     * Le migration girano una volta per processo, come in `Tests\TestCase` e in `Modules\User\Tests\TestCase`.
     */
    public static bool $fixcitySchemaMigrated = false;

    public ?User $user = null;

    public ?User $admin = null;

    public ?Ticket $ticket = null;

    public ?\Closure $callStatic = null;

    /** @var list<string> */
    protected $connectionsToTransact = ['fixcity', 'user', 'comment', 'media', 'blog'];

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

    /**
     * Una transazione di test per ogni ISTANZA di connessione, non per ogni nome.
     *
     * `TenantServiceProvider::aliasModuleConnectionsToDefault()` risolve le connessioni di modulo senza database proprio (`fixcity`,
     * `comment`, `media`, `blog`) sulla stessa istanza `Connection` della default. Con i cinque nomi il trait apriva quattro livelli
     * sulla stessa connessione: un'Action con `transaction()` committava al livello 4, mai a 1, e il DatabaseTransactionsManager di test
     * non eseguiva piu' i callback "dopo il commit" (observer `ShouldHandleEventsAfterCommit`, eventi `ShouldDispatchAfterCommit`).
     * Effetto in CI su MariaDB: nessuna notifica di approvazione, scarto o chiusura (TicketLifecycleNotificationTest, area personale).
     * Lo skip del manager conta i nomi restituiti qui, quindi ogni nome deve corrispondere a una sola transazione aperta.
     *
     * @return list<string>
     */
    protected function connectionsToTransact(): array
    {
        $database = $this->app?->make('db');
        if (! $database instanceof DatabaseManager) {
            return $this->connectionsToTransact;
        }

        $names = [];
        $seen = [];
        foreach ($this->connectionsToTransact as $name) {
            $id = spl_object_id($database->connection($name));
            if (isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $names[] = $name;
        }

        return $names;
    }

    /**
     * Su MariaDB (CI: DB_CONNECTION=mariadb, DB_DATABASE_USER impostato) prepareSharedSqliteForTesting() non tocca le connessioni,
     * perche' converte solo quelle con driver sqlite: i test girano sui database `*_test` vuoti, che `xot:build-test-sqlite` non
     * costruisce (popola solo lo SQLite condiviso). Senza questo passo la suite Fixcity moriva con 409 "Table
     * 'fixcity_user_test.users' doesn't exist" e decine di tickets/articles/profiles/categories/job_batches mancanti.
     * `migrate` e' additivo (mai `fresh`, dati sacri) e la TestDatabaseGuard garantisce che ogni connessione punti a un database `_test`.
     * Va fatto prima di aprire le transazioni: in MySQL ogni DDL fa commit implicito.
     *
     * Nessun tipo di ritorno nativo: il metodo del trait non lo dichiara.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        if (! static::$fixcitySchemaMigrated) {
            static::$fixcitySchemaMigrated = true;

            if (config('database.default') !== 'sqlite') {
                $this->app?->make(Kernel::class)->call('migrate', ['--force' => true]);
            }
        }

        $this->openDatabaseTransactions();
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

        // nessuna email reale dai test: creare una segnalazione invia la ricevuta al proprietario
        Mail::fake();
        // gli allegati dei ticket vanno sul disco privato (SEC-04): mai file reali scritti dai test
        Storage::fake(ResolveTicketMediaDiskAction::DEFAULT_DISK);
    }
}
