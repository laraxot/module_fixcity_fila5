<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-186 (scheda del front office, gruppo C): il recupero password dava HTTP 500 all'invio del modulo. Le configurazioni `auth.php` del
 * tenant Fixcity avevano ereditato da un altro progetto `'connection' => 'liveuser_general'`, una connessione che qui non esiste. Gli utenti
 * stanno sulla connessione `user`, dove c'e' anche la tabella `password_resets`.
 */
it('stores password reset tokens on a connection that exists and holds the table', function (): void {
    $connection = config('auth.passwords.users.connection');
    $table = config()->string('auth.passwords.users.table');

    Assert::assertIsString($connection);
    Assert::assertArrayHasKey($connection, config()->array('database.connections'), "La connessione «{$connection}» dei token di reset non e' configurata.");
    Assert::assertTrue(Schema::connection($connection)->hasTable($table), "La tabella «{$table}» non esiste sulla connessione «{$connection}».");
});

it('sends a reset link to an existing user without failing', function (): void {
    Notification::fake();
    $user = UserFactory::new()->createOne(['email' => 'recupero.password@example.test']);

    $status = Password::sendResetLink(['email' => $user->email]);

    Assert::assertSame(Password::RESET_LINK_SENT, $status);
});

it('answers an unknown email without failing', function (): void {
    Notification::fake();

    Assert::assertSame(Password::INVALID_USER, Password::sendResetLink(['email' => 'non.esiste@example.test']));
});
