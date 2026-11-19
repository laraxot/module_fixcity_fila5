<?php

declare(strict_types=1);

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Permission;

/*
 * DCP-281 (A-23): il report dice che `hasPermissionToOrCreate` fa `Permission::findOrCreate` «a ogni controllo». Misurato: Spatie
 * cerca prima nella cache del registrar e inserisce solo se il permesso manca, quindi si scrive UNA volta per nome di permesso,
 * mai a ogni controllo. Questo test fissa il comportamento reale (e che l'esito dell'autorizzazione non cambia).
 * Togliere del tutto l'auto-registrazione e' una decisione di architettura (DA DECIDERE, vedi la story), non un fix locale.
 */

uses(TestCase::class);

/**
 * Esegue $check e restituisce le istruzioni SQL di scrittura emesse, su qualunque connessione.
 *
 * @return list<string>
 */
function dcp281WritesDuring(Closure $check): array
{
    $writes = [];
    DB::listen(static function (QueryExecuted $query) use (&$writes): void {
        if (Str::startsWith(strtolower(ltrim($query->sql)), ['insert', 'update', 'delete'])) {
            $writes[] = $query->sql;
        }
    });

    $check();

    return $writes;
}

it('inserts a missing permission once and never writes again on later checks', function (): void {
    $user = UserFactory::new()->createOne();
    $name = 'dcp281.lookup.probe';

    $first = dcp281WritesDuring(static fn (): bool => $user->hasPermissionToOrCreate($name));
    $second = dcp281WritesDuring(static fn (): bool => $user->hasPermissionToOrCreate($name));
    $third = dcp281WritesDuring(static fn (): bool => $user->hasPermissionToOrCreate($name));

    expect($first)->toHaveCount(1)
        ->and(strtolower($first[0]))->toStartWith('insert into')->toContain('permissions')
        ->and($second)->toBe([])
        ->and($third)->toBe([])
        ->and(Permission::query()->where('name', $name)->count())->toBe(1);
});

it('keeps the authorization outcome: false without the permission, true once granted', function (): void {
    $user = UserFactory::new()->createOne();
    $name = 'dcp281.lookup.outcome';

    expect($user->hasPermissionToOrCreate($name))->toBeFalse();

    $user->givePermissionTo($name);

    expect($user->hasPermissionToOrCreate($name))->toBeTrue();
});
