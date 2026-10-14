<?php

declare(strict_types=1);

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Component;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

uses(TestCase::class);

/*
 * Audit Blueprint F-03 (DCP-180, check B2): il campo `images` scrive su un disco servito: senza `acceptedFileTypes()` il solo argine era la
 * collezione del modello, che scatta DOPO Ticket::create (ticket orfano, SEC-04 DCP-49). Dopo DCP-184 (`getFormSchemaOld()` dei Resource tolto)
 * c'e' un solo schema che scrive foto: `TicketForm`, usato dal back office, dal wizard e dal `CreateTicketWidget`. Il campo deve ammettere
 * gli stessi mime della collezione a cui scrive.
 */
/**
 * @param  array<int|string, mixed>  $components
 */
function findImagesUpload(array $components): ?SpatieMediaLibraryFileUpload
{
    foreach ($components as $component) {
        if ($component instanceof SpatieMediaLibraryFileUpload) {
            return $component;
        }
        $children = $component instanceof Component ? $component->getDefaultChildComponents() : null;
        if (is_array($children)) {
            $found = findImagesUpload($children);
            if ($found !== null) {
                return $found;
            }
        }
    }

    return null;
}

it('restricts the images upload to the mime types its media collection accepts', function (): void {
    $field = findImagesUpload(app(TicketForm::class)->getFormSchema());

    Assert::assertNotNull($field, 'Campo images non trovato: la guardia passerebbe a vuoto.');
    Assert::assertSame('images', $field->getName());

    $collection = null;
    foreach ((new Ticket)->getRegisteredMediaCollections() as $registered) {
        if ($registered instanceof MediaCollection && $registered->name === $field->getCollection()) {
            $collection = $registered;
        }
    }
    Assert::assertInstanceOf(MediaCollection::class, $collection, 'Collezione del campo non registrata sul modello.');
    Assert::assertEqualsCanonicalizing($collection->acceptsMimeTypes, $field->getAcceptedFileTypes() ?? []);
});
