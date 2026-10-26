<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\CanUserReceiveTicketMailAction;
use Modules\Fixcity\Actions\ResolveTicketConfirmationDataAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Notifications\TicketLifecycleNotification;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Contracts\UserContract;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-286 (FO-2-25, FO-3-03): la conferma diceva "Abbiamo inviato il riepilogo all'email :email" a tutti. La mail di ricezione
 * (TicketLifecycleNotification, DCP-284) parte solo con email verificata e avvisi via email non disattivati; in ogni caso c'e' la
 * notifica nell'area personale. La conferma ora dice quello che succede davvero, con la stessa regola dei canali.
 */

/** Disattiva gli avvisi via email del cittadino, come fa la pagina delle preferenze. */
function turnOffTicketEmailUpdates(UserContract $user): void
{
    $profile = $user->profile()->firstOrCreate([]);
    $profile->setAttribute('preferences', ['fixcity' => ['email_ticket_updates' => false]]);
    $profile->saveOrFail();
}

describe('CanUserReceiveTicketMailAction', function (): void {
    test('is true only for a verified, non-empty address with email alerts not turned off', function (): void {
        $action = app(CanUserReceiveTicketMailAction::class);

        $verified = UserFactory::new()->createOne(['email' => 'verificata@example.test']);
        $unverified = UserFactory::new()->unverified()->createOne(['email' => 'non.verificata@example.test']);
        $optedOut = UserFactory::new()->createOne(['email' => 'disattivata@example.test']);
        turnOffTicketEmailUpdates($optedOut);

        Assert::assertTrue($action->execute($verified));
        Assert::assertFalse($action->execute($unverified));
        Assert::assertFalse($action->execute($optedOut));
        Assert::assertFalse($action->execute(new stdClass));
    });

    test('says what TicketLifecycleNotification::via really does, for every kind of owner', function (): void {
        $action = app(CanUserReceiveTicketMailAction::class);
        $notification = new TicketLifecycleNotification('TCK-PARITY001', TicketLifecycleEventEnum::Received, TicketStatusEnum::PENDING);

        $optedOut = UserFactory::new()->createOne();
        turnOffTicketEmailUpdates($optedOut);
        $owners = [
            'verified' => UserFactory::new()->createOne(),
            'unverified' => UserFactory::new()->unverified()->createOne(),
            'opted out' => $optedOut,
        ];

        foreach ($owners as $label => $owner) {
            Assert::assertSame(
                in_array('mail', $notification->via($owner), true),
                $action->execute($owner),
                "Il testo della conferma e i canali della notifica divergono per l'utente: {$label}.",
            );
        }
    });
});

describe('confirmation data: email_delivery', function (): void {
    test('is "mail" for a verified owner, "in_app" otherwise, empty when the owner cannot be resolved', function (): void {
        $build = app(BuildTicketConfirmationDataAction::class);
        $verified = UserFactory::new()->createOne();
        $unverified = UserFactory::new()->unverified()->createOne();
        $optedOut = UserFactory::new()->createOne();
        turnOffTicketEmailUpdates($optedOut);

        Assert::assertSame('mail', $build->execute(TicketFactory::new()->createOne(['owner_id' => $verified->id]))['email_delivery']);
        Assert::assertSame('in_app', $build->execute(TicketFactory::new()->createOne(['owner_id' => $unverified->id]))['email_delivery']);
        Assert::assertSame('in_app', $build->execute(TicketFactory::new()->createOne(['owner_id' => $optedOut->id]))['email_delivery']);
        // proprietario non risolvibile (utente eliminato): nessuna promessa, nessuna riga
        $orphan = TicketFactory::new()->createOne(['owner_id' => $verified->id])->setRelation('owner', null);
        Assert::assertSame('', $build->execute($orphan)['email_delivery']);
    });

    test('is never given to someone who only holds the tracking code', function (): void {
        $owner = UserFactory::new()->createOne();
        TicketFactory::new()->createOne(['code' => 'TCK-DELIVERY01', 'owner_id' => $owner->id]);
        $resolve = app(ResolveTicketConfirmationDataAction::class);

        $this->actingAs(UserFactory::new()->createOne());
        Assert::assertSame('', $resolve->execute('TCK-DELIVERY01')['email_delivery'] ?? null);

        $this->actingAs($owner);
        Assert::assertSame('mail', $resolve->execute('TCK-DELIVERY01')['email_delivery']);
    });
});
