<?php

declare(strict_types=1);

use Dom\Element;
use Dom\HTMLDocument;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;
use Livewire\Livewire;
use Modules\Xot\Tests\XotBaseTestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\preg_match;
use function Safe\preg_match_all;

uses(XotBaseTestCase::class);

/*
 * DCP-43, passo 2 del wizard: contratto di markup delle viste Design Comuni dei campi
 * (riferimento: sito/segnalazione-02-dati e segnalazione-01-privacy). Le viste dc-* non sono ancora cablate in TicketForm:
 * qui si fissa cio' che il cablaggio puo' dare per scontato (struttura, id/for, required, aria-*, escape).
 *
 * I campi sono Filament reali dentro un componente Livewire di prova con stato in memoria: nessuna riga di database, nessun
 * file di lingua scritto (`lang.save_missing_translations` spento, vedi dcFieldsRender()).
 */

/** Valore ostile: chiude l'attributo, apre uno script e prova ad aggiungere un gestore di evento. */
const DC_EVIL = '"><script>alert(1)</script><b onmouseover="alert(2)" x="';

/** I campi del passo 2 con le viste in prova. Ogni campo ha `data-element="f-<chiave>"` per ritrovarlo senza dipendere dalla struttura. */
final class DcFieldsProbe extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public bool $required = true;

    public bool $disabled = false;

    public ?int $contentMaxLength = 200;

    public string $label = 'Etichetta del campo';

    public string $optionKey = 'a';

    public string $optionLabel = 'Buca';

    /** Messaggio al posto di quello standard di Filament quando il campo e' vuoto (vuoto = standard). */
    public string $message = '';

    public string $placeholder = 'Cerca un luogo';

    /** aria-describedby passato dal chiamante al consenso (TicketForm: lo span `#ticket-privacy-error` della shell); vuoto = nessuno. */
    public string $privacyDescribedBy = '';

    public function form(Schema $schema): Schema
    {
        $views = 'fixcity::filament.forms.components.';
        $messages = $this->message === '' ? [] : ['required' => $this->message, 'accepted' => $this->message];

        return $schema->statePath('data')->components([
            Select::make('type')
                ->label($this->label)->native()->required($this->required)
                ->options([$this->optionKey => $this->optionLabel, 'b' => 'Perdita'])
                ->validationMessages($messages)->extraAttributes(['data-element' => 'f-type'])
                ->view($views.'dc-select'),
            TextInput::make('name')
                ->label($this->label)->required($this->required)->minLength(3)->maxLength(255)->placeholder('SEGNAPOSTO')
                ->validationMessages($messages)->extraAttributes(['data-element' => 'f-name'])
                ->view($views.'dc-text-input'),
            Textarea::make('content')
                ->label($this->label)->required($this->required)->minLength(10)->rows(2)->maxLength($this->contentMaxLength)
                ->placeholder('SEGNAPOSTO')->validationMessages($messages)->extraAttributes(['data-element' => 'f-content'])
                ->view($views.'dc-textarea'),
            TextInput::make('location.address')
                ->hiddenLabel()->placeholder($this->placeholder)->required($this->required)->disabled($this->disabled)
                ->validationMessages($messages)->extraAttributes(['data-element' => 'f-address'])
                ->view($views.'dc-place-autocomplete'),
            Checkbox::make('privacyAccepted')
                ->label($this->label)->required($this->required)->accepted()
                ->validationMessages($messages)->extraAttributes(['data-element' => 'f-privacy'])
                ->extraInputAttributes($this->privacyDescribedBy === '' ? [] : ['aria-describedby' => $this->privacyDescribedBy])
                ->view($views.'privacy-consent-checkbox'),
        ]);
    }

    public function check(): void
    {
        // getSchema() e' tipato (?Schema): `$this->form` passa dal __get dinamico di Filament e per PHPStan e' mixed.
        ($this->getSchema('form') ?? throw new LogicException('Lo schema "form" della sonda non e\' stato costruito.'))->validate();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}

/**
 * @param  array<string, mixed>  $props  proprieta pubbliche della sonda (stato incluso)
 */
function dcFieldsHtml(array $props = [], bool $validate = false): string
{
    // La chiave di lingua mancante scrive la riga nel DB e il file lang: nei test mai.
    config(['lang.save_missing_translations' => false]);
    app()->setLocale('it');

    $component = Livewire::test(DcFieldsProbe::class, $props);
    if ($validate) {
        $component->call('check');
    }

    return $component->html();
}

/**
 * @param  array<string, mixed>  $props
 */
function dcFieldsDoc(array $props = [], bool $validate = false): HTMLDocument
{
    return HTMLDocument::createFromString(dcFieldsHtml($props, $validate), LIBXML_NOERROR);
}

function dcFieldOne(HTMLDocument|Element $scope, string $selector): Element
{
    return $scope->querySelector($selector) ?? Assert::fail('Selettore non trovato: '.$selector);
}

function dcFieldCount(HTMLDocument|Element $scope, string $selector): int
{
    return $scope->querySelectorAll($selector)->length;
}

function dcFieldText(Element $element): string
{
    return trim($element->textContent ?? '');
}

/**
 * @return list<string>
 */
function dcFieldTokens(string $value): array
{
    return array_values(array_filter(explode(' ', $value), static fn (string $token): bool => $token !== ''));
}

function dcFieldById(HTMLDocument $document, string $id): Element
{
    return $document->getElementById($id) ?? Assert::fail('Nessun elemento con id '.$id);
}

/**
 * Il controllo di ogni campo: id dell'elemento di input e selettori del suo contratto (dataset per i test trasversali).
 *
 * @return array<string, array{0: string, 1: string, 2: string}>
 */
function dcFieldCases(): array
{
    return [
        'dc-select' => ['form.type', 'select.u-grey-dark', '.select-wrapper > label.visually-hidden'],
        'dc-text-input' => ['form.name', 'input.form-control', '.text-area-wrapper > .form-group.cmp-input > label.cmp-input__label'],
        'dc-textarea' => ['form.content', 'textarea.text-area', '.cmp-text-area > .form-group > label.d-block'],
        'dc-place-autocomplete' => ['form.location.address', 'input.autocomplete', '.cmp-input-autocomplete > .form-group > label.label-input'],
        'privacy-consent-checkbox' => ['form.privacyAccepted', 'input[type="checkbox"]', '.form-check > .checkbox-body > input + label'],
    ];
}

// ---------------------------------------------------------------------------------------------------------------------
// Struttura del riferimento
// ---------------------------------------------------------------------------------------------------------------------

it('renders dc-select as the reference select-wrapper with a hidden label and a native select', function (): void {
    $document = dcFieldsDoc();

    $label = dcFieldOne($document, '.select-wrapper.select-partials > label.visually-hidden');
    $select = dcFieldOne($document, '.select-wrapper.select-partials > select.u-grey-dark');
    Assert::assertSame($select->getAttribute('id'), $label->getAttribute('for'));
    Assert::assertTrue($select->hasAttribute('required'));
    Assert::assertSame('data.type', $select->getAttribute('wire:model'));

    // Prima opzione vuota che ripete la label con l'asterisco (come nel riferimento), poi le due opzioni del campo.
    $options = $select->querySelectorAll('option');
    Assert::assertCount(3, $options);
    $first = $options->item(0) ?? Assert::fail('Prima opzione mancante.');
    Assert::assertSame('', $first->getAttribute('value'));
    Assert::assertSame('Etichetta del campo*', dcFieldText($first));
    Assert::assertSame('Etichetta del campo*', dcFieldText($label));
});

it('selects the option matching the state and the empty option when the state is empty', function (): void {
    $empty = dcFieldOne(dcFieldsDoc(), 'select.u-grey-dark');
    Assert::assertSame(1, dcFieldCount($empty, 'option[selected]'));
    Assert::assertSame('', dcFieldOne($empty, 'option[selected]')->getAttribute('value'));

    $chosen = dcFieldOne(dcFieldsDoc(['data' => ['type' => 'b']]), 'select.u-grey-dark');
    Assert::assertSame(1, dcFieldCount($chosen, 'option[selected]'));
    Assert::assertSame('b', dcFieldOne($chosen, 'option[selected]')->getAttribute('value'));
    Assert::assertSame('Perdita', dcFieldText(dcFieldOne($chosen, 'option[selected]')));
});

it('renders dc-text-input as text-area-wrapper > form-group cmp-input with the label before the input', function (): void {
    $document = dcFieldsDoc();

    $group = dcFieldOne($document, '.text-area-wrapper > .form-group.cmp-input');
    $label = dcFieldOne($group, 'label.cmp-input__label');
    $input = dcFieldOne($group, 'label.cmp-input__label + input.form-control');
    Assert::assertSame('text', $input->getAttribute('type'));
    Assert::assertSame('form.name', $input->getAttribute('id'));
    Assert::assertSame('form.name', $label->getAttribute('for'));
    Assert::assertSame('Etichetta del campo*', dcFieldText($label));
    Assert::assertSame('3', $input->getAttribute('minlength'));
    Assert::assertSame('255', $input->getAttribute('maxlength'));
    Assert::assertSame('data.name', $input->getAttribute('wire:model'));
    // Il riferimento non ha segnaposto: il testo guida e' la label flottante. Il placeholder() di Filament non va emesso.
    Assert::assertFalse($input->hasAttribute('placeholder'));
    // La label si alza con `active` (Alpine: fuoco o stato non vuoto), senza Bootstrap JS.
    $binding = $label->getAttribute(':class') ?? '';
    Assert::assertStringContainsString("'active'", $binding);
    Assert::assertStringContainsString('data.name', $binding);
});

it('renders dc-textarea as cmp-text-area with rows, the limit in maxlength and in the label hint', function (): void {
    $document = dcFieldsDoc();

    $group = dcFieldOne($document, '.cmp-text-area > .form-group');
    $label = dcFieldOne($group, 'label.d-block');
    $textarea = dcFieldOne($group, 'label.d-block + textarea.text-area');
    $hint = dcFieldOne($group, 'textarea.text-area + span.label');
    Assert::assertSame('form.content', $label->getAttribute('for'));
    Assert::assertSame('form.content', $textarea->getAttribute('id'));
    Assert::assertSame('2', $textarea->getAttribute('rows'));
    Assert::assertSame('10', $textarea->getAttribute('minlength'));
    Assert::assertTrue($textarea->hasAttribute('required'));
    Assert::assertFalse($textarea->hasAttribute('placeholder'));
    Assert::assertSame('data.content', $textarea->getAttribute('wire:model'));
    Assert::assertSame('form.content-hint', $hint->getAttribute('id'));
    Assert::assertSame('form.content-hint', $textarea->getAttribute('aria-describedby'));
    Assert::assertSame('200', $textarea->getAttribute('maxlength'));
    Assert::assertStringContainsString('200', dcFieldText($hint));
});

it('keeps the textarea limit and its hint on the same number and drops both without maxLength', function (): void {
    $limited = dcFieldsDoc(['contentMaxLength' => 50]);
    $textarea = dcFieldOne($limited, 'textarea.text-area');
    Assert::assertSame('50', $textarea->getAttribute('maxlength'));
    Assert::assertSame(1, preg_match('/\b50\b/', dcFieldText(dcFieldOne($limited, 'textarea.text-area + span.label'))));

    $unlimited = dcFieldsDoc(['contentMaxLength' => null]);
    $textarea = dcFieldOne($unlimited, 'textarea.text-area');
    Assert::assertFalse($textarea->hasAttribute('maxlength'));
    Assert::assertFalse($textarea->hasAttribute('aria-describedby'));
    Assert::assertSame(0, dcFieldCount($unlimited, '.cmp-text-area span.label'));
});

it('renders dc-place-autocomplete with the search input, the geolocation link and a status line', function (): void {
    $document = dcFieldsDoc();

    $group = dcFieldOne($document, '.cmp-input-autocomplete > .form-group');
    $label = dcFieldOne($group, 'label.label-input.d-none');
    $input = dcFieldOne($group, 'input[type="search"].autocomplete');
    Assert::assertSame('form.location.address', $input->getAttribute('id'));
    Assert::assertSame('form.location.address', $label->getAttribute('for'));
    Assert::assertSame('data.location.address', $input->getAttribute('wire:model'));
    Assert::assertTrue($input->hasAttribute('required'));
    // Il segnaposto e la label dicono la stessa cosa e portano l'asterisco dei campi obbligatori.
    Assert::assertSame('Cerca un luogo*', $input->getAttribute('placeholder'));
    Assert::assertSame('Cerca un luogo*', dcFieldText($label));

    $link = dcFieldOne($group, '.link-wrapper > a.list-item');
    Assert::assertSame('#', $link->getAttribute('href'));
    Assert::assertStringContainsString('locate()', $link->getAttribute('x-on:click.prevent') ?? '');
    Assert::assertNotSame('', dcFieldText($link), 'Il link "Usa la tua posizione" deve avere un testo.');

    // Riga di stato per i messaggi di geolocalizzazione: annunciata ai lettori di schermo, nascosta finche' e' vuota.
    $status = dcFieldOne($group, 'p[role="status"]');
    Assert::assertTrue($status->hasAttribute('hidden'));
    Assert::assertSame('message', $status->getAttribute('x-text'));
});

it('wires the geolocation of dc-place-autocomplete to the map picker chain, with no HTTPS or permission message', function (): void {
    $root = dcFieldOne(dcFieldsDoc(), '.cmp-input-autocomplete');
    $script = $root->getAttribute('x-data') ?? '';

    // Il link non decide nulla: delega al picker modificabile (DCP-127). GPS, posizione approssimativa da IP o silenzio si
    // decidono li', in un posto solo, con le stesse regole del pulsante della mappa.
    Assert::assertStringContainsString("coordinate-picker-lit:not([readonly])", $script);
    Assert::assertStringContainsString('_requestGeolocation()', $script);

    // Niente contesto sicuro obbligatorio e niente messaggi di errore di posizione: l'utente non puo' risolverli.
    Assert::assertStringNotContainsString('isSecureContext', $script);
    Assert::assertStringNotContainsString('getCurrentPosition', $script);
    foreach (['insecure', 'denied', 'timeout', 'unsupported', 'unavailable'] as $errorKey) {
        Assert::assertStringNotContainsString($errorKey, $script, "Messaggio di errore di posizione reintrodotto: {$errorKey}");
    }
    Assert::assertDoesNotMatchRegularExpression('/https/i', $root->textContent ?? '', 'Nessun testo visibile deve nominare HTTPS.');
});

it('hides the geolocation link and the required mark when dc-place-autocomplete is disabled', function (): void {
    $document = dcFieldsDoc(['disabled' => true]);

    $input = dcFieldOne($document, 'input.autocomplete');
    Assert::assertTrue($input->hasAttribute('disabled'));
    Assert::assertFalse($input->hasAttribute('required'));
    Assert::assertSame('Cerca un luogo', $input->getAttribute('placeholder'));
    Assert::assertSame(0, dcFieldCount($document, '.cmp-input-autocomplete .link-wrapper'));
    Assert::assertSame(0, dcFieldCount($document, '.cmp-input-autocomplete p[role="status"]'));
});

it('renders privacy-consent-checkbox as form-check with the input immediately before its label', function (): void {
    $document = dcFieldsDoc();

    $input = dcFieldOne($document, '.form-check > .checkbox-body > input[type="checkbox"]');
    $label = dcFieldOne($document, '.form-check > .checkbox-body > input[type="checkbox"] + label');
    Assert::assertSame('form.privacyAccepted', $input->getAttribute('id'));
    Assert::assertSame('form.privacyAccepted', $label->getAttribute('for'));
    Assert::assertSame('Etichetta del campo', dcFieldText($label));
    Assert::assertTrue($input->hasAttribute('required'));
    Assert::assertSame('data.privacyAccepted', $input->getAttribute('wire:model'));
    Assert::assertSame(0, dcFieldCount($document, '.form-check .form-feedback'), 'Nessun errore prima della validazione.');
});

// ---------------------------------------------------------------------------------------------------------------------
// Trasversali: id/for, required, extra attributi, errori
// ---------------------------------------------------------------------------------------------------------------------

it('associates every label with its control through id and for', function (string $id, string $control, string $label): void {
    $document = dcFieldsDoc();

    $input = dcFieldById($document, $id);
    Assert::assertTrue($input->matches($control), 'L\'elemento '.$id.' non e\' '.$control);
    Assert::assertSame($id, dcFieldOne($document, $label)->getAttribute('for'));
    Assert::assertSame(1, dcFieldCount($document, '[id="'.$id.'"]'), 'id duplicato: '.$id);
})->with(dcFieldCases());

it('marks a required field with the attribute and an asterisk, an optional one with neither', function (string $id, string $control, string $label): void {
    $required = dcFieldsDoc();
    Assert::assertTrue(dcFieldById($required, $id)->hasAttribute('required'));

    $optional = dcFieldsDoc(['required' => false]);
    Assert::assertFalse(dcFieldById($optional, $id)->hasAttribute('required'));
    Assert::assertStringNotContainsString('*', dcFieldText(dcFieldOne($optional, $label)));
    if ($id !== 'form.privacyAccepted') {
        Assert::assertStringEndsWith('*', dcFieldText(dcFieldOne($required, $label)));
    }
})->with(dcFieldCases());

it('keeps the extra attributes of the Filament field on the rendered markup', function (): void {
    $document = dcFieldsDoc();

    foreach (['f-type', 'f-name', 'f-content', 'f-address', 'f-privacy'] as $hook) {
        Assert::assertSame(1, dcFieldCount($document, '[data-element="'.$hook.'"]'), 'data-element perso: '.$hook);
    }
});

it('emits no error state before validation', function (string $id): void {
    $document = dcFieldsDoc();

    $input = dcFieldById($document, $id);
    Assert::assertFalse($input->hasAttribute('aria-invalid'));
    Assert::assertFalse($input->classList->contains('is-invalid'));
    Assert::assertSame(0, dcFieldCount($document, '[data-validation-error]'));
    // Solo il consenso privacy porta sempre `data-field-wrapper` (DCP-146: il gestore `form-validation-error` di Filament lo cerca per aprire il passo 1
    // quando l'errore e' nascosto); gli altri campi lo emettono soltanto con un errore.
    $wrappers = $document->querySelectorAll('[data-field-wrapper]');
    Assert::assertSame(1, $wrappers->length);
    Assert::assertNotNull($wrappers->item(0)?->querySelector('[id="form.privacyAccepted"]'));
    Assert::assertStringNotContainsString($id.'-error', $input->getAttribute('aria-describedby') ?? '');
})->with(['form.type', 'form.name', 'form.content', 'form.location.address', 'form.privacyAccepted']);

it('links every invalid control to its message with aria-invalid and aria-describedby', function (string $id): void {
    $document = dcFieldsDoc(validate: true);

    $input = dcFieldById($document, $id);
    Assert::assertSame('true', $input->getAttribute('aria-invalid'));
    $describedBy = dcFieldTokens($input->getAttribute('aria-describedby') ?? '');
    Assert::assertContains($id.'-error', $describedBy);
    foreach ($describedBy as $target) {
        Assert::assertNotSame('', dcFieldText(dcFieldById($document, $target)), 'aria-describedby punta a un elemento vuoto: '.$target);
    }
})->with(['form.type', 'form.name', 'form.content', 'form.location.address']);

it('lets the caller own the aria-describedby of the privacy consent and still flags it invalid', function (): void {
    $input = dcFieldById(dcFieldsDoc(['privacyDescribedBy' => 'ticket-privacy-error'], validate: true), 'form.privacyAccepted');

    Assert::assertSame('true', $input->getAttribute('aria-invalid'));
    Assert::assertSame('ticket-privacy-error', $input->getAttribute('aria-describedby'));
});

it('shows the validation message in the error element of the field', function (string $id): void {
    $document = dcFieldsDoc(['message' => 'Messaggio di prova'], validate: true);

    $message = $id === 'form.privacyAccepted' ? dcFieldOne($document, '.form-check > p.form-feedback') : dcFieldById($document, $id.'-error');
    Assert::assertSame('Messaggio di prova', dcFieldText($message));
    Assert::assertTrue($message->classList->contains('form-feedback'));
})->with(['form.type', 'form.name', 'form.content', 'form.location.address', 'form.privacyAccepted']);

it('marks the invalid control of dc-select, dc-text-input and dc-textarea with is-invalid', function (string $id): void {
    Assert::assertTrue(dcFieldById(dcFieldsDoc(validate: true), $id)->classList->contains('is-invalid'));
})->with(['form.type', 'form.name', 'form.content']);

it('gives the first error of the Filament scroll handler a data-field-wrapper to scroll to', function (): void {
    // vendor/filament/schemas: handleFormValidationError fa querySelector('[data-validation-error]').closest('[data-field-wrapper]').scrollIntoView():
    // senza wrapper e' un TypeError. Ogni messaggio marcato deve quindi stare dentro un wrapper marcato.
    $document = dcFieldsDoc(validate: true);

    $messages = $document->querySelectorAll('[data-validation-error]');
    Assert::assertGreaterThanOrEqual(3, $messages->length);
    foreach ($messages as $message) {
        Assert::assertNotNull($message->closest('[data-field-wrapper]'), 'Messaggio senza data-field-wrapper: '.$message->getAttribute('id'));
    }
});

it('adds the hint and then the error to the textarea aria-describedby in this order', function (): void {
    $textarea = dcFieldById(dcFieldsDoc(validate: true), 'form.content');

    Assert::assertSame('form.content-hint form.content-error', $textarea->getAttribute('aria-describedby'));
});

it('keeps the privacy error as visible feedback for sighted users only (the shell announces it)', function (): void {
    $feedback = dcFieldOne(dcFieldsDoc(validate: true), '.form-check > p.form-feedback');

    Assert::assertSame('true', $feedback->getAttribute('aria-hidden'));
    Assert::assertTrue(dcFieldById(dcFieldsDoc(validate: true), 'form.privacyAccepted')->classList->contains('fi-invalid'));
});

// ---------------------------------------------------------------------------------------------------------------------
// Escape
// ---------------------------------------------------------------------------------------------------------------------

it('escapes label, option and message text in every field view', function (): void {
    $html = dcFieldsHtml(['label' => DC_EVIL, 'optionKey' => DC_EVIL, 'optionLabel' => DC_EVIL, 'message' => DC_EVIL], validate: true);
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);

    Assert::assertStringNotContainsString('<script', $html);
    Assert::assertStringNotContainsString('onmouseover="', $html);
    Assert::assertSame(0, dcFieldCount($document, 'script'), 'Un valore con <script> e\' diventato un elemento.');
    Assert::assertSame(0, dcFieldCount($document, '[onmouseover]'), 'Un valore con " ha aggiunto un attributo.');

    // Il testo ostile resta leggibile come testo: escapato, non scartato.
    Assert::assertSame(DC_EVIL.'*', dcFieldText(dcFieldOne($document, '.select-wrapper > label')));
    Assert::assertSame(DC_EVIL.'*', dcFieldText(dcFieldOne($document, 'label.cmp-input__label')));
    Assert::assertSame(DC_EVIL.'*', dcFieldText(dcFieldOne($document, '.cmp-text-area label')));
    Assert::assertSame(DC_EVIL, dcFieldText(dcFieldOne($document, '.form-check label')));
    Assert::assertSame(DC_EVIL, dcFieldText(dcFieldOne($document, '.form-check > p.form-feedback')));
    foreach (['form.type', 'form.name', 'form.content', 'form.location.address'] as $id) {
        Assert::assertSame(DC_EVIL, dcFieldText(dcFieldById($document, $id.'-error')), 'Messaggio non escapato: '.$id);
    }
    $option = dcFieldOne($document, 'select.u-grey-dark')->querySelectorAll('option')->item(1) ?? Assert::fail('Opzione mancante.');
    Assert::assertSame(DC_EVIL, $option->getAttribute('value'));
    Assert::assertSame(DC_EVIL, dcFieldText($option));
});

it('escapes a hostile state when it selects an option', function (): void {
    $html = dcFieldsHtml(['optionKey' => DC_EVIL, 'optionLabel' => DC_EVIL, 'data' => ['type' => DC_EVIL]]);
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);

    Assert::assertStringNotContainsString('<script', $html);
    Assert::assertSame(0, dcFieldCount($document, '[onmouseover]'));
    $selected = dcFieldOne($document, 'select.u-grey-dark option[selected]');
    Assert::assertSame(DC_EVIL, $selected->getAttribute('value'));
    Assert::assertSame(1, dcFieldCount($document, 'select.u-grey-dark option[selected]'));
});

/*
 * Il segnaposto viene da `->placeholder()` o dalla lingua (modificabile da admin), non dal cittadino. In dc-place-autocomplete l'ultimo `merge([...])`
 * non usa piu' `escape: false`: un `"` nel segnaposto non deve uscire dall'attributo. Difetto trovato da VIEWTESTS-TEMA il 2026-10-13 e corretto.
 */
it('escapes the placeholder attribute of dc-place-autocomplete', function (): void {
    $html = dcFieldsHtml(['placeholder' => DC_EVIL]);
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);

    Assert::assertSame(0, dcFieldCount($document, 'script'), 'Il segnaposto ha aperto un elemento script.');
    Assert::assertSame(0, dcFieldCount($document, '[onmouseover]'), 'Il segnaposto ha aggiunto un attributo.');
    Assert::assertSame(DC_EVIL.'*', dcFieldById($document, 'form.location.address')->getAttribute('placeholder'));
});

// ---------------------------------------------------------------------------------------------------------------------
// Sorgenti
// ---------------------------------------------------------------------------------------------------------------------

/**
 * @return array<string, array{0: string}>
 */
function dcFieldViewFiles(): array
{
    $directory = dirname(__DIR__, 2).'/resources/views/filament/forms/components';
    $files = [];
    foreach (['dc-select', 'dc-text-input', 'dc-textarea', 'dc-place-autocomplete', 'privacy-consent-checkbox'] as $name) {
        $files[$name] = [$directory.'/'.$name.'.blade.php'];
    }

    return $files;
}

it('keeps the source free of important, html directives and raw output of values', function (string $file): void {
    Assert::assertFileExists($file);
    $source = file_get_contents($file);

    Assert::assertStringNotContainsString('!important', $source);
    Assert::assertStringNotContainsString('x-html', $source);
    Assert::assertStringNotContainsString('innerHTML', $source);

    // `{!! !!}` solo per scegliere tra due stringhe fisse (selected, disabled): mai una variabile come risultato.
    $count = preg_match_all('/\{!!\s*(.*?)\s*!!\}/s', $source, $matches);
    Assert::assertIsArray($matches[1]);
    Assert::assertCount($count, $matches[1]);
    foreach ($matches[1] as $expression) {
        Assert::assertIsString($expression);
        Assert::assertSame(1, preg_match('/\?\s*\'[^\'$\\\\]*\'\s*:\s*\'[^\'$\\\\]*\'$/', $expression), '{!! !!} con valore non fisso: '.$expression);
    }
})->with(dcFieldViewFiles());
