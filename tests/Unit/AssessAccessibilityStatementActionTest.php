<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessAccessibilityStatementAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\json_decode;

uses(TestCase::class);

/*
 * DCP-132: la dichiarazione di accessibilita' (L. 4/2004 art. 3-quater) e' pubblicabile solo con i dati dell'ente e coerente con la
 * valutazione. Il codice non decide lo stato di conformita': qui si prova che senza i dati, o con dati incoerenti, la verifica lo dica.
 */
/**
 * Dichiarazione completa con valori inventati SOLO per il test (nessuno finisce nel runtime).
 *
 * @param  array<string, mixed>  $override
 * @return array<string, mixed>
 */
function completeDeclaration(array $override = []): array
{
    return array_replace([
        'status' => 'partially_compliant',
        'evaluation' => ['method' => 'self', 'date' => '2026-09-01'],
        'published_at' => '2026-09-15',
        'updated_at' => '2026-09-15',
        'feedback_email' => 'segnalazioni@ente.test',
        'procedure_url' => 'https://www.agid.gov.it/it/design-servizi/accessibilita/procedura-attuazione',
        'non_accessible' => [['content' => 'Mappa interattiva', 'reason' => 'Alternativa da tastiera in sviluppo']],
    ], $override);
}

/**
 * @param  array<string, mixed>  $declaration
 * @return array<string, string>
 */
function assessDeclaration(array $declaration): array
{
    return app(AssessAccessibilityStatementAction::class)->execute($declaration);
}

it('accepts a complete declaration for every status', function (string $status): void {
    expect(assessDeclaration(completeDeclaration(['status' => $status, 'non_accessible' => $status === 'compliant' ? [] : completeDeclaration()['non_accessible']])))->toBe([]);
})->with(['compliant', 'partially_compliant', 'non_compliant']);

it('lists everything the municipality still has to provide when the declaration is empty', function (): void {
    $empty = ['status' => null, 'evaluation' => ['method' => null, 'date' => null], 'published_at' => null, 'updated_at' => null, 'feedback_email' => null, 'procedure_url' => null];

    expect(assessDeclaration($empty))->toBe([
        'status' => 'missing', 'evaluation' => 'missing', 'published_at' => 'missing', 'updated_at' => 'missing',
        'feedback_email' => 'missing', 'procedure_url' => 'missing',
    ]);
});

it('does not accept a compliant claim without a dated evaluation', function (): void {
    $issues = assessDeclaration(completeDeclaration(['status' => 'compliant', 'non_accessible' => [], 'evaluation' => ['method' => 'self', 'date' => null]]));

    expect($issues)->toBe(['evaluation' => 'invalid']);
});

it('requires the inaccessible parts, with a reason, unless the site is compliant', function (mixed $parts): void {
    expect(assessDeclaration(completeDeclaration(['non_accessible' => $parts])))->toBe(['non_accessible' => 'missing']);
})->with([
    'none' => [[]],
    'no reason' => [[['content' => 'Mappa', 'reason' => '  ']]],
    'not a list of parts' => [['Mappa']],
]);

it('rejects invalid values', function (mixed $override, string $key): void {
    Assert::assertIsArray($override);
    /** @var array<string, mixed> $override */
    expect(assessDeclaration(completeDeclaration($override)))->toBe([$key => 'invalid']);
})->with([
    'unknown status' => [['status' => 'conforme'], 'status'],
    'impossible date' => [['published_at' => '2026-02-31'], 'published_at'],
    'date in another format' => [['updated_at' => '15/09/2026'], 'updated_at'],
    'bad email' => [['feedback_email' => 'non-una-email'], 'feedback_email'],
    'procedure over http' => [['procedure_url' => 'http://www.agid.gov.it/x'], 'procedure_url'],
    'unknown method' => [['evaluation' => ['method' => 'robot', 'date' => '2026-09-01']], 'evaluation'],
]);

it('rejects example values left in the declaration', function (mixed $override): void {
    Assert::assertIsArray($override);
    /** @var array<string, mixed> $override */
    expect(assessDeclaration(completeDeclaration($override)))->toHaveKey('document', 'placeholder');
})->with([
    'example.it' => [['non_accessible' => [['content' => 'PDF', 'reason' => 'scrivere a accessibilita@comune.example.it']]]],
    'agid form template' => [['agid_declaration_url' => 'https://form.agid.gov.it/view/xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx']],
    'brackets' => [['non_accessible' => [['content' => '[[DA COMPILARE]]', 'reason' => 'motivo']]]],
]);

it('keeps the same non-text facts in the four language blocks of the tenant page', function (): void {
    $page = json_decode(file_get_contents(dirname(__DIR__, 4).'/config/local/fixcity/database/content/pages/accessibilita.index.json'), true);
    $facts = [];
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        $declaration = data_get($page, "content_blocks.{$locale}.0.data.declaration");
        Assert::assertIsArray($declaration, "Blocco {$locale} senza declaration.");
        $facts[$locale] = array_diff_key($declaration, array_flip(['non_accessible', 'disproportionate_burden', 'out_of_scope']));
    }

    expect($facts['en'])->toBe($facts['it'])->and($facts['de'])->toBe($facts['it'])->and($facts['es'])->toBe($facts['it']);
});
