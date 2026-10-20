<?php

declare(strict_types=1);

use Modules\Fixcity\Models\User;
use Modules\Fixcity\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\glob;

uses(TestCase::class);

test('ticket subscribers has one active migration owner', function (): void {
    $migrationPath = dirname(__DIR__, 2).'/database/migrations';
    $owner = file_get_contents($migrationPath.'/2023_01_01_000000_create_ticket_subscribers_table.php');

    expect($owner)
        ->toContain('protected ?string $model_class = TicketSubscriber::class;')
        ->toContain("\$table->string('user_id', 36)->index();")
        ->toContain('$this->updateTimestamps(table: $table, hasSoftDeletes: true);')
        ->not->toContain('$table->timestamps();')
        ->not->toContain('$table->softDeletes();');

    $ticketSubscriberOwners = array_filter(
        glob($migrationPath.'/*ticket_subscribers*.php') ?: [],
        static fn (mixed $path): bool => is_string($path) && ! str_contains(basename($path), '_repair_'),
    );

    expect($ticketSubscriberOwners)
        ->toHaveCount(1);

    expect(glob($migrationPath.'/*create_users_table.php') ?: [])
        ->toBeEmpty();

    // Ownership: users vive su Modules/User + connessione `user`.
    // Non alterare fixcity.users legacy (anti-pattern D36 errato).
    expect(glob($migrationPath.'/*update_users_id*.php') ?: [])
        ->toBeEmpty();
});

test('fixcity user keeps the user module UUID connection', function (): void {
    $user = new User;

    expect($user->getConnectionName())
        ->toBe('user');
    expect($user->getKeyType())
        ->toBe('string');
    expect($user->getIncrementing())
        ->toBeFalse();
});

test('fixcity configuration keeps user-owned tables outside the data database', function (): void {
    $tenantDatabase = file_get_contents(dirname(__DIR__, 4).'/config/local/fixcity/database.php');
    $moduleDatabase = file_get_contents(dirname(__DIR__, 2).'/config/database.php');

    expect($tenantDatabase)
        ->toContain("\$userDatabase = env('DB_DATABASE_USER', 'fixcity_user');")
        ->toContain("\$userDatabase = 'fixcity_user';")
        ->toContain("'database' => \$userDatabase,")
        ->toContain('if ($userDatabase === $applicationDatabase)')
        ->toContain("\$userDatabase = 'fixcity_user';");

    expect($moduleDatabase)
        ->toContain('if ($user_database_name === $database_name)')
        ->toContain("\$user_database_name = 'fixcity_user';")
        ->toContain("'database' => \$user_database_name,");
});
