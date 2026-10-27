<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Modules\Fixcity\Tests\TestCase;
use Modules\Gdpr\Actions\Cookie\GetCookieInventoryAction;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Cookie policy: il cookie «Ricordami» del login (casella nel form di accesso, `Auth::attempt($credentials, $remember)`) e' elencato con il nome
 * e la durata che Laravel imposta davvero. Si prova sul cookie accodato da un login con remember, non sul nome ricavato dalla stessa guardia.
 * Serve il DB di prova (pest-scratch-db.sh).
 */
it('declares the remember-me cookie that a login with the box ticked really queues, with the real lifetime', function (): void {
    app()->setLocale('it');
    $user = UserFactory::new()->createOne();

    Auth::guard('web')->login($user, true);

    $queued = array_values(array_filter(
        Cookie::getQueuedCookies(),
        static fn ($cookie): bool => str_starts_with($cookie->getName(), 'remember_'),
    ));
    Assert::assertCount(1, $queued, 'un login con «Ricordami» imposta esattamente un cookie remember_*');

    $declared = collect(app(GetCookieInventoryAction::class)->execute()->cookies)->first(static fn ($cookie): bool => $cookie->name === $queued[0]->getName());
    Assert::assertNotNull($declared, "{$queued[0]->getName()} e' impostato dal login ma non e' nell'inventario");

    $days = (int) round((Carbon::createFromTimestamp($queued[0]->getExpiresTime())->diffInSeconds(now(), true)) / 86400);
    Assert::assertSame(400, $days, 'durata reale del cookie in giorni');
    Assert::assertStringContainsString((string) $days, $declared->duration);
});
