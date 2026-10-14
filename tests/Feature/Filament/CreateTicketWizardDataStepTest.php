<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-128: il passo "dati" del wizard reale (widget con `TicketForm::getSteps()`) ha il markup di sito/segnalazione-02-dati e funziona.
 * Render e validazione del widget vero con un utente autenticato (i dati del DB sono quelli di prova della transazione).
 */

beforeEach(function (): void {
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });
});

describe('data step markup', function (): void {
    it('renders the three cards, the index, the native select, the 200 character limit, the upload and its own navigation', function (): void {
        $user = UserFactory::new()->createOne(['name' => 'Giulia Bianchi', 'email' => 'giulia.bianchi@example.test']);
        $this->actingAs($user);
        app()->setLocale('it');

        $html = Livewire::test(CreateTicketWizardWidget::class)->html();
        $document = HTMLDocument::createFromString('<!doctype html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);

        foreach (['report-place', 'report-info', 'report-author'] as $id) {
            Assert::assertNotNull($document->querySelector("section.it-page-section#{$id} > .cmp-card > .card.has-bkg-grey"), "scheda #{$id}");
        }
        // indice laterale: una voce per scheda, nell'ordine delle schede
        $links = array_map(static fn ($link): string => (string) $link->getAttribute('href'), iterator_to_array($document->querySelectorAll('.cmp-navscroll a.nav-link')));
        Assert::assertSame(['#report-place', '#report-info', '#report-author'], $links);

        $select = $document->querySelector('.select-wrapper select[required]');
        Assert::assertNotNull($select);
        Assert::assertSame('Tipo di disservizio*', trim((string) $select->querySelector('option')?->textContent), 'prima opzione = etichetta + asterisco');
        Assert::assertNull($document->querySelector('.fi-select-input-btn'), 'niente select JS di Filament');

        $details = $document->querySelector('.cmp-text-area textarea');
        Assert::assertSame('200', $details?->getAttribute('maxlength'), 'il limite scritto e\' anche applicato dal browser');
        Assert::assertStringContainsString('200', (string) $document->querySelector('.cmp-text-area span.label')?->textContent);

        $upload = $document->querySelector('.btn-wrapper input[type=file]');
        Assert::assertNotNull($upload);
        Assert::assertSame('image/jpeg,image/png,application/pdf', $upload->getAttribute('accept'));
        Assert::assertNotNull($upload->getAttribute('multiple'));
        Assert::assertNull($document->querySelector('.filepond--root'), 'il tema non carica il CSS di FilePond: il widget di Filament non va usato');

        // scheda Autore: dati veri dell'utente (nome, email), niente nome di esempio del riferimento
        $author = (string) $document->querySelector('#report-author')?->textContent;
        Assert::assertStringContainsString('Giulia Bianchi', $author);
        Assert::assertStringContainsString('giulia.bianchi@example.test', $author);
        Assert::assertStringNotContainsString('Mario Rossi', $author);

        // navigazione propria del passo: la navigazione di ripiego del wizard non va duplicata
        $nav = $document->querySelector('.steppers-content > .cmp-nav-steps[data-wizard-nav]');
        Assert::assertNotNull($nav);
        Assert::assertStringContainsString('requestNextStep()', (string) $nav->querySelector('.steppers-btn-confirm')?->getAttribute('x-on:click'));
        Assert::assertNotNull($document->querySelector('p.title-xsmall.d-lg-none'), 'avviso dei campi obbligatori (< 992 px)');
        Assert::assertNotNull($document->querySelector('[data-element="location-map"]'));
    });

    it('does not show the author card to a guest', function (): void {
        app()->setLocale('it');

        $html = Livewire::test(CreateTicketWizardWidget::class)->html();
        $document = HTMLDocument::createFromString('<!doctype html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);

        Assert::assertNull($document->querySelector('#report-author'));
        Assert::assertCount(2, $document->querySelectorAll('.cmp-navscroll a.nav-link'), 'senza la scheda Autore sparisce anche la sua voce');
    });
});

describe('data step validation', function (): void {
    it('rejects details over 200 characters', function (): void {
        $this->actingAs(UserFactory::new()->createOne());

        Livewire::test(CreateTicketWizardWidget::class)
            ->set('data.privacyAccepted', true)
            ->set('data.content', str_repeat('a', 201))
            ->call('save')
            ->assertHasErrors(['data.content' => 'max']);
    });

    it('requires the place, the type, the title and the details with readable messages in the page language', function (string $locale, string $message): void {
        $this->actingAs(UserFactory::new()->createOne());
        app()->setLocale($locale);

        $component = Livewire::test(CreateTicketWizardWidget::class)->set('data.privacyAccepted', true)->call('save');
        $component->assertHasErrors(['data.location.address' => 'required', 'data.type' => 'required', 'data.name' => 'required', 'data.content' => 'required']);
        // messaggio nella lingua della pagina (non l'italiano di `user::validation`, che `Modules/Lang` applica a ogni campo)
        $component->assertSee($message);
    })->with([
        'it' => ['it', 'Il campo titolo è obbligatorio.'],
        'en' => ['en', 'The title field is required.'],
        'de' => ['de', 'Das Feld titel ist erforderlich.'],
        'es' => ['es', 'El campo título es obligatorio.'],
    ]);
});
