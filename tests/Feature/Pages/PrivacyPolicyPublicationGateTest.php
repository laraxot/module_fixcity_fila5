<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessPrivacyPolicyAction;
use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-130, guardia di pubblicazione. Legge il testo VERO del tenant attivo (`config/<tenant>/lang/<lingua>/policy.md`) e FALLISCE
 * finche' contiene segnaposto o manca una sezione dell'art. 13 GDPR. E' rosso per costruzione fino a quando il Comune (titolare, DPO)
 * non fornisce il testo: senza informativa completa il servizio non va in produzione. Modello da compilare:
 * Modules/Fixcity/resources/templates/privacy-policy.<lingua>.md. Esecuzione mirata: PUBLICATION_GATE=1 pest --group=publication-gate (senza la variabile i test risultano SALTATI, con il motivo scritto: non rossi nella suite generale e non nascosti).
 */
it('has a publishable privacy policy in the tenant', function (string $locale): void {
    app()->setLocale($locale);

    $issues = app(AssessPrivacyPolicyAction::class)->execute(app(GetPublishedLegalDocumentAction::class)->execute('policy.md'));

    $report = [];
    foreach ($issues as $section => $reason) {
        $report[] = __('fixcity::privacy.sections.'.$section.'.title').' ('.$reason.')';
    }
    Assert::assertSame([], $issues, "Informativa privacy ({$locale}) NON pubblicabile. Da fornire: ".implode('; ', $report));
})->with(['it', 'en', 'de', 'es'])->group('publication-gate')->skip(
    fn (): bool => ! filter_var(getenv('PUBLICATION_GATE'), FILTER_VALIDATE_BOOLEAN),
    'Blocco di messa in produzione: rosso finche il Comune non fornisce i dati. Eseguire con PUBLICATION_GATE=1 pest --group=publication-gate',
);
