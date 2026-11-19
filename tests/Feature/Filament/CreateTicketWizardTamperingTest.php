<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * SEC-01 (DCP-49): `status` e `priority` sono campi nascosti del form, quindi il client puo' riscriverli
 * (`$wire.set('data.status', 'closed')`). Un cittadino non deve poter creare un ticket gia' aperto o chiuso:
 * stato e priorita' iniziali li decide il server.
 */
it('ignores a status and priority tampered by the client when a citizen creates a ticket', function (string $status, string $priority): void {
    Storage::fake('public');
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });

    $user = UserFactory::new()->createOne();
    $this->actingAs($user);

    $component = Livewire::test(CreateTicketWizardWidget::class);
    $component->set('data.privacyAccepted', true);
    $component->set('data.name', 'Buca pericolosa in via Verdi');
    $component->set('data.type', TicketTypeEnum::PUBLIC_LIGHTING->value);
    $component->set('data.content', 'Buca profonda nel mezzo della carreggiata.');
    $component->set('data.location', [
        'latitude' => '45.4642',
        'longitude' => '9.1900',
        'address' => 'Via Verdi, Milano',
    ]);
    $component->set('data.images', [UploadedFile::fake()->image('buca.jpg')]);
    $component->set('data.status', $status);
    $component->set('data.priority', $priority);
    $component->call('submit');
    $component->assertHasNoErrors();

    $ticket = Ticket::query()->where('owner_id', $user->getAuthIdentifier())->latest('id')->first();
    Assert::assertInstanceOf(Ticket::class, $ticket);
    Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
    // `priority` non ha cast enum: si controlla il valore grezzo salvato.
    Assert::assertSame(TicketPriorityEnum::LOW->value, $ticket->getRawOriginal('priority'));
})->with([
    'closed e urgent' => ['closed', 'urgent'],
    'resolved e critical' => ['resolved', 'critical'],
    'open e high' => ['open', 'high'],
]);

/**
 * SEC-10 (DCP-49): `blockData` (da cui esce `confirmation_path`) e `confirmationLocale` decidono il redirect dopo il salvataggio.
 * Sono impostati dal server in `mount()`: il client non deve poterli riscrivere.
 */
it('cannot update locked properties that decide the post-save redirect', function (string $property, mixed $value): void {
    Livewire::test(CreateTicketWizardWidget::class)->set($property, $value);
})->throws(CannotUpdateLockedPropertyException::class)->with([
    'blockData' => ['blockData', ['confirmation_path' => 'https://evil.example/phish']],
    'confirmationLocale' => ['confirmationLocale', 'xx'],
]);
