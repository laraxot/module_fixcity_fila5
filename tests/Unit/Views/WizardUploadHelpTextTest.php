<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\Media\Actions\Upload\GetEffectiveUploadLimitAction;
use Modules\Media\Filament\Forms\Components\CameraPhotoUpload;

uses(TestCase::class);

/*
 * DCP-286 (FO-3-19): l'aiuto sotto "Carica file" diceva solo "Seleziona una o piu' immagini": non diceva quali formati il server accetta (JPG, PNG, PDF),
 * ne' la dimensione massima (10 MB) ne' il numero massimo (5). Ora il testo lo compone la vista dalle regole vere del campo. Nessuna promessa su HEIC/HEIF
 * ne' sulla rimozione dei dati EXIF: il client converte HEIC solo dove il browser sa leggerlo e l'EXIF non viene tolto (E-03, decisione del titolare).
 */
it('tells the citizen which files the attachment field really accepts', function (string $locale, string $expected): void {
    app()->setLocale($locale);
    $html = html_entity_decode(Livewire::test(CreateTicketWizardWidget::class)->html(), ENT_QUOTES);

    expect($html)
        ->toContain($expected)
        ->not->toContain('HEIC files are accepted')
        ->not->toContain('EXIF');
})->with([
    'italiano' => ['it', 'Formati ammessi: JPG, PNG, PDF. Fino a 5 file, massimo 10 MB ciascuno.'],
    'inglese' => ['en', 'Accepted formats: JPG, PNG, PDF. Up to 5 files, 10 MB each.'],
    'tedesco' => ['de', 'Zulässige Formate: JPG, PNG, PDF. Bis zu 5 Dateien, jeweils höchstens 10 MB.'],
    'spagnolo' => ['es', 'Formatos admitidos: JPG, PNG, PDF. Hasta 5 archivos, de máximo 10 MB cada uno.'],
]);

/*
 * DCP-378: le foto da fotocamera (3-8 MB) morivano sul limite di upload del server (2 MB su nginx/PHP) dopo aver superato il controllo
 * del browser (10 MB). Il client riduce le immagini grandi prima dell'upload (modulo Media, `MediaCameraImage`) e controlla il limite
 * che il server applica davvero.
 */
it('shrinks camera photos and checks the size limit the server really applies', function (): void {
    $html = html_entity_decode(Livewire::test(CreateTicketWizardWidget::class)->html(), ENT_QUOTES);
    // il tetto in byte e' il contratto Media (campo da 10 MB, regola Livewire, 90% del limite reale di PHP), non una formula copiata qui
    $maxBytes = app(GetEffectiveUploadLimitAction::class)->execute(10 * 1024);

    expect($html)
        ->toContain('window.MediaCameraImage.normalize(file, { maxSide: this.maxSide })')
        ->toContain('maxSide: '.CameraPhotoUpload::MAX_SIDE.',')
        ->toContain('maxBytes: '.$maxBytes.',');
});
