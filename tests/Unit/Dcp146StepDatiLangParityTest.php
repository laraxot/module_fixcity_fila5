<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Finder\Finder;

use function Safe\file_get_contents;
use function Safe\preg_match;

uses(TestCase::class);

/*
 * DCP-146 (story 2026-10-13-dcp-146-wizard-passo-dati-mappa-sempre-funzionante): il passo "Dati di segnalazione" del wizard
 * deve avere le stesse chiavi di `ticket_form.php` in it/en/de/es e nessun testo che nomini HTTPS o la connessione sicura:
 * il GPS e' un miglioramento, mai un requisito, e l'utente su HTTP non puo' risolvere il problema.
 * Le chiavi del passo si derivano dai prefissi sotto: una chiave nuova aggiunta a `it` entra da sola nel confronto.
 */
dataset('dcp146 locales', ['it', 'en', 'de', 'es']);
dataset('dcp146 translated locales', ['en', 'de', 'es']);

const DCP146_STEP_PREFIXES = [
    'fields.location.', 'fields.type.', 'fields.content.', 'fields.images.', 'fields.name.',
    'fields.author.', 'fields.author_fiscal_code.', 'sections.location.', 'sections.issue.', 'sections.author.',
    'steps.dati-di-segnalazione.', 'steps.data.',
];

/** HTTPS e connessione sicura nelle 4 lingue (it, en, de, es). */
const DCP146_FORBIDDEN = '/https|connessione sicura|secure connection|sichere verbindung|conexi[o\x{00f3}]n segura/iu';

/**
 * @param  array<mixed>  $tree
 * @return array<string, string>
 */
function dcp146Flat(array $tree, string $prefix = ''): array
{
    $flat = [];
    foreach ($tree as $key => $value) {
        $path = $prefix.$key;
        if (is_array($value)) {
            $flat += dcp146Flat($value, $path.'.');
        } else {
            $flat[$path] = is_string($value) ? $value : '';
        }
    }

    return $flat;
}

/** @return array<string, string> */
function dcp146Lang(string $module, string $file, string $locale): array
{
    $tree = require dirname(__DIR__, 3).'/'.$module.'/lang/'.$locale.'/'.$file.'.php';
    Assert::assertIsArray($tree);

    return dcp146Flat($tree);
}

/** @return list<string> chiavi del passo, derivate da `it` */
function dcp146StepKeys(string $locale): array
{
    return array_values(array_filter(
        array_keys(dcp146Lang('Fixcity', 'ticket_form', $locale)),
        static fn (string $key): bool => array_any(DCP146_STEP_PREFIXES, static fn (string $prefix): bool => str_starts_with($key, $prefix)),
    ));
}

it('has the same step keys as the Italian source, no more and no fewer', function (string $locale): void {
    $source = dcp146StepKeys('it');
    Assert::assertNotSame([], $source, 'Nessuna chiave del passo trovata in it/ticket_form.php: prefissi da aggiornare.');
    $actual = dcp146StepKeys($locale);

    Assert::assertSame([], array_values(array_diff($source, $actual)), "Chiavi del passo mancanti in {$locale}/ticket_form.php.");
    Assert::assertSame([], array_values(array_diff($actual, $source)), "Chiavi del passo orfane in {$locale}/ticket_form.php (assenti in it).");
})->with('dcp146 translated locales');

it('fills every visible step text in each language, never with the key itself', function (string $locale): void {
    $source = dcp146Lang('Fixcity', 'ticket_form', 'it');
    $translated = dcp146Lang('Fixcity', 'ticket_form', $locale);
    $empty = [];
    foreach (dcp146StepKeys('it') as $key) {
        $value = trim($translated[$key] ?? '');
        // Le `description` dei campi sono note per sviluppatori (a volte uguali al nome del campo): contano solo quelle delle sezioni.
        $isVisibleKey = preg_match('/\.(label|placeholder|helper_text|heading|show_more|invalid)$/', $key) === 1
            || (str_starts_with($key, 'sections.') && str_ends_with($key, '.description'));
        $isVisibleText = $isVisibleKey && trim($source[$key]) !== '';
        $segments = explode('.', $key);
        $isPlaceholderOfItself = $isVisibleKey && ($value === ($segments[count($segments) - 2] ?? '') || str_contains($value, 'fixcity::'));
        if (($isVisibleText && $value === '') || $isPlaceholderOfItself) {
            $empty[] = $key;
        }
    }

    Assert::assertSame([], $empty, "Testi del passo vuoti o uguali alla propria chiave in {$locale}.");
})->with('dcp146 locales');

it('never names HTTPS or a secure connection in the wizard and map texts', function (string $locale): void {
    $offending = [];
    foreach ([['Fixcity', 'ticket_form'], ['Fixcity', 'ticket_wizard'], ['Geo', 'coordinate-picker']] as [$module, $file]) {
        foreach (dcp146Lang($module, $file, $locale) as $key => $value) {
            if (preg_match(DCP146_FORBIDDEN, $value) === 1) {
                $offending[] = "{$module}/{$file}.{$key}: {$value}";
            }
        }
    }

    Assert::assertSame([], $offending, "Testi con HTTPS o connessione sicura in {$locale}.");
})->with('dcp146 locales');

it('ships no source that shows an insecure-context message to the citizen', function (): void {
    $root = dirname(__DIR__, 4);
    $finder = (new Finder)->files()->ignoreUnreadableDirs()
        ->in([$root.'/Modules/Fixcity/resources/views', $root.'/Modules/Geo/resources/views', $root.'/Modules/Geo/resources/js/components/map', $root.'/Themes/Sixteen/resources/views/components/wizard'])
        ->name(['*.blade.php', '*.js'])
        ->notName(['timezone-centers.js', '*.bak*']);
    $offending = [];
    foreach ($finder as $file) {
        $source = file_get_contents($file->getPathname());
        if (preg_match('/geo(location)?_insecure|texts\.insecure|Apri questa pagina in HTTPS|connessione sicura \(HTTPS\)/i', $source) === 1) {
            $offending[] = $file->getRelativePathname();
        }
    }

    Assert::assertSame([], $offending, 'Sorgenti che mostrano ancora il messaggio su HTTPS.');
});
