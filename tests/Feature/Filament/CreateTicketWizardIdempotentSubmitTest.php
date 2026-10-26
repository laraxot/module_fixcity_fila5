<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Livewire\Features\SupportTesting\Testable;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Actions\SubmitTicketOnceAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-286 (FO-3-02, FO-3-06): un doppio clic su "Invia" creava due segnalazioni (misurato su :8091: ticket 19 e 20, stesso titolo, due secondi
 * di distanza), e con la sessione scaduta "Invia" non diceva nulla. L'invio e' idempotente per token generato al mount; il token e' #[Locked].
 */

/** Il consenso richiede una informativa pubblicata: nei test se ne simula una. */
function publishPrivacyForWizardTest(): void
{
    app()->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });
}

/** Una segnalazione minima del proprietario, senza passare dal wizard. */
function ticketOfOwnerForIdempotencyTest(string $ownerId): Ticket
{
    $suffix = Str::lower(Str::random(8));

    return Ticket::query()->create([
        'name' => 'Segnalazione di prova '.$suffix,
        'slug' => 'segnalazione-di-prova-'.$suffix,
        'content' => 'Contenuto di prova della segnalazione.',
        'code' => 'TCK-'.Str::upper($suffix),
        'owner_id' => $ownerId,
        'status' => TicketStatusEnum::PENDING->value,
        'latitude' => '45.4642',
        'longitude' => '9.1900',
    ]);
}

/** L'identificativo di un utente come stringa, la forma che usano Action e cache. */
function userIdForIdempotencyTest(Authenticatable $user): string
{
    return SafeStringCastAction::cast($user->getAuthIdentifier());
}

/**
 * Riempie il form come il cittadino: passi 1 e 2 validi, pronto per "Invia".
 *
 * @param  Testable<CreateTicketWizardWidget>  $component
 * @return Testable<CreateTicketWizardWidget>
 */
function fillWizardForIdempotencyTest(Testable $component, string $title = 'Lampione spento in via Roma'): Testable
{
    return $component
        ->set('data.privacyAccepted', true)
        ->set('data.name', $title)
        ->set('data.type', TicketTypeEnum::PUBLIC_LIGHTING->value)
        ->set('data.content', 'Il lampione non funziona da tre giorni.')
        ->set('data.location', ['latitude' => '45.4642', 'longitude' => '9.1900', 'address' => 'Via Roma, Milano'])
        ->set('data.images', [UploadedFile::fake()->image('lampione.jpg')]);
}

it('creates one ticket when the same page submits twice', function (): void {
    /** @var TestCase $this */
    Storage::fake('public');
    publishPrivacyForWizardTest();
    $user = UserFactory::new()->createOne();
    $this->actingAs($user);

    $component = fillWizardForIdempotencyTest(Livewire::test(CreateTicketWizardWidget::class));
    $component->call('submit')->assertHasNoErrors();
    $component->call('submit')->assertHasNoErrors();

    $tickets = Ticket::query()->where('owner_id', $user->getAuthIdentifier())->get();
    Assert::assertCount(1, $tickets, 'Due invii dello stesso modulo devono dare una sola segnalazione.');
    $ticket = $tickets->firstOrFail();
    $component->assertRedirectContains('/tickets/confirmation');
    $component->assertRedirectContains(SafeStringCastAction::cast($ticket->code));
    // gli allegati si salvano una volta sola
    Assert::assertCount(1, $ticket->getMedia('attachments'));
});

it('answers a repeated submit with the same confirmation code, without validating the form again', function (): void {
    /** @var TestCase $this */
    Storage::fake('public');
    publishPrivacyForWizardTest();
    $user = UserFactory::new()->createOne();
    $this->actingAs($user);

    $component = fillWizardForIdempotencyTest(Livewire::test(CreateTicketWizardWidget::class));
    $component->call('submit');
    $ticket = Ticket::query()->where('owner_id', $user->getAuthIdentifier())->firstOrFail();

    // un secondo invio con i dati ormai svuotati (allegati gia' spostati dal primo) non deve fallire ne' creare nulla
    $component->set('data.name', '')->call('submit')->assertHasNoErrors();
    $component->assertRedirectContains(SafeStringCastAction::cast($ticket->code));
    Assert::assertSame(1, Ticket::query()->where('owner_id', $user->getAuthIdentifier())->count());
});

it('lets a new page load send a new report', function (): void {
    /** @var TestCase $this */
    Storage::fake('public');
    publishPrivacyForWizardTest();
    $user = UserFactory::new()->createOne();
    $this->actingAs($user);

    fillWizardForIdempotencyTest(Livewire::test(CreateTicketWizardWidget::class), 'Prima segnalazione')->call('submit');
    fillWizardForIdempotencyTest(Livewire::test(CreateTicketWizardWidget::class), 'Seconda segnalazione')->call('submit');

    Assert::assertSame(2, Ticket::query()->where('owner_id', $user->getAuthIdentifier())->count());
});

it('gives each page load its own submission token and does not let the client change it', function (): void {
    $first = Livewire::test(CreateTicketWizardWidget::class);
    $second = Livewire::test(CreateTicketWizardWidget::class);

    $tokenOfFirst = $first->get('submissionToken');
    Assert::assertIsString($tokenOfFirst);
    Assert::assertNotSame('', $tokenOfFirst);
    Assert::assertNotSame($tokenOfFirst, $second->get('submissionToken'));

    $first->set('submissionToken', 'riuso-di-un-token');
})->throws(CannotUpdateLockedPropertyException::class);

it('does not validate an incomplete form as a sent report: the token is spent only by a created ticket', function (): void {
    /** @var TestCase $this */
    Storage::fake('public');
    publishPrivacyForWizardTest();
    $user = UserFactory::new()->createOne();
    $this->actingAs($user);

    $component = fillWizardForIdempotencyTest(Livewire::test(CreateTicketWizardWidget::class))->set('data.name', '');
    $component->call('submit')->assertHasErrors(['data.name']);
    Assert::assertSame(0, Ticket::query()->where('owner_id', $user->getAuthIdentifier())->count());

    $component->set('data.name', 'Titolo corretto')->call('submit')->assertHasNoErrors();
    Assert::assertSame(1, Ticket::query()->where('owner_id', $user->getAuthIdentifier())->count());
});

describe('SubmitTicketOnceAction', function (): void {
    it('runs the creation once per owner and token', function (): void {
        $owner = UserFactory::new()->createOne();
        $other = UserFactory::new()->createOne();
        $ownerId = userIdForIdempotencyTest($owner);
        $otherId = userIdForIdempotencyTest($other);
        $calls = 0;
        $create = function (string $by) use (&$calls): Ticket {
            $calls++;

            return ticketOfOwnerForIdempotencyTest($by);
        };
        $action = app(SubmitTicketOnceAction::class);

        $first = $action->execute($ownerId, 'token-a', fn (): Ticket => $create($ownerId));
        $again = $action->execute($ownerId, 'token-a', fn (): Ticket => $create($ownerId));
        // lo stesso token di un altro utente non restituisce la segnalazione altrui
        $foreign = $action->execute($otherId, 'token-a', fn (): Ticket => $create($otherId));

        Assert::assertSame(2, $calls);
        Assert::assertTrue($first->is($again));
        Assert::assertFalse($first->is($foreign));
        $stored = $action->existing($ownerId, 'token-a');
        Assert::assertInstanceOf(Ticket::class, $stored);
        Assert::assertTrue($stored->is($first));
        Assert::assertNull($action->existing($ownerId, 'token-mai-usato'));
    });

    it('refuses to run the creation while another request holds the same token', function (): void {
        $ownerId = userIdForIdempotencyTest(UserFactory::new()->createOne());
        $action = app(SubmitTicketOnceAction::class);
        $held = Cache::lock(SubmitTicketOnceAction::lockKey($ownerId, 'token-b'), 30);
        Assert::assertTrue($held->get());

        try {
            $action->execute($ownerId, 'token-b', static fn (): Ticket => ticketOfOwnerForIdempotencyTest($ownerId), waitSeconds: 0);
            Assert::fail('Doveva lanciare LockTimeoutException.');
        } catch (LockTimeoutException) {
            Assert::assertSame(0, Ticket::query()->where('owner_id', $ownerId)->count());
        } finally {
            $held->release();
        }
    });

    it('does not remember a token whose creation failed', function (): void {
        $ownerId = userIdForIdempotencyTest(UserFactory::new()->createOne());
        $action = app(SubmitTicketOnceAction::class);
        $failing = static function (): Ticket {
            throw new RuntimeException('database non raggiungibile');
        };

        expect(fn (): Ticket => $action->execute($ownerId, 'token-c', $failing))->toThrow(RuntimeException::class);

        $beforeRetry = $action->existing($ownerId, 'token-c');
        Assert::assertNull($beforeRetry);
        $retry = $action->execute($ownerId, 'token-c', static fn (): Ticket => ticketOfOwnerForIdempotencyTest($ownerId));
        $stored = $action->existing($ownerId, 'token-c');
        Assert::assertInstanceOf(Ticket::class, $stored);
        Assert::assertTrue($stored->is($retry));
    });
});
