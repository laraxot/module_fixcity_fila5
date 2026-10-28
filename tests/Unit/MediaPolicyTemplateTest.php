<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\preg_match;
use function Safe\preg_match_all;

class MediaPolicyTemplateTestCase extends TestCase
{
    // The fixtures are static and the rendered route uses a mocked action.
    /** @var list<string> */
    protected $connectionsToTransact = [];
}

uses(MediaPolicyTemplateTestCase::class);

/*
 * DCP-231: modello della media policy (`resources/templates/media-policy.<lingua>.md`), da copiare nel tenant come
 * `lang/<lingua>/media-policy.md`. Non esiste ancora una guardia di pubblicazione (`assess`) per questo documento: questo test
 * protegge solo il modello. Fallisce se in una lingua manca o si aggiunge una sezione, se una sezione perde o guadagna un dato
 * dell'ente (`[[...]]`), se manca un rimando agli altri documenti legali, se compare un trattino lungo o un emoji.
 * Il codice non scrive nessun dato dell'ente: la lista sotto e' l'elenco di che cosa deve fornire il Comune, sezione per sezione.
 */
dataset('media policy locales', ['it', 'en', 'de', 'es']);

/** Dati dell'ente (`[[...]]`) attesi in ciascuna delle 14 sezioni, nell'ordine del modello. */
const MEDIA_POLICY_ENTITY_DATA_BY_SECTION = [1, 6, 5, 1, 0, 0, 1, 3, 1, 3, 2, 1, 1, 3];

/** Posizione (da 0) della sezione sulle segnalazioni di contenuti e di quella sulle foto, che la richiama per titolo. */
const MEDIA_POLICY_REPORT_SECTION = 7;

const MEDIA_POLICY_PHOTO_SECTION = 8;

function mediaPolicyText(string $locale): string
{
    return file_get_contents(dirname(__DIR__, 2)."/resources/templates/media-policy.{$locale}.md");
}

/**
 * @return list<array{title: string, body: string}>
 */
function mediaPolicySections(string $locale): array
{
    // explode e non preg_split: restituisce list<string> (il modello inizia con `# titolo`, ogni sezione parte a inizio riga)
    $parts = explode("\n## ", mediaPolicyText($locale));
    array_shift($parts);

    $sections = [];
    foreach ($parts as $part) {
        [$title, $body] = array_pad(explode("\n", $part, 2), 2, '');

        $sections[] = ['title' => trim($title), 'body' => trim($body)];
    }

    return $sections;
}

it('has every section with a body and the same entity data slots in each locale', function (string $locale): void {
    $sections = mediaPolicySections($locale);

    Assert::assertCount(count(MEDIA_POLICY_ENTITY_DATA_BY_SECTION), $sections, "Media policy {$locale}: numero di sezioni diverso dal modello.");
    $slots = [];
    foreach ($sections as $section) {
        Assert::assertNotSame('', $section['body'], "Media policy {$locale}: sezione senza testo ({$section['title']}).");
        $slots[] = preg_match_all('/\[\[.*?\]\]/u', $section['body']);
    }
    Assert::assertSame(MEDIA_POLICY_ENTITY_DATA_BY_SECTION, $slots, "Media policy {$locale}: dati dell'ente per sezione diversi dal modello.");
})->with('media policy locales');

it('starts with the title and the note for the municipality, so it is never publishable as is', function (string $locale): void {
    $text = mediaPolicyText($locale);

    Assert::assertSame(1, preg_match('/\A# [^\n]+\n\n> \[\[[^\n]+\]\]\n\n## /u', $text), "Media policy {$locale}: manca il titolo o la nota per l'ente.");
})->with('media policy locales');

it('points to privacy, cookie, legal notes and accessibility documents', function (string $locale): void {
    $text = mediaPolicyText($locale);

    foreach (['privacy', 'cookie-policy', 'note-legali', 'accessibilita'] as $page) {
        Assert::assertStringContainsString("]({$page})", $text, "Media policy {$locale}: manca il rimando a {$page}.");
    }
})->with('media policy locales');

it('names the section about reporting content exactly where the photo section refers to it', function (string $locale): void {
    $sections = mediaPolicySections($locale);

    Assert::assertStringContainsString($sections[MEDIA_POLICY_REPORT_SECTION]['title'], $sections[MEDIA_POLICY_PHOTO_SECTION]['body']);
})->with('media policy locales');

it('has no long dash and no emoji', function (string $locale): void {
    $text = mediaPolicyText($locale);

    Assert::assertSame(0, preg_match('/[\x{2013}\x{2014}\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', $text), "Media policy {$locale}: trattino lungo o emoji.");
})->with('media policy locales');

it('renders the model through the shared legal document page', function (string $locale, string $firstSectionTitle): void {
    $template = mediaPolicyText($locale);
    app()->instance(GetPublishedLegalDocumentAction::class, new class($template) extends GetPublishedLegalDocumentAction
    {
        public function __construct(private readonly string $template) {}

        public function execute(string $file): ?string
        {
            return $file === 'media-policy.md' ? $this->template : null;
        }
    });

    /** @var TestCase $this */
    $this->get("/{$locale}/media-policy")
        ->assertOk()
        ->assertSee('data-element="legal-text"', false)
        ->assertSee($firstSectionTitle);
})->with([
    ['it', 'Ente titolare e finalità della policy'],
    ['en', 'Owner and purpose of the policy'],
    ['de', 'Träger und Zweck der Policy'],
    ['es', 'Titular y finalidad de la política'],
]);
