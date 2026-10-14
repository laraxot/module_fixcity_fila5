<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessPrivacyPolicyAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\preg_replace;

uses(TestCase::class);

/*
 * DCP-162: le note legali (disclaimer e condizioni di utilizzo) del tenant hanno le 11 sezioni del modello e nessun segnaposto.
 * Il codice non scrive nessun valore legale (titolare, foro, licenza, contatti): li fornisce l'ente. Il modello e' una proposta da
 * far rivedere al legale, non un elenco di obblighi: lo dice la nota in testa, che blocca la pubblicazione finche' c'e'.
 */
dataset('notes locales', ['it', 'en', 'de', 'es']);

/** Sezioni del modello che richiedono un dato dell'ente (contengono `[[...]]` nel modello). */
const NOTES_WITH_ENTITY_DATA = ['owner', 'purpose', 'contributions', 'liability', 'ip', 'changes', 'law', 'contacts'];

/**
 * @param  list<string>  $without
 */
function legalNotesFixture(array $without = [], string $body = 'Testo fornito dall’ente.'): string
{
    $text = "# Note legali\n\nIntroduzione.\n";
    foreach (AssessPrivacyPolicyAction::NOTES_SECTIONS as $key) {
        if (! in_array($key, $without, true)) {
            $text .= "\n## ".__('fixcity::legal_notes.sections.'.$key.'.title')."\n\n".$body."\n";
        }
    }

    return $text;
}

/**
 * @return array<string, string>
 */
function assessNotes(?string $markdown): array
{
    return app(AssessPrivacyPolicyAction::class)->execute($markdown, AssessPrivacyPolicyAction::DOCUMENT_LEGAL_NOTES);
}

function legalNotesTemplate(string $locale): string
{
    return file_get_contents(dirname(__DIR__, 2)."/resources/templates/legal-notes.{$locale}.md");
}

it('accepts notes that have every section and no placeholder', function (string $locale): void {
    app()->setLocale($locale);

    expect(assessNotes(legalNotesFixture()))->toBe([]);
})->with('notes locales');

it('lists every section when there is no text', function (): void {
    expect(assessNotes(null))->toBe(array_fill_keys(AssessPrivacyPolicyAction::NOTES_SECTIONS, 'missing'));
});

it('names the sections that are missing or empty', function (): void {
    $text = legalNotesFixture(['law', 'contacts', 'liability'])."\n## ".__('fixcity::legal_notes.sections.liability.title')."\n\n   \n";

    expect(assessNotes($text))->toBe(['liability' => 'missing', 'law' => 'missing', 'contacts' => 'missing']);
});

it('rejects placeholders inside a section and outside the sections', function (string $placeholder): void {
    $inside = str_replace('Testo fornito dall’ente.', $placeholder, legalNotesFixture(), $count);
    expect($count)->toBe(11);
    expect(assessNotes($inside))->toBe(array_fill_keys(AssessPrivacyPolicyAction::NOTES_SECTIONS, 'placeholder'));

    expect(assessNotes(str_replace('Introduzione.', $placeholder, legalNotesFixture())))->toBe(['document' => 'placeholder']);
})->with([
    'double brackets' => ['[[foro competente]]'],
    'stub file' => ['Edit this file to define the terms of service for your application.'],
    'lorem ipsum' => ['Lorem ipsum dolor sit amet'],
    'todo' => ['TODO indicare la legge'],
    'example address' => ['legale@comune.example.it'],
]);

it('keeps the privacy policy check unchanged when no document is given', function (): void {
    expect(app(AssessPrivacyPolicyAction::class)->execute(null))->toBe(array_fill_keys(AssessPrivacyPolicyAction::SECTIONS, 'missing'));
});

it('ships a template per locale whose headings are the titles of the language file', function (string $locale): void {
    app()->setLocale($locale);
    $template = legalNotesTemplate($locale);

    foreach (AssessPrivacyPolicyAction::NOTES_SECTIONS as $key) {
        Assert::assertStringContainsString("\n## ".__('fixcity::legal_notes.sections.'.$key.'.title')."\n", $template, "Modello {$locale} senza la sezione {$key}.");
    }
})->with('notes locales');

it('does not publish the template as it is: the sections with entity data and the note on top block it', function (string $locale): void {
    app()->setLocale($locale);
    $template = legalNotesTemplate($locale);

    expect(assessNotes($template))->toBe(array_fill_keys(NOTES_WITH_ENTITY_DATA, 'placeholder'));

    // compilati i dati dell'ente, resta la nota in testa: ancora non pubblicabile
    $filled = preg_replace('/\[\[(?!Nota|Note|Hinweis).*?\]\]/u', 'valore', $template);
    expect(assessNotes($filled))->toBe(['document' => 'placeholder']);

    // tolta anche la nota, il modello e' pubblicabile
    $published = preg_replace('/^> \[\[.*\]\]\R/mu', '', $filled);
    expect(assessNotes($published))->toBe([]);
})->with('notes locales');

it('has a title and a hint for every section in every language, with the same keys', function (): void {
    $reference = AssessPrivacyPolicyAction::NOTES_SECTIONS;
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        /** @var array{sections: array<string, array{title: string, hint: string}>, page_title: string} $lang */
        $lang = require dirname(__DIR__, 2)."/lang/{$locale}/legal_notes.php";
        Assert::assertSame($reference, array_keys($lang['sections']), "Chiavi diverse in '{$locale}'");
        foreach ($lang['sections'] as $key => $row) {
            Assert::assertNotSame('', trim($row['title']), "{$locale}.{$key}.title vuoto");
            Assert::assertNotSame('', trim($row['hint']), "{$locale}.{$key}.hint vuoto");
        }
        Assert::assertCount(count($reference), array_unique(array_column($lang['sections'], 'title')), "Titoli duplicati in '{$locale}'");
    }
});

it('says in every language that the service is not an emergency channel', function (string $locale): void {
    expect(legalNotesTemplate($locale))->toContain('112');
})->with('notes locales');
