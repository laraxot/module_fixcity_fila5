<?php

declare(strict_types=1);

use Dom\Element;
use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Safe\json_decode;
use function Safe\parse_url;
use function Safe\preg_match;
use function Safe\preg_replace;

uses(TestCase::class);

/*
 * DCP-146 (story 2026-10-13-dcp-146-wizard-passo-dati-mappa-sempre-funzionante): contratto lato server del passo "Dati di segnalazione".
 * Il comportamento su origine HTTP (centratura, clic, "Usa la mia posizione") lo prova Playwright
 * (tests/Playwright/dcp146-step-dati-http.spec.js): qui si prova cio' che il server consegna al browser.
 * Il nome della config e dell'attributo `default-view` e' quello riportato da W2-GEO nella story.
 */
dataset('dcp146 page locales', ['it', 'en', 'de', 'es']);

const DCP146_PAGE_FORBIDDEN = '/https|connessione sicura|secure connection|sichere verbindung|conexi[o\x{00f3}]n segura/iu';
const DCP146_PAGE_RAW_KEY = '/\b[a-z][\w-]*::[\w.-]+|\b(?:ticket_form|ticket_wizard|ticket_review|coordinate-picker|fixcity\.ticket)\.[\w.-]+/i';

function dcp146Number(mixed $value, string $what): float
{
    return is_numeric($value) ? (float) $value : Assert::fail("{$what} assente o non numerico.");
}

function dcp146Text(mixed $value): string
{
    return is_string($value) ? $value : '';
}

/** @return array<mixed> */
function dcp146Config(string $key): array
{
    $value = config($key);

    return is_array($value) ? $value : [];
}

function dcp146Page(TestCase $test, string $locale): HTMLDocument
{
    $response = $test->actingAs(UserFactory::new()->createOne())->get("/{$locale}/tickets/create");
    $response->assertOk();
    $html = $response->getContent();
    Assert::assertIsString($html);

    return HTMLDocument::createFromString($html, LIBXML_NOERROR);
}

/** @return list<string> testo e attributi accessibili del widget del wizard, senza script, stili e template */
function dcp146WidgetTexts(HTMLDocument $document): array
{
    foreach ($document->querySelectorAll('script, style, template, noscript') as $node) {
        $node->remove();
    }
    $widget = $document->querySelector('.cmp-wizard-widget') ?? Assert::fail('.cmp-wizard-widget assente.');
    $texts = [trim(preg_replace('/\s+/u', ' ', $widget->textContent ?? ''))];
    foreach ($widget->querySelectorAll('[aria-label], [title], [placeholder], [alt]') as $element) {
        foreach (['aria-label', 'title', 'placeholder', 'alt'] as $attribute) {
            if ($element->hasAttribute($attribute)) {
                $texts[] = $element->getAttribute($attribute) ?? '';
            }
        }
    }

    return $texts;
}

it('declares a default map view that is never the whole world', function (): void {
    $view = dcp146Config('geo.config.map.default_view');

    Assert::assertNotSame([], $view, 'Manca la config geo.config.map.default_view (lat, lng, zoom): la mappa senza punto finirebbe sul mondo.');
    $lat = dcp146Number($view['lat'] ?? null, 'lat');
    $lng = dcp146Number($view['lng'] ?? null, 'lng');
    $zoom = dcp146Number($view['zoom'] ?? null, 'zoom');
    Assert::assertGreaterThanOrEqual(-90, $lat);
    Assert::assertLessThanOrEqual(90, $lat);
    Assert::assertGreaterThanOrEqual(-180, $lng);
    Assert::assertLessThanOrEqual(180, $lng);
    Assert::assertGreaterThanOrEqual(5, $zoom, 'Zoom di default sotto 5: e\' il mondo intero.');
    Assert::assertLessThanOrEqual(12, $zoom);
});

it('does not send the visitor IP to a third party by default (DCP-146 decision, GDPR art. 6 and 44)', function (): void {
    $approximate = dcp146Config('geo.config.approximate_location');
    $enabled = ($approximate['enabled'] ?? false) === true;
    $url = dcp146Text($approximate['url'] ?? '');
    $host = $url === '' ? null : parse_url($url, PHP_URL_HOST);
    $appHost = parse_url(dcp146Text(config('app.url')), PHP_URL_HOST);

    Assert::assertTrue(! $enabled || $host === null || $host === $appHost, "geo.config.approximate_location e' attiva di default verso {$url}: l'IP del cittadino va a un terzo senza decisione del DPO.");
});

it('ships the map with a default view and non-world zoom, in every language', function (string $locale): void {
    /** @var TestCase $this */
    $document = dcp146Page($this, $locale);
    $picker = $document->querySelector('coordinate-picker-lit') ?? Assert::fail('coordinate-picker-lit assente dalla pagina.');
    Assert::assertInstanceOf(Element::class, $picker);

    $defaultView = json_decode($picker->getAttribute('default-view') ?? '', true);
    Assert::assertIsArray($defaultView, "Attributo default-view assente o non JSON su coordinate-picker-lit ({$locale}).");
    Assert::assertGreaterThanOrEqual(5, dcp146Number($defaultView['zoom'] ?? null, 'default-view.zoom'), 'Vista di default sul mondo intero.');

    $labels = json_decode($picker->getAttribute('labels') ?? '', true);
    Assert::assertIsArray($labels);
    Assert::assertNotSame('', trim(dcp146Text($labels['no_position'] ?? '')), 'Manca l\'invito neutro a indicare il punto.');
    foreach ($labels as $name => $label) {
        $text = dcp146Text($label);
        Assert::assertSame(0, preg_match(DCP146_PAGE_FORBIDDEN, $text), "Etichetta {$name} ({$locale}) nomina HTTPS: {$text}");
    }
})->with('dcp146 page locales');

it('renders the step without HTTPS wording or raw translation keys, with a matching html lang', function (string $locale): void {
    /** @var TestCase $this */
    $document = dcp146Page($this, $locale);
    Assert::assertSame($locale, substr($document->documentElement?->getAttribute('lang') ?? '', 0, 2), "<html lang> non coerente con /{$locale}/.");

    $texts = dcp146WidgetTexts($document);
    $https = array_values(array_filter($texts, static fn (string $text): bool => preg_match(DCP146_PAGE_FORBIDDEN, $text) === 1));
    $raw = array_values(array_filter($texts, static fn (string $text): bool => preg_match(DCP146_PAGE_RAW_KEY, $text) === 1));

    Assert::assertSame([], $https, "Testo su HTTPS o connessione sicura in {$locale}.");
    Assert::assertSame([], $raw, "Chiavi di traduzione grezze visibili in {$locale}.");
})->with('dcp146 page locales');

it('does not block pinch zoom (WCAG 1.4.4)', function (): void {
    /** @var TestCase $this */
    $document = dcp146Page($this, 'it');
    $viewport = $document->querySelector('meta[name="viewport"]')?->getAttribute('content') ?? '';

    Assert::assertNotSame('', $viewport, 'meta viewport assente.');
    Assert::assertSame(0, preg_match('/user-scalable\s*=\s*(no|0)|maximum-scale\s*=\s*1(\.0+)?(?![\d.])/i', $viewport), "meta viewport blocca lo zoom: {$viewport}");
});

it('gives every visible form control of the wizard an accessible name', function (string $locale): void {
    /** @var TestCase $this */
    $document = dcp146Page($this, $locale);
    $unnamed = [];
    foreach ($document->querySelectorAll('.cmp-wizard-widget input:not([type=hidden]):not([type=submit]):not([type=checkbox]), .cmp-wizard-widget select, .cmp-wizard-widget textarea') as $control) {
        $id = $control->getAttribute('id') ?? '';
        $hasLabelFor = $id !== '' && $document->querySelector('label[for="'.addcslashes($id, '"\\').'"]') instanceof Element;
        $named = trim($control->getAttribute('aria-label') ?? '') !== ''
            || trim($control->getAttribute('aria-labelledby') ?? '') !== ''
            || $hasLabelFor
            || $control->closest('label') instanceof Element;
        if (! $named) {
            $unnamed[] = strtolower($control->localName).($id !== '' ? '#'.$id : '');
        }
    }

    Assert::assertSame([], $unnamed, "Campi senza nome accessibile ({$locale}): solo placeholder non basta (WCAG 1.3.1, 4.1.2).");
})->with(['it', 'en']);
