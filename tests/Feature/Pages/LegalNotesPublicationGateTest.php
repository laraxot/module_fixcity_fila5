<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessPrivacyPolicyAction;
use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-162, guardia di pubblicazione. Legge il testo VERO del tenant attivo (`config/<tenant>/lang/<lingua>/legal-notes.md`) e FALLISCE finche'
 * manca una sezione del modello o resta un segnaposto: e' rossa per costruzione fino a quando l'ente non fornisce il testo rivisto dal suo
 * legale. Modello da compilare: Modules/Fixcity/resources/templates/legal-notes.<lingua>.md. Esecuzione mirata:
 * PUBLICATION_GATE=1 pest --group=publication-gate (senza la variabile i test risultano SALTATI, con il motivo scritto: non sono verdi).
 */
it('has publishable legal notes in the tenant', function (string $locale): void {
    app()->setLocale($locale);

    $issues = app(AssessPrivacyPolicyAction::class)->execute(
        app(GetPublishedLegalDocumentAction::class)->execute('legal-notes.md'),
        AssessPrivacyPolicyAction::DOCUMENT_LEGAL_NOTES,
    );

    $report = [];
    foreach ($issues as $section => $reason) {
        $report[] = ($section === 'document' ? 'documento' : __('fixcity::legal_notes.sections.'.$section.'.title')).' ('.$reason.')';
    }
    Assert::assertSame([], $issues, "Note legali ({$locale}) NON pubblicabili. Da fornire: ".implode('; ', $report));
})->with(['it', 'en', 'de', 'es'])->group('publication-gate')->skip(
    fn (): bool => ! filter_var(getenv('PUBLICATION_GATE'), FILTER_VALIDATE_BOOLEAN),
    'Blocco di messa in produzione: rosso finche l\'ente non fornisce il testo rivisto. Eseguire con PUBLICATION_GATE=1 pest --group=publication-gate',
);
