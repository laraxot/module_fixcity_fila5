<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessPrivacyPolicyAction;
use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;

uses(TestCase::class);

/*
 * DCP-162: /{lingua}/note-legali e' la pagina JSON `note-legali.index` (blocco legal-document, `assess: legal-notes`). Risponde sempre 200 con un
 * solo <h1>: col testo completo mostra il testo; con testo assente o ancora segnaposto mostra l'avviso con i titoli delle NOTE LEGALI (non
 * quelli dell'informativa). Il testo vero del tenant lo verifica LegalNotesPublicationGateTest.
 */
function fakeTenantLegalNotes(?string $markdown): void
{
    app()->instance(GetPublishedLegalDocumentAction::class, new class($markdown) extends GetPublishedLegalDocumentAction
    {
        public function __construct(private readonly ?string $markdown) {}

        public function execute(string $file): ?string
        {
            return $this->markdown;
        }
    });
}

function completeLegalNotes(string $locale): string
{
    app()->setLocale($locale);
    $text = "# Titolo del testo\n\nIntroduzione dell’ente.\n";
    foreach (AssessPrivacyPolicyAction::NOTES_SECTIONS as $key) {
        $text .= "\n## ".__('fixcity::legal_notes.sections.'.$key.'.title')."\n\nCorpo {$key} fornito dall’ente.\n";
    }

    return $text."\n<script>alert('unsafe')</script>\n";
}

it('serves complete notes as sanitized text with no warning', function (string $locale): void {
    fakeTenantLegalNotes(completeLegalNotes($locale));
    $title = e((string) __('pub_theme::legal.documents.legal_notes.title'));

    $response = $this->get("/{$locale}/note-legali")->assertOk();
    $response
        ->assertSee('data-element="page-name">'.$title.'</h1>', false)
        ->assertSee('Corpo law fornito dall’ente.', false)
        ->assertSee('data-element="legal-text"', false)
        ->assertDontSee('data-element="legal-incomplete"', false)
        ->assertDontSee("alert('unsafe')", false);
    expect(substr_count((string) $response->getContent(), '<h1'))->toBe(1);
})->with(['it', 'en', 'de', 'es']);

it('warns with the titles of the legal notes, not of the privacy policy, when there is no text', function (string $locale): void {
    fakeTenantLegalNotes(null);
    app()->setLocale($locale);

    $response = $this->get("/{$locale}/note-legali")->assertOk()->assertSee('data-element="legal-incomplete"', false);
    foreach (AssessPrivacyPolicyAction::NOTES_SECTIONS as $key) {
        $response->assertSee(e((string) __('fixcity::legal_notes.sections.'.$key.'.title')), false);
    }
    $response->assertDontSee(e((string) __('fixcity::privacy.sections.dpo.title')), false);
    expect(substr_count((string) $response->getContent(), '<h1'))->toBe(1);
})->with(['it', 'en', 'de', 'es']);

it('lists only the sections that still have fields to fill when the template is published as it is', function (string $locale): void {
    fakeTenantLegalNotes(file_get_contents(dirname(__DIR__, 3)."/resources/templates/legal-notes.{$locale}.md"));
    app()->setLocale($locale);

    $response = $this->get("/{$locale}/note-legali")->assertOk()->assertSee('data-element="legal-incomplete"', false);
    foreach (['owner', 'law', 'contacts'] as $key) {
        $response->assertSee(e((string) __('fixcity::legal_notes.sections.'.$key.'.title')), false);
    }
    $response->assertDontSee('<strong>'.e((string) __('fixcity::legal_notes.sections.links.title')).'</strong>', false);
    $response->assertSee(e((string) __('fixcity::privacy.reason.placeholder')), false);
})->with(['it', 'en', 'de', 'es']);

it('keeps the privacy page on its own check', function (): void {
    fakeTenantLegalNotes(null);
    app()->setLocale('it');

    $response = $this->get('/it/privacy')->assertOk();
    $response->assertSee(e((string) __('fixcity::privacy.sections.dpo.title')), false);
    Assert::assertStringNotContainsString(e((string) __('fixcity::legal_notes.sections.law.title')), (string) $response->getContent());
});
