<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match;
use function Safe\preg_match_all;

uses(TestCase::class);

/*
 * DCP-55 difetto 2: il wizard tickets/create in de ed es era quasi tutto in italiano, perche' `lang/{de,es}/ticket_form.php`
 * conteneva solo segnaposto dell'auto-writer (`'label' => 'fixcity::ticket_form.sections.location.heading'`) e `en` copriva 56 chiavi su 143.
 * Il file `it` e' la fonte: ogni sua chiave deve esistere nelle altre lingue, con un testo tradotto e con gli stessi segnaposto.
 */
dataset('languages', ['en', 'de', 'es']);

/**
 * @param  array<mixed>  $tree
 * @return array<string, string>
 */
function ticketFormFlat(array $tree, string $prefix = ''): array
{
    $flat = [];
    foreach ($tree as $key => $value) {
        $path = $prefix.$key;
        is_array($value) ? $flat += ticketFormFlat($value, $path.'.') : $flat[$path] = is_string($value) ? $value : '';
    }

    return $flat;
}

/** @return array<string, string> */
function ticketFormLang(string $locale): array
{
    $tree = require dirname(__DIR__, 2).'/lang/'.$locale.'/ticket_form.php';
    Assert::assertIsArray($tree);

    return ticketFormFlat($tree);
}

/** Segnaposto dell'auto-writer, colori, icone e note per sviluppatori: restano uguali in ogni lingua. */
function ticketFormIsTechnical(string $key, string $value): bool
{
    return $value === '' || preg_match('/^[a-z_\-]+$/', $value) === 1 || str_starts_with($value, 'heroicon') || str_ends_with($key, 'privacyAccepted.description');
}

it('has every Italian key', function (string $locale): void {
    $missing = array_keys(array_diff_key(ticketFormLang('it'), ticketFormLang($locale)));

    Assert::assertSame([], $missing, "Chiavi di ticket_form mancanti in {$locale}.");
})->with('languages');

it('translates every visible text instead of leaving the Italian one', function (string $locale): void {
    // Omografi veri: la parola e' la stessa in italiano e nella lingua di arrivo (motivare qui ogni voce aggiunta).
    $sameWord = ['en' => ['fields.email.label']];
    $translated = ticketFormLang($locale);
    $untranslated = [];
    foreach (ticketFormLang('it') as $key => $value) {
        if (! ticketFormIsTechnical($key, $value) && ! in_array($key, $sameWord[$locale] ?? [], true) && ($translated[$key] ?? $value) === $value) {
            $untranslated[] = $key;
        }
    }

    Assert::assertSame([], $untranslated, "Testi di ticket_form ancora in italiano in {$locale}.");
})->with('languages');

it('contains no auto-writer placeholder pointing at its own key', function (string $locale): void {
    $placeholders = array_keys(array_filter(ticketFormLang($locale), static fn (string $value): bool => str_contains($value, 'fixcity::')));

    Assert::assertSame([], $placeholders, "Segnaposto dell'auto-writer in {$locale}.");
})->with('languages');

it('keeps the :placeholders and the plural syntax of the Italian text', function (string $locale): void {
    $translated = ticketFormLang($locale);
    $pattern = '/:[a-z_]+|\{1\}|\[2,\*\]/';
    foreach (ticketFormLang('it') as $key => $value) {
        preg_match_all($pattern, $value, $expected);
        preg_match_all($pattern, $translated[$key] ?? '', $actual);
        sort($expected[0]);
        sort($actual[0]);
        Assert::assertSame($expected[0], $actual[0], "Segnaposto diversi in {$locale} per {$key}.");
    }
})->with('languages');
