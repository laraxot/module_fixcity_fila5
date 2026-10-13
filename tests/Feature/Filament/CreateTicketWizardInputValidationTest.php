<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * SEC-04/SEC-06 (DCP-49): il form accettava `image/*` mentre la collezione `attachments` salva solo jpeg, png e pdf (SVG, GIF,
 * WebP passavano la validazione e poi la creazione dei media falliva dopo `Ticket::create`: ticket orfano e 500); nessun limite
 * di dimensione; `location` era solo `required()` (un array con latitudine e longitudine nulle passava, e oltre 99 gradi la colonna
 * `decimal(20,18)` dava QueryException). Ogni input fuori contratto deve fermarsi in validazione, prima di scrivere il ticket.
 *
 * @param  array<string, mixed>  $override
 */
function ticketWizardSubmitWith(array $override): Testable
{
    Storage::fake('public');
    app()->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });
    Auth::login(UserFactory::new()->createOne());

    $state = array_merge([
        'privacyAccepted' => true,
        'name' => 'Lampione spento in via Roma',
        'type' => TicketTypeEnum::PUBLIC_LIGHTING->value,
        'content' => 'Il lampione non funziona da tre giorni.',
        'location' => ['latitude' => '45.4642', 'longitude' => '9.1900', 'address' => 'Via Roma, Milano'],
        'images' => [UploadedFile::fake()->image('ok.jpg')],
    ], $override);

    $component = Livewire::test(CreateTicketWizardWidget::class);
    foreach ($state as $key => $value) {
        $component->set('data.'.$key, $value);
    }

    return $component->call('submit');
}

it('rejects an attachment type the collection cannot store and creates no ticket', function (string $name, string $mime): void {
    $before = Ticket::query()->count();

    $component = ticketWizardSubmitWith(['images' => [UploadedFile::fake()->create($name, 10, $mime)]]);

    $component->assertHasErrors();
    Assert::assertSame($before, Ticket::query()->count(), 'Un allegato non ammesso ha lasciato un ticket orfano.');
})->with([
    'svg' => ['logo.svg', 'image/svg+xml'],
    'gif' => ['anim.gif', 'image/gif'],
    'webp' => ['foto.webp', 'image/webp'],
]);

it('rejects an attachment larger than the limit', function (): void {
    $before = Ticket::query()->count();

    $component = ticketWizardSubmitWith(['images' => [UploadedFile::fake()->create('enorme.jpg', 11 * 1024, 'image/jpeg')]]);

    $component->assertHasErrors();
    Assert::assertSame($before, Ticket::query()->count());
});

it('rejects coordinates that are missing, not numeric or out of range', function (mixed $location): void {
    $before = Ticket::query()->count();

    $component = ticketWizardSubmitWith(['location' => $location]);

    $component->assertHasErrors();
    Assert::assertSame($before, Ticket::query()->count(), 'Coordinate non valide hanno creato un ticket.');
})->with([
    'senza punto' => [['latitude' => null, 'longitude' => null, 'address' => null]],
    'latitudine oltre 90' => [['latitude' => '123.4', 'longitude' => '9.19', 'address' => 'x']],
    'longitudine oltre 180' => [['latitude' => '45.46', 'longitude' => '200', 'address' => 'x']],
    'testo' => [['latitude' => 'abc', 'longitude' => '9.19', 'address' => 'x']],
]);

it('still accepts a valid photo and valid coordinates', function (): void {
    $component = ticketWizardSubmitWith([]);

    $component->assertHasNoErrors();
    Assert::assertNotNull(Ticket::query()->latest('id')->first());
});
