<?php

declare(strict_types=1);

use Dom\Element;
use Dom\HTMLDocument;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Regressione D15 (Themes/Sixteen/docs/decision-log/D15-volt-fragment-explicit-root.md).
 * Il Volt di pages/[container0]/[slug0]/index.blade.php deve avere come radice div.page-content: se il suo
 * wire:id finisce su un elemento dentro il widget, wire:model e callSchemaComponentMethod colpiscono il Volt
 * ("Livewire Entangle Error", MethodNotFoundException cliccando "Avanti").
 * Un ospite viene rimandato al login (TicketGuestLocaleRedirectTest): la pagina si legge da utente autenticato.
 */
it('keeps the page volt rooted on page-content and the wizard widget as its own livewire root', function (): void {
    /** @var TestCase $this */
    $response = $this->actingAs(UserFactory::new()->createOne())->get('/it/tickets/create');
    $response->assertOk();

    $html = $response->getContent();
    Assert::assertIsString($html);
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);

    $voltRoots = $document->querySelectorAll('[wire\:name^="volt-anonymous-fragment"]');
    Assert::assertCount(1, $voltRoots, 'Exactly one Volt fragment must be rendered on the page.');
    $voltRoot = $voltRoots->item(0) ?? Assert::fail('Volt fragment missing.');
    Assert::assertSame('div', strtolower($voltRoot->localName));
    Assert::assertTrue(
        $voltRoot->classList->contains('page-content'),
        'wire:name volt-anonymous-fragment must sit on div.page-content, found: '.$voltRoot->getAttribute('class'),
    );

    $widgetRoot = null;
    foreach ($document->querySelectorAll('[wire\:name]') as $candidate) {
        if ($candidate->getAttribute('wire:name') === CreateTicketWizardWidget::class) {
            $widgetRoot = $candidate;
        }
    }
    Assert::assertInstanceOf(Element::class, $widgetRoot, 'CreateTicketWizardWidget is not rendered on /it/tickets/create.');
    Assert::assertNotSame($voltRoot, $widgetRoot);
    Assert::assertTrue($voltRoot->contains($widgetRoot), 'The widget must be a descendant of the Volt root, not the opposite.');
    Assert::assertFalse($widgetRoot->contains($voltRoot));

    $wizard = $document->querySelector('.cmp-wizard-widget') ?? Assert::fail('.cmp-wizard-widget missing.');
    $owner = $wizard->closest('[wire\:id]') ?? Assert::fail('.cmp-wizard-widget has no wire:id ancestor.');
    Assert::assertSame(
        CreateTicketWizardWidget::class,
        $owner->getAttribute('wire:name'),
        '.cmp-wizard-widget must belong to the widget, not to the page Volt.',
    );
});
