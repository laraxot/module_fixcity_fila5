<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;
use function Safe\preg_replace;

uses(TestCase::class);

/**
 * DCP-33: la vista del tema `pub_theme::components.wizard.summary-step` riceve l'array di
 * BuildTicketReviewSummaryAction (`review`) e il guscio JSON-shaped (`data`: titolo, briciole, passi, avviso, pulsanti)
 * e deve emettere il markup di sito/segnalazione-03-riepilogo.html senza testi propri.
 */
const SUMMARY_STEP_VIEW = 'pub_theme::components.wizard.summary-step';

/**
 * @param  array<mixed>  $replace  Sostituzioni ricorsive sui dati del riferimento (Action)
 * @return array<mixed>
 */
function summaryStepReview(array $replace = []): array
{
    return array_replace_recursive([
        'report' => [
            'title' => 'Segnalazione',
            'card_title' => 'Disservizio',
            'edit_label' => 'Modifica',
            'edit_step' => 'form.dati-di-segnalazione::data::wizard-step',
            'rows' => [
                ['label' => 'Indirizzo', 'value' => 'Via Solferino - 50100 Firenze (FI)'],
                ['label' => 'Tipo di disservizio', 'value' => 'Danneggiamento proprietà pubblica'],
                ['label' => 'Titolo', 'value' => 'Panchina danneggiata'],
                ['label' => 'Dettagli', 'value' => 'La seduta della panchina risulta inutilizzabile e pericolosa dato che ci sono molte schegge e parti appuntite'],
                ['label' => 'Immagini', 'value' => '6yhakandsahm413d8da.jpg'],
            ],
            'images' => ['6yhakandsahm413d8da.jpg'],
            'location' => ['latitude' => 43.7696, 'longitude' => 11.2558, 'address' => 'Via Solferino - 50100 Firenze (FI)'],
            'map_label' => 'Posizione del disservizio sulla mappa',
        ],
        'author' => [
            'group_title' => 'Dati Generali',
            'card_title' => 'Autore della segnalazione',
            'name' => 'Giulia Bianchi',
            // Il riferimento ha il codice fiscale; l'Action non lo produce mai (nessuna sorgente), la vista lo mostra se c'e'.
            'rows' => [['label' => 'Codice Fiscale', 'value' => 'GLABNC72H25H501Y']],
        ],
        'contacts' => [
            'title' => 'Contatti',
            'rows' => [
                ['label' => 'Telefono', 'value' => '+39 331 1234567'],
                ['label' => 'Email', 'value' => 'giulia.bianchi@gmail.com'],
            ],
        ],
    ], $replace);
}

/**
 * @param  array<mixed>  $replace  Sostituzioni ricorsive sul guscio (stesse chiavi del JSON di tests.segnalazione-03-riepilogo)
 * @return array<mixed>
 */
function summaryStepShell(array $replace = []): array
{
    return array_replace_recursive([
        'title' => 'Segnalazione disservizio',
        'breadcrumb' => [
            'items' => [['label' => 'Home', 'url' => '/it'], ['label' => 'Servizi', 'url' => '#']],
            'current' => 'Segnalazione disservizio',
        ],
        'steps' => [
            'items' => [
                ['label' => 'Autorizzazioni e condizioni', 'state' => 'confirmed'],
                ['label' => 'Dati di segnalazione', 'state' => 'confirmed'],
                ['label' => 'Riepilogo', 'state' => 'active'],
            ],
            'index' => '3/3',
            'confirmed_label' => 'Confermato',
            'active_label' => 'Attivo',
        ],
        'callout' => [
            'variant' => 'warning',
            'icon' => 'it-horn',
            'title' => 'Attenzione',
            'text' => 'Le informazioni che hai fornito hanno valore di dichiarazione.',
            'emphasis' => 'Verifica che siano corrette.',
        ],
        'nav' => [
            'aria_label' => 'Step',
            'back' => 'Indietro',
            'save' => 'Salva Richiesta',
            'save_short' => 'Salva',
            'next' => 'Invia',
            'saved_message' => 'Richiesta salvata con successo',
            'next_class' => 'send',
            'next_target' => '#modal-terms',
            'next_icon' => false,
        ],
    ], $replace);
}

/**
 * @param  array<mixed>  $review
 * @param  array<mixed>  $data
 * @param  array<string, mixed>  $flags
 */
function renderSummaryStep(array $review, array $data, array $flags = []): string
{
    return trim(view(SUMMARY_STEP_VIEW, ['review' => $review, 'data' => $data, ...$flags])->render());
}

describe('wizard summary-step view', function (): void {
    test('emits the markup of segnalazione-03-riepilogo', function (): void {
        $html = renderSummaryStep(summaryStepReview(), summaryStepShell());

        Assert::assertSame(1, substr_count($html, 'id="main-container"'));
        Assert::assertSame(1, substr_count($html, 'class="steppers"'));
        preg_match_all('/<li class="(confirmed|active)">/', $html, $steps);
        Assert::assertSame(['confirmed', 'confirmed', 'active'], $steps[1]);
        Assert::assertStringContainsString('class="callout callout-highlight ps-3 warning"', $html);
        Assert::assertSame(2, substr_count($html, 'class="title-xxlarge mb-4 mt-40"'));
        Assert::assertSame(2, substr_count($html, 'class="cmp-card mb-4"'));
        Assert::assertSame(3, substr_count($html, 'class="cmp-info-summary bg-white'));
        Assert::assertSame(8, substr_count($html, 'class="single-line-info border-light"'));
        Assert::assertStringContainsString('class="cmp-nav-steps"', $html);
        Assert::assertStringContainsString('steppers-btn-confirm send', $html);
        Assert::assertSame(1, substr_count($html, '<h1 '));
        Assert::assertStringContainsString('Modifica', $html);
    });

    test('prints every value of the review and nothing from the framework', function (): void {
        $html = renderSummaryStep(summaryStepReview(), summaryStepShell());

        foreach (['Via Solferino - 50100 Firenze (FI)', 'Danneggiamento proprietà pubblica', 'Panchina danneggiata', '6yhakandsahm413d8da.jpg', 'Giulia Bianchi', '+39 331 1234567', 'giulia.bianchi@gmail.com'] as $value) {
            Assert::assertStringContainsString($value, $html);
        }
        Assert::assertDoesNotMatchRegularExpression('/\bfi-[a-z]/', $html);
        Assert::assertStringNotContainsString('<form', $html);
        Assert::assertDoesNotMatchRegularExpression('/wire:(model|click|submit|id)/', $html);
    });

    test('escapes the values coming from the form state', function (): void {
        $html = renderSummaryStep(summaryStepReview(['report' => ['rows' => [2 => ['value' => '<script>alert(1)</script>']]]]), summaryStepShell());

        Assert::assertStringNotContainsString('<script>alert(1)</script>', $html);
        Assert::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    });

    test('has no text of its own', function (): void {
        $english = summaryStepReview();
        $english['report'] = ['title' => 'Report', 'card_title' => 'Issue', 'edit_label' => 'Edit', 'edit_step' => 'form.report-details::data::wizard-step', 'rows' => [['label' => 'Address', 'value' => 'Main Street 1']], 'images' => [], 'location' => null, 'map_label' => ''];
        $english['author'] = ['group_title' => 'General data', 'card_title' => 'Report author', 'name' => 'Jane Doe', 'rows' => []];
        $english['contacts'] = ['title' => 'Contact details', 'rows' => [['label' => 'Email', 'value' => 'jane@example.test']]];
        $shell = ['title' => 'Report an issue', 'breadcrumb' => ['items' => [], 'current' => 'Report an issue'], 'steps' => ['items' => [['label' => 'Summary', 'state' => 'active']], 'index' => '1/1', 'active_label' => 'Active'], 'nav' => ['aria_label' => 'Steps', 'back' => 'Back', 'next' => 'Send', 'next_icon' => false]];

        $html = renderSummaryStep($english, $shell);

        foreach (['Indietro', 'Modifica', 'Attenzione', 'Segnalazione', 'Riepilogo', 'Invia', 'Contatti', 'Telefono', 'Salva', 'Dati Generali', 'Disservizio'] as $italian) {
            Assert::assertStringNotContainsString($italian, $html);
        }
        foreach (['Report an issue', 'Edit', 'General data', 'Report author', 'Contact details', 'Back', 'Send'] as $english) {
            Assert::assertStringContainsString($english, $html);
        }
    });

    test('hides author and contacts for a guest', function (): void {
        $review = summaryStepReview();
        $review['author'] = null;
        $review['contacts'] = null;

        $html = renderSummaryStep($review, summaryStepShell());

        Assert::assertSame(1, substr_count($html, 'class="cmp-card mb-4"'));
        Assert::assertSame(1, substr_count($html, 'class="cmp-info-summary bg-white'));
        Assert::assertStringNotContainsString('Dati Generali', $html);
        Assert::assertStringNotContainsString('Giulia Bianchi', $html);
    });

    test('shows the author without a contacts box when there are no contacts', function (): void {
        $review = summaryStepReview();
        $review['contacts'] = null;

        $html = renderSummaryStep($review, summaryStepShell());

        Assert::assertSame(2, substr_count($html, 'class="cmp-info-summary bg-white'));
        Assert::assertStringContainsString('Giulia Bianchi', $html);
        Assert::assertStringNotContainsString('Contatti', $html);
    });

    test('lets the caller leave out the shell and the navigation', function (): void {
        $html = renderSummaryStep(summaryStepReview(), summaryStepShell(), ['withShell' => false, 'withNav' => false]);

        foreach (['cmp-breadcrumbs', '<h1', 'class="steppers"', 'cmp-nav-steps'] as $part) {
            Assert::assertStringNotContainsString($part, $html);
        }
        Assert::assertStringContainsString('class="callout callout-highlight', $html);
        Assert::assertSame(8, substr_count($html, 'class="single-line-info border-light"'));
    });

    test('lets the wizard shell own the container', function (): void {
        $html = renderSummaryStep(summaryStepReview(), summaryStepShell(), ['withContainer' => false, 'withShell' => false]);

        Assert::assertStringNotContainsString('id="main-container"', $html);
        Assert::assertStringStartsWith('<div class="row justify-content-center">', $html);
        Assert::assertStringContainsString('col-12 col-lg-8', $html);
        Assert::assertSame(8, substr_count($html, 'class="single-line-info border-light"'));
    });

    test('omits the save button when the data has no save label', function (): void {
        $html = renderSummaryStep(summaryStepReview(), summaryStepShell(['nav' => ['save' => null, 'save_short' => null]]));

        Assert::assertStringNotContainsString('steppers-btn-save', $html);
        Assert::assertStringContainsString('steppers-btn-confirm send', $html);
    });

    test('wires back and edit to the wizard only when asked', function (): void {
        $plain = renderSummaryStep(summaryStepReview(), summaryStepShell());
        $wired = renderSummaryStep(summaryStepReview(), summaryStepShell(), ['wired' => true]);

        Assert::assertStringNotContainsString('x-on:click', $plain);
        Assert::assertStringContainsString('class="btn btn-sm steppers-btn-prev p-0" x-on:click="goToPreviousStep"', $wired);
        Assert::assertStringContainsString('x-on:click.prevent="goToStep(&#039;form.dati-di-segnalazione::data::wizard-step&#039;)"', $wired);
        Assert::assertStringContainsString('href="?step=form.dati-di-segnalazione%3A%3Adata%3A%3Awizard-step"', $wired);
    });

    test('puts the map in the report card, hideable by gates, after the last row', function (): void {
        $html = renderSummaryStep(summaryStepReview(), summaryStepShell());

        Assert::assertSame(1, substr_count($html, 'data-dc-map'));
        Assert::assertStringContainsString('aria-label="Posizione del disservizio sulla mappa: Via Solferino - 50100 Firenze (FI)"', $html);
        Assert::assertStringContainsString('<coordinate-picker-lit', $html);
        Assert::assertMatchesRegularExpression('/<coordinate-picker-lit\s+readonly/', $html);
        $lastReportRow = strpos($html, '6yhakandsahm413d8da.jpg');
        $map = strpos($html, 'data-dc-map');
        $nextGroup = strpos($html, 'Dati Generali');
        Assert::assertIsInt($lastReportRow);
        Assert::assertIsInt($map);
        Assert::assertIsInt($nextGroup);
        Assert::assertGreaterThan($lastReportRow, $map);
        Assert::assertLessThan($nextGroup, $map);
    });

    test('draws no map without coordinates', function (): void {
        $html = renderSummaryStep(summaryStepReview(['report' => ['location' => null]]), summaryStepShell());

        Assert::assertStringNotContainsString('data-dc-map', $html);
    });

    test('hands latitude, longitude and address to the read-only map', function (): void {
        $html = renderSummaryStep(summaryStepReview(), summaryStepShell());

        Assert::assertStringContainsString('&quot;latitude&quot;:43.7696,&quot;longitude&quot;:11.2558,&quot;address&quot;:&quot;Via Solferino - 50100 Firenze (FI)&quot;', $html);
        Assert::assertStringContainsString('https://www.openstreetmap.org/?mlat=43.769600&amp;mlon=11.255800', $html);
    });

    test('renders the whole main of the reference when asked (harness)', function (): void {
        $out = (string) getenv('DCP33_RENDER_OUT');
        $contacts = [
            'layout' => 'offset',
            'main' => ['title' => 'Contatta il comune', 'items' => [
                ['icon' => 'it-help-circle', 'label' => 'Leggi le domande frequenti', 'url' => '#'],
                ['icon' => 'it-mail', 'label' => 'Richiedi assistenza', 'url' => '#', 'element' => 'contacts'],
                ['icon' => 'it-hearing', 'label' => 'Chiama il numero verde 05 0505', 'url' => '#'],
                ['icon' => 'it-calendar', 'label' => 'Prenota appuntamento', 'url' => '#', 'element' => 'appointment-booking'],
            ]],
        ];
        $modal = ['id' => 'modal-terms', 'title' => 'Termini e condizioni', 'info' => 'Cliccando su Conferma e invia confermi di aver preso visione dei termini e delle condizioni di servizio.', 'link' => 'Leggi termini e condizioni', 'link_url' => '#', 'confirm' => 'Conferma e invia', 'cancel' => 'Annulla', 'close_label' => 'Chiudi finestra modale'];
        $data = [...summaryStepShell(), 'contacts' => $contacts, 'modal' => $modal];

        $main = Blade::render(<<<'BLADE'
            @php
                $d = static fn (string $key, mixed $default = '') => data_get($data, $key, $default);
                $assets = asset('themes/Sixteen/design-comuni/assets');
                $icon = static fn (string $name): string => $assets.'/bootstrap-italia/dist/svg/sprites.svg#'.$name;
            @endphp
            <main>
            @include('pub_theme::components.wizard.summary-step', ['review' => $review, 'data' => $data])
            @include('pub_theme::components.blocks.report-partials.contacts')
            @include('pub_theme::components.blocks.report-step.modal-terms')
            </main>
            BLADE, ['review' => summaryStepReview(), 'data' => $data]);

        File::put($out, $main);
        Assert::assertStringContainsString('</main>', $main);
    })->skip(fn (): bool => ! is_string(getenv('DCP33_RENDER_OUT')) || getenv('DCP33_RENDER_OUT') === '', 'solo con DCP33_RENDER_OUT=<file>');
});

describe('wizard summary-step view: photo previews (DCP-182)', function (): void {
    /** @return array<mixed> */
    function previewsReview(): array
    {
        return summaryStepReview(['report' => [
            'images' => ['panchina.jpg', 'buca.png'],
            'image_previews' => ['http://dev.test/livewire/preview-file/aaa.jpg?signature=1', 'http://dev.test/livewire/preview-file/bbb.png?signature=2'],
            'image_alts' => ['Anteprima della foto panchina.jpg', 'Anteprima della foto buca.png'],
            'images_label' => 'Foto allegate',
        ]]);
    }

    test('shows one thumbnail per preview with its alternative text, inside the report card', function (): void {
        $html = renderSummaryStep(previewsReview(), summaryStepShell());

        Assert::assertSame(1, substr_count($html, 'data-element="review-attachments"'));
        Assert::assertSame(2, substr_count($html, '<img class="img-fluid rounded border"'));
        Assert::assertStringContainsString('src="http://dev.test/livewire/preview-file/aaa.jpg?signature=1" alt="Anteprima della foto panchina.jpg"', $html);
        Assert::assertStringContainsString('src="http://dev.test/livewire/preview-file/bbb.png?signature=2" alt="Anteprima della foto buca.png"', $html);
        Assert::assertStringContainsString('aria-label="Foto allegate"', $html);
        $firstThumb = strpos($html, 'data-element="review-attachments"');
        $map = strpos($html, 'data-dc-map');
        $author = strpos($html, 'Dati Generali');
        Assert::assertIsInt($firstThumb);
        Assert::assertIsInt($map);
        Assert::assertIsInt($author);
        Assert::assertLessThan($map, $firstThumb);
        Assert::assertLessThan($author, $firstThumb);
    });

    test('adds no markup at all when there is no preview, so the reference parity does not change', function (): void {
        $without = renderSummaryStep(summaryStepReview(), summaryStepShell());
        $empty = renderSummaryStep(summaryStepReview(['report' => ['image_previews' => [], 'image_alts' => []]]), summaryStepShell());

        // l'id della mappa e' casuale a ogni resa: si normalizza, il resto dell'HTML deve essere identico
        $normalize = static fn (string $html): string => preg_replace('/geo-point-map-[a-z0-9]{8}/', 'geo-point-map-X', $html);

        Assert::assertStringNotContainsString('review-attachments', $without);
        Assert::assertSame($normalize($without), $normalize($empty));
    });

    test('escapes hostile text in the alternative text and the url', function (): void {
        $review = summaryStepReview(['report' => [
            'image_previews' => ['http://dev.test/p/x.png?a=1&b="><script>alert(1)</script>'],
            'image_alts' => ['Anteprima della foto <script>alert(2)</script>.png'],
            'images_label' => 'Foto "allegate"',
        ]]);

        $html = renderSummaryStep($review, summaryStepShell());

        Assert::assertStringNotContainsString('<script>alert', $html);
        Assert::assertStringContainsString('&lt;script&gt;alert(2)&lt;/script&gt;', $html);
        Assert::assertStringContainsString('aria-label="Foto &quot;allegate&quot;"', $html);
    });

    test('has no text of its own: the label and the alternative text come from the review data, in every language', function (string $label, string $alt): void {
        $review = summaryStepReview(['report' => [
            'image_previews' => ['http://dev.test/p/aaa.jpg?signature=1'],
            'image_alts' => [$alt],
            'images_label' => $label,
        ]]);

        $html = renderSummaryStep($review, summaryStepShell());

        Assert::assertStringContainsString('aria-label="'.$label.'"', $html);
        Assert::assertStringContainsString('alt="'.$alt.'"', $html);
        foreach (['Foto allegate', 'Anteprima della foto'] as $italian) {
            if (! str_contains($label.$alt, $italian)) {
                Assert::assertStringNotContainsString($italian, $html);
            }
        }
    })->with([
        'it' => ['Foto allegate', 'Anteprima della foto a.jpg'],
        'en' => ['Attached photos', 'Preview of photo a.jpg'],
        'de' => ['Angehängte Fotos', 'Vorschau des Fotos a.jpg'],
        'es' => ['Fotos adjuntas', 'Vista previa de la foto a.jpg'],
    ]);
});
