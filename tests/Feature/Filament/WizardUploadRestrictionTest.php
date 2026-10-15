<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

/*
 * Audit Blueprint F-02 (DCP-180, check A5): `InteractsWithSchemas` porta con se' `WithFileUploads`, quindi ogni widget espone
 * `_startUpload` su QUALUNQUE proprieta'. `RestrictsFileUploadsToSchemaComponents` (messa su XotBaseWidget, ereditata da XotBaseSchemaWidget,
 * XotBaseInfolistWidget e XotBaseWizardWidget) fa rispondere 403 a chi carica verso un bersaglio che non e' un campo di upload dello schema.
 * Qui si prova sul widget reale del wizard: il campo `images` deve restare utilizzabile, ogni altro bersaglio no.
 */
$fileInfo = [['name' => 'a.png', 'size' => 1024, 'type' => 'image/png']];

/** Controllo di mutazione: lo stesso widget con la restrizione spenta, per provare che il 403 viene da quel flag e non da altro. */
final class WizardWithoutUploadRestriction extends CreateTicketWizardWidget
{
    #[Override]
    public function shouldRestrictFileUploadsToSchemaComponents(): bool
    {
        return false;
    }
}

it('refuses uploads aimed at a property that is not an upload field', function (string $target) use ($fileInfo): void {
    $this->actingAs(UserFactory::new()->createOne());

    Livewire::test(CreateTicketWizardWidget::class)
        ->call('_startUpload', $target, $fileInfo, false)
        ->assertForbidden();
})->with([
    'text field' => ['data.name'],
    'whole state' => ['data'],
    'component property' => ['blockData'],
    'unknown property' => ['nonExistent'],
]);

it('still accepts an upload aimed at the images field', function () use ($fileInfo): void {
    $this->actingAs(UserFactory::new()->createOne());

    Livewire::test(CreateTicketWizardWidget::class)
        ->call('_startUpload', 'data.images.k1', $fileInfo, false)
        ->assertSuccessful();
});

it('would let the same upload through if the restriction were off (mutation control)', function () use ($fileInfo): void {
    $this->actingAs(UserFactory::new()->createOne());

    Livewire::test(WizardWithoutUploadRestriction::class)
        ->call('_startUpload', 'data.name', $fileInfo, false)
        ->assertSuccessful();
});
