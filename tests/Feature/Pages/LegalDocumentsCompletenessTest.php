<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessPrivacyPolicyAction;
use Modules\Fixcity\Actions\BuildLegalDocumentAction;
use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/*
 * Testi legali VERI del tenant (`config/<tenant>/lang/<lingua>/{policy,legal-notes,media-policy,terms}.md`). Il testo e' completo; i soli dati
 * che mancano sono quelli dell'ente (titolare, DPO, recapiti), presi da `config('ente')` con i segnaposto `{{ente.<campo>}}`.
 * Con `config('ente')` vuoto restano da completare solo le sezioni che dipendono dall'ente; con l'ente compilato nessuna.
 */
dataset('legal locales', ['it', 'en', 'de', 'es']);

/**
 * @return array<string, string>
 */
function completeEnteConfig(): array
{
    return [
        'name' => 'Comune di Prova',
        'address' => 'Piazza del Municipio 1, 00100 Prova (PR)',
        'pec' => 'protocollo@pec.comune.prova.it',
        'email' => 'urp@comune.prova.it',
        'phone' => '+39 06 1234567',
        'tax_code' => '00000000000',
        'vat' => '00000000000',
        'dpo_name' => 'Studio DPO di Prova',
        'dpo_email' => 'dpo@comune.prova.it',
    ];
}

/**
 * @return array<string, string>
 */
function assessTenantDocument(string $file, string $document): array
{
    return app(AssessPrivacyPolicyAction::class)->execute(app(GetPublishedLegalDocumentAction::class)->execute($file), $document);
}

it('leaves only the ente-dependent privacy sections incomplete while config(ente) is empty', function (string $locale): void {
    config(['ente' => []]);
    app()->setLocale($locale);

    expect(assessTenantDocument('policy.md', AssessPrivacyPolicyAction::DOCUMENT_PRIVACY))
        ->toBe(['controller' => AssessPrivacyPolicyAction::PLACEHOLDER, 'dpo' => AssessPrivacyPolicyAction::PLACEHOLDER]);
})->with('legal locales');

it('leaves only the ente-dependent legal notes sections incomplete while config(ente) is empty', function (string $locale): void {
    config(['ente' => []]);
    app()->setLocale($locale);

    expect(assessTenantDocument('legal-notes.md', AssessPrivacyPolicyAction::DOCUMENT_LEGAL_NOTES))
        ->toBe(['owner' => AssessPrivacyPolicyAction::PLACEHOLDER, 'contacts' => AssessPrivacyPolicyAction::PLACEHOLDER]);
})->with('legal locales');

it('has complete privacy notice and legal notes once config(ente) is filled', function (string $locale): void {
    config(['ente' => completeEnteConfig()]);
    app()->setLocale($locale);

    expect(assessTenantDocument('policy.md', AssessPrivacyPolicyAction::DOCUMENT_PRIVACY))->toBe([])
        ->and(assessTenantDocument('legal-notes.md', AssessPrivacyPolicyAction::DOCUMENT_LEGAL_NOTES))->toBe([]);

    $privacy = app(BuildLegalDocumentAction::class)->execute('policy.md', 'gdpr-art13');
    expect($privacy['issues'])->toBe([])
        ->and($privacy['html'])->toContain('Comune di Prova')
        ->toContain('href="mailto:dpo@comune.prova.it"')
        ->not->toContain('{{')
        ->not->toContain('[[');
})->with('legal locales');

it('publishes media policy and terms with no placeholder in every locale', function (string $locale, string $file): void {
    config(['ente' => []]);
    app()->setLocale($locale);

    $markdown = app(GetPublishedLegalDocumentAction::class)->execute($file);

    expect($markdown)->toBeString()
        ->and(assessTenantDocument($file, AssessPrivacyPolicyAction::DOCUMENT_PRIVACY))->not->toHaveKey('document')
        ->and((string) $markdown)->not->toContain('[[')
        ->not->toContain('{{')
        ->not->toMatch('/\b(?:TODO|TBD|FIXME)\b|example\.(?:com|org|it)|lorem ipsum/i');
})->with('legal locales')->with(['media-policy.md', 'terms.md']);

it('links the tenant legal documents with locale-absolute paths, never relative to the page that shows them', function (string $locale, string $file): void {
    config(['ente' => []]);
    app()->setLocale($locale);

    $markdown = (string) app(GetPublishedLegalDocumentAction::class)->execute($file);
    preg_match_all('/\]\(([^)\s]+)\)/', $markdown, $matches);

    // La stessa informativa e' mostrata in /<lingua>/privacy e nel passo privacy del wizard (/<lingua>/tickets/create): un link relativo
    // (`cookie-policy`) nel secondo si risolveva in /<lingua>/tickets/cookie-policy (404).
    foreach ($matches[1] as $target) {
        expect($target)->toMatch('#^(?:https?://|mailto:|/'.$locale.'/)#');
    }
})->with('legal locales')->with(['policy.md']);
