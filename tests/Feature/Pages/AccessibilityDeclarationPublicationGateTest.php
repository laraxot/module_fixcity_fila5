<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessAccessibilityStatementAction;
use Modules\Fixcity\Actions\GetAccessibilityDeclarationAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-132, guardia di pubblicazione. Legge la dichiarazione VERA del tenant attivo (JSON `accessibilita.index`, chiave `declaration`
 * di ogni lingua) e FALLISCE finche' manca un dato dell'ente o un valore e' incoerente: stato di conformita' (dall'esito della
 * valutazione di accessibilita', DCP-143), metodo e data della valutazione, date di pubblicazione e aggiornamento, casella delle
 * segnalazioni, parti non accessibili se il sito non e' conforme. Rosso per costruzione fino a quando l'ente fornisce i dati: una
 * dichiarazione "conforme" senza valutazione non si pubblica. Esecuzione mirata: PUBLICATION_GATE=1 pest --group=publication-gate (senza la variabile i test risultano SALTATI, con il motivo scritto: non rossi nella suite generale e non nascosti).
 */
it('has a publishable accessibility declaration in the tenant', function (string $locale): void {
    app()->setLocale($locale);

    $issues = app(AssessAccessibilityStatementAction::class)->execute(app(GetAccessibilityDeclarationAction::class)->execute());

    $report = [];
    foreach ($issues as $key => $reason) {
        $report[] = __('fixcity::accessibility.issues.'.$key.'.title').' ('.$reason.')';
    }
    Assert::assertSame([], $issues, "Dichiarazione di accessibilità ({$locale}) NON pubblicabile. Da fornire: ".implode('; ', $report));
})->with(['it', 'en', 'de', 'es'])->group('publication-gate')->skip(
    fn (): bool => ! filter_var(getenv('PUBLICATION_GATE'), FILTER_VALIDATE_BOOLEAN),
    'Blocco di messa in produzione: rosso finche il Comune non fornisce i dati. Eseguire con PUBLICATION_GATE=1 pest --group=publication-gate',
);
