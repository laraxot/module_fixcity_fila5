<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessAccessibilityStatementAction;
use Modules\Fixcity\Actions\GetAccessibilityDeclarationAction;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/*
 * DCP-132: /{lingua}/accessibilita e' la pagina JSON `accessibilita.index` (blocco accessibility-statement). Risponde sempre 200 con
 * <h1>, feedback e procedura; senza i dati dell'ente mostra un avviso che li elenca. Valori inventati SOLO per il test.
 */
dataset('accessibility locales', ['it', 'en', 'de', 'es']);

/**
 * @return array<string, mixed>
 */
function declarationForPage(): array
{
    return [
        'status' => 'partially_compliant',
        'evaluation' => ['method' => 'third_party', 'date' => '2026-09-01'],
        'published_at' => '2026-09-15',
        'updated_at' => '2026-09-20',
        'feedback_email' => 'segnalazioni@ente.test',
        'procedure_url' => 'https://www.agid.gov.it/it/design-servizi/accessibilita/procedura-attuazione',
        'non_accessible' => [['content' => 'Mappa interattiva', 'reason' => 'Alternativa da tastiera in sviluppo']],
        'disproportionate_burden' => [],
        'out_of_scope' => [['content' => 'Allegati PDF anteriori al 2018', 'reason' => 'Non soggetti alla legge']],
    ];
}

it('serves the page with heading, feedback and procedure and no example values', function (string $locale): void {
    $response = $this->get("/{$locale}/accessibilita")->assertOk();
    $html = (string) $response->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('data-element="accessibility-feedback"')
        ->and($html)->toContain('href="#feedback"')
        ->and($html)->toContain('https://www.agid.gov.it/it/design-servizi/accessibilita/procedura-attuazione')
        ->and($html)->not->toContain('example.it')
        ->and($html)->not->toContain('xxxxxxxx');
})->with('accessibility locales');

it('says what the municipality still has to provide while the tenant declaration is empty', function (string $locale): void {
    app()->setLocale($locale);
    $response = $this->get("/{$locale}/accessibilita")->assertOk()->assertSee('data-element="accessibility-incomplete"', false);

    foreach (['status', 'evaluation', 'published_at', 'updated_at', 'feedback_email'] as $key) {
        $response->assertSee('<strong>'.e((string) __('fixcity::accessibility.issues.'.$key.'.title')).'</strong>', false);
    }
    $response->assertSee(e((string) __('pub_theme::accessibility.status.pending')), false);
})->with('accessibility locales');

it('shows status, dates, parts, mailbox and procedure when the municipality provided them', function (): void {
    app()->setLocale('it');
    app()->instance(GetAccessibilityDeclarationAction::class, new class extends GetAccessibilityDeclarationAction
    {
        public function execute(): array
        {
            return declarationForPage();
        }
    });

    $html = view('pub_theme::components.blocks.accessibility-statement.design-comuni', ['data' => ['declaration' => declarationForPage()]])->render();

    expect(app(AssessAccessibilityStatementAction::class)->execute(declarationForPage()))->toBe([])
        ->and($html)->not->toContain('accessibility-incomplete')
        ->and($html)->toContain((string) __('pub_theme::accessibility.status.partially_compliant'))
        ->and($html)->toContain('Mappa interattiva')
        ->and($html)->toContain('Alternativa da tastiera in sviluppo')
        ->and($html)->toContain('Allegati PDF anteriori al 2018')
        ->and($html)->toContain('mailto:segnalazioni@ente.test')
        ->and($html)->toContain((string) __('pub_theme::accessibility.drafting.methods.third_party'))
        ->and($html)->toContain('15 settembre 2026')
        ->and($html)->toContain('20 settembre 2026')
        ->and($html)->toContain('<textarea')
        ->and($html)->toContain('https://www.agid.gov.it/it/design-servizi/accessibilita/procedura-attuazione');
});
