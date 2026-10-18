<?php

declare(strict_types=1);

use Modules\Fixcity\Models\User;

test('ticket subscribers has one active migration owner', function (): void {
    $migrationPath = dirname(__DIR__, 2).'/database/migrations';
    $owner = file_get_contents($migrationPath.'/2023_01_01_000000_create_ticket_subscribers_table.php');

    expect($owner)
        ->toContain('protected ?string $model_class = TicketSubscriber::class;')
        ->toContain("\$table->string('user_id', 36)->index();")
        ->toContain('$this->updateTimestamps(table: $table, hasSoftDeletes: true);')
        ->not->toContain('$table->timestamps();')
        ->not->toContain('$table->softDeletes();');

    expect(glob($migrationPath.'/*ticket_subscribers*.php') ?: [])
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
