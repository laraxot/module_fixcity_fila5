<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Session;
use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\ResolveTicketConfirmationDataAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * DCP-34: dati della pagina di conferma. Il flash di CreateTicketAction vince e si consuma una volta sola;
 * senza flash vale il codice di tracciamento (capability token); l'email del proprietario la vede solo il proprietario autenticato.
 */
describe('ResolveTicketConfirmationDataAction', function (): void {
    test('returns the flashed bag and consumes it', function (): void {
        Session::flash(BuildTicketConfirmationDataAction::SESSION_KEY, [
            'code' => 'TCK-FLASH001',
            'email' => 'proprietario.flash@example.test',
        ]);

        $resolve = app(ResolveTicketConfirmationDataAction::class);
        $data = $resolve->execute();

        Assert::assertSame('TCK-FLASH001', $data['code'] ?? null);
        Assert::assertSame('proprietario.flash@example.test', $data['email'] ?? null);
        Assert::assertFalse(Session::has(BuildTicketConfirmationDataAction::SESSION_KEY));
        Assert::assertSame([], $resolve->execute());
    });

    test('prefers the flashed bag over the code query', function (): void {
        TicketFactory::new()->createOne(['code' => 'TCK-OTHER001']);
        Session::flash(BuildTicketConfirmationDataAction::SESSION_KEY, ['code' => 'TCK-FLASH002', 'email' => 'a@example.test']);

        $data = app(ResolveTicketConfirmationDataAction::class)->execute('TCK-OTHER001');

        Assert::assertSame('TCK-FLASH002', $data['code'] ?? null);
    });

    test('falls back to the ticket of the code without exposing the owner email', function (): void {
        $owner = UserFactory::new()->createOne(['email' => 'proprietario.codice@example.test']);
        TicketFactory::new()->createOne(['code' => 'TCK-CODE0001', 'owner_id' => $owner->id]);

        $data = app(ResolveTicketConfirmationDataAction::class)->execute('TCK-CODE0001');

        Assert::assertSame('TCK-CODE0001', $data['code'] ?? null);
        Assert::assertSame('', $data['email'] ?? null);
        Assert::assertIsArray($data['receipt'] ?? null);
        Assert::assertStringNotContainsString('proprietario.codice@example.test', json_encode($data, JSON_THROW_ON_ERROR));
    });

    test('shows the owner email to the authenticated owner and to nobody else', function (): void {
        $owner = UserFactory::new()->createOne(['email' => 'proprietario.codice@example.test']);
        $other = UserFactory::new()->createOne();
        TicketFactory::new()->createOne(['code' => 'TCK-OWNER001', 'owner_id' => $owner->id]);
        $action = app(ResolveTicketConfirmationDataAction::class);

        $this->actingAs($other);
        $asOther = $action->execute('TCK-OWNER001');
        Assert::assertSame('', $asOther['email'] ?? null);

        $this->actingAs($owner);
        $asOwner = $action->execute('TCK-OWNER001');
        Assert::assertSame('proprietario.codice@example.test', $asOwner['email'] ?? null);
    });

    test('returns an empty array without flash and without a usable code', function (?string $code): void {
        Assert::assertSame([], app(ResolveTicketConfirmationDataAction::class)->execute($code));
    })->with([
        'no code' => [null],
        'blank code' => ['   '],
        'unknown code' => ['TCK-NOPE0000'],
    ]);
});
