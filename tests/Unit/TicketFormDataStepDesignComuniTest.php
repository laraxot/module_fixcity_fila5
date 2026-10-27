<?php

declare(strict_types=1);

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Xot\Tests\XotBaseTestCase;
use PHPUnit\Framework\Assert;

uses(XotBaseTestCase::class);

/*
 * DCP-128: cablaggio del passo "dati" del wizard con le viste Design Comuni (sito/segnalazione-02-dati).
 * Lo STESSO `TicketForm` serve il back office (`getFormSchema()` -> `getDataSchema()` senza argomento) e il wizard front office
 * (`getSteps()['data']` -> `getDataSchema(designComuni: true)`): qui si fissa che il front office e' decorato e il back office NO.
 * Nessun database: si ispeziona lo schema (componenti Filament), non si renderizza.
 */

/** La vista impostata con `->view()` o quella di default; null per i componenti con HTML incorporato (Section, Step, campi semplici). */
function dcView(SchemaComponent $component): ?string
{
    return $component->hasView() ? $component->getView() : null;
}

/**
 * @return array<array-key, mixed> i figli diretti (anche quelli nascosti: si ispeziona lo schema, non si renderizza)
 */
function dcChildren(SchemaComponent $component): array
{
    $children = $component->getDefaultChildComponents();

    return $children instanceof Schema ? $children->getComponents() : $children;
}

/**
 * @param  array<array-key, mixed>  $components
 * @return array<string, Field> i campi per nome (state path relativo), a qualunque profondita'
 */
function dcFieldsByName(array $components): array
{
    $fields = [];
    foreach ($components as $component) {
        if ($component instanceof Section || $component instanceof View) {
            $fields += dcFieldsByName(dcChildren($component));
        } elseif ($component instanceof Field) {
            $fields[$component->getName()] = $component;
        }
    }

    return $fields;
}

describe('back office: lo schema piatto non cambia', function (): void {
    it('keeps the flat schema with no Design Comuni view or constraint', function (): void {
        $schema = TicketForm::getDataSchema();

        Assert::assertSame(['priority', 'status', 'locationSection', 'issueSection', 'authorSection'], array_keys($schema));
        foreach (['locationSection', 'issueSection', 'authorSection'] as $key) {
            Assert::assertInstanceOf(Section::class, $schema[$key]);
            Assert::assertStringNotContainsString('pub_theme::', (string) dcView($schema[$key]), "{$key} non deve avere la vista del tema");
        }

        $fields = dcFieldsByName(array_values($schema));
        $select = $fields['type'] ?? null;
        Assert::assertInstanceOf(Select::class, $select);
        Assert::assertTrue($select->isSearchable(), 'il select del back office resta ricercabile (JS)');
        $content = $fields['content'] ?? null;
        Assert::assertInstanceOf(Textarea::class, $content);
        Assert::assertNull($content->getMaxLength(), 'nessun limite di 200 caratteri nel back office');
        Assert::assertSame(4, $content->getRows());
        foreach (['name', 'content', 'images', 'location.address', 'location', 'type'] as $name) {
            Assert::assertStringNotContainsString('fixcity::filament.forms.components.dc-', (string) dcView($fields[$name]), "{$name} non deve avere una vista dc-*");
        }
        Assert::assertFalse($fields['location.address']->isRequired(), 'nel back office l\'indirizzo resta facoltativo');
        $map = $fields['location'];
        Assert::assertInstanceOf(CoordinatePicker::class, $map);
        Assert::assertTrue($map->getGeolocateWhenEmpty(), 'la mappa del back office continua a partire dalla posizione dell\'utente');
    });

    it('serves the back office with the same keys as before', function (): void {
        $keys = array_keys(TicketForm::getDataSchema());

        Assert::assertSame(['priority', 'status', 'locationSection', 'issueSection', 'authorSection'], $keys);
    });
});

describe('front office: il passo dati ha il markup di segnalazione-02-dati', function (): void {
    it('wraps the three cards in one step view and keeps the hidden defaults outside of it', function (): void {
        $schema = TicketForm::getDataSchema(designComuni: true);

        Assert::assertSame(['priority', 'status', 'dataStep'], array_keys($schema));
        Assert::assertInstanceOf(Hidden::class, $schema['priority']);
        Assert::assertInstanceOf(Hidden::class, $schema['status']);
        $step = $schema['dataStep'];
        Assert::assertInstanceOf(View::class, $step);
        Assert::assertSame('pub_theme::components.wizard.data-step', $step->getView());

        $cards = dcChildren($step);
        Assert::assertCount(3, $cards);
        $ids = [];
        foreach ($cards as $card) {
            Assert::assertInstanceOf(Section::class, $card);
            Assert::assertSame('pub_theme::components.wizard.dc-section-card', $card->getView());
            $ids[] = $card->getId();
        }
        Assert::assertSame(['report-place', 'report-info', 'report-author'], $ids);
    });

    it('turns the place field into the required search box and tags the map for the gates', function (): void {
        $fields = dcFieldsByName([TicketForm::getDataSchema(designComuni: true)['dataStep']]);

        $address = $fields['location.address'];
        Assert::assertSame('fixcity::filament.forms.components.dc-place-autocomplete', $address->getView());
        Assert::assertTrue($address->isRequired());
        Assert::assertTrue($address->isLabelHidden());

        $map = $fields['location'];
        Assert::assertInstanceOf(CoordinatePicker::class, $map);
        Assert::assertTrue($map->isRequired());
        Assert::assertSame('location-map', $map->getExtraFieldWrapperAttributes()['data-element'] ?? null);
        // D19: nessun centro fisso, la mappa parte dalla posizione dell'utente (e dal punto gia' scelto)
        Assert::assertTrue($map->getGeolocateWhenEmpty());
    });

    it('uses a native select, a title, details limited to 200 characters and the Design Comuni upload', function (): void {
        $fields = dcFieldsByName([TicketForm::getDataSchema(designComuni: true)['dataStep']]);

        $type = $fields['type'];
        Assert::assertInstanceOf(Select::class, $type);
        Assert::assertTrue($type->isNative());
        Assert::assertFalse($type->isSearchable());
        Assert::assertTrue($type->isRequired());
        Assert::assertSame('fixcity::filament.forms.components.dc-select', $type->getView());

        $name = $fields['name'];
        Assert::assertInstanceOf(TextInput::class, $name);
        Assert::assertSame('fixcity::filament.forms.components.dc-text-input', $name->getView());
        Assert::assertSame(__('fixcity::ticket_form.fields.title.label'), $name->getLabel());
        Assert::assertSame(255, $name->getMaxLength());

        $details = $fields['content'];
        Assert::assertInstanceOf(Textarea::class, $details);
        Assert::assertSame('fixcity::filament.forms.components.dc-textarea', $details->getView());
        Assert::assertSame(200, $details->getMaxLength(), 'il limite dichiarato e\' applicato (regola max:200), non solo scritto');
        Assert::assertSame(10, $details->getMinLength());
        Assert::assertSame(2, $details->getRows());

        $images = $fields['images'];
        Assert::assertInstanceOf(SpatieMediaLibraryFileUpload::class, $images);
        Assert::assertSame('fixcity::filament.forms.components.dc-file-upload', $images->getView());
        // le regole di sicurezza SEC-04 restano: mime della collezione, massimo 5 file, 10 MB
        Assert::assertSame(['image/jpeg', 'image/png', 'application/pdf'], $images->getAcceptedFileTypes());
        Assert::assertSame(5, $images->getMaxFiles());
        Assert::assertSame(10 * 1024, $images->getMaxSize());
        Assert::assertSame('attachments', $images->getCollection());
    });

    it('shows the author card only to a signed in user, with the real data', function (): void {
        $cards = dcChildren(TicketForm::getDataSchema(designComuni: true)['dataStep']);
        $author = $cards[2];

        Assert::assertInstanceOf(Section::class, $author);
        Assert::assertFalse($author->isVisible(), 'da ospite la scheda Autore e la sua voce dell\'indice non ci sono');
        $authorViews = dcChildren($author);
        Assert::assertCount(1, $authorViews);
        Assert::assertInstanceOf(View::class, $authorViews[0]);
        Assert::assertSame('pub_theme::components.wizard.data-author', $authorViews[0]->getView());
    });

    it('does not rename the step: the ?step= key depends on its label', function (): void {
        $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
        {
            public function execute(): string
            {
                return '# Informativa di test';
            }
        });

        $steps = app(TicketForm::class)->getSteps();
        $data = $steps['data'];

        Assert::assertSame((string) __('fixcity::fixcity.ticket.steps.data.label'), $data->getLabel());
        $children = dcChildren($data);
        Assert::assertCount(3, $children);
        Assert::assertInstanceOf(View::class, $children[2]);
        Assert::assertSame('pub_theme::components.wizard.data-step', $children[2]->getView());
    });
});
