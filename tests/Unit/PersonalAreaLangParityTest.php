<?php

declare(strict_types=1);

use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;

uses(TestCase::class);

/*
 * Parita' delle traduzioni di fixcity::personal_area (DCP-110): il fallback del sito e' l'italiano, quindi una chiave mancante in
 * en, de o es NON da' errore ma mostra italiano in una pagina inglese, tedesca o spagnola. Qui la mancanza fa fallire il test.
 * Solo filesystem, nessun database.
 */
$moduleLang = dirname(__DIR__, 2).'/lang';

/**
 * @return array<array-key, mixed>
 */
function personalAreaLang(string $path): array
{
    $lang = require $path;
    Assert::assertIsArray($lang, $path.' must return an array.');

    return $lang;
}

/**
 * @param  array<array-key, mixed>  $tree
 * @return array<string, string>
 */
function flattenPersonalAreaLang(array $tree, string $prefix = ''): array
{
    $flat = [];
    foreach ($tree as $key => $value) {
        $path = $prefix.$key;
        if (is_array($value)) {
            $flat += flattenPersonalAreaLang($value, $path.'.');
        } else {
            Assert::assertIsString($value, $path.' must be a string.');
            $flat[$path] = $value;
        }
    }

    return $flat;
}

it('has the same keys, non-empty values and placeholders in it, en, de and es', function () use ($moduleLang): void {
    $italian = flattenPersonalAreaLang(personalAreaLang($moduleLang.'/it/personal_area.php'));
    Assert::assertNotSame([], $italian);

    foreach (['en', 'de', 'es'] as $locale) {
        $translated = flattenPersonalAreaLang(personalAreaLang($moduleLang.'/'.$locale.'/personal_area.php'));

        Assert::assertSame([], array_values(array_diff(array_keys($italian), array_keys($translated))), $locale.': keys missing');
        Assert::assertSame([], array_values(array_diff(array_keys($translated), array_keys($italian))), $locale.': keys not in it');
        foreach ($translated as $key => $value) {
            Assert::assertNotSame('', trim($value), $locale.' '.$key.': empty');
            preg_match_all('/:[a-z_]+/', $italian[$key], $expected);
            preg_match_all('/:[a-z_]+/', $value, $found);
            Assert::assertEqualsCanonicalizing($expected[0], $found[0], $locale.' '.$key.': placeholders differ from it');
        }
    }
});

// DCP-287 (FO-2-32): l'area personale non ha piu' un proprio elenco di etichette di stato, usa quello dell'enum; qui si prova che
// l'elenco dell'enum copra ogni stato in ogni lingua e che il vecchio elenco non torni.
it('translates every ticket workflow status in every served language from the enum list only', function () use ($moduleLang): void {
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        Assert::assertArrayNotHasKey('status', personalAreaLang($moduleLang.'/'.$locale.'/personal_area.php'), $locale.': second status list');

        $enumLang = personalAreaLang($moduleLang.'/'.$locale.'/ticket_status_enum.php');
        $values = $enumLang['values'] ?? null;
        Assert::assertIsArray($values, $locale.': values');
        foreach (TicketStatusEnum::cases() as $status) {
            $entry = $values[$status->value] ?? null;
            Assert::assertIsArray($entry, $locale.': no entry for status '.$status->value);
            $label = $entry['label'] ?? null;
            Assert::assertTrue(is_string($label) && trim($label) !== '', $locale.': no label for status '.$status->value);
        }
        Assert::assertCount(count(TicketStatusEnum::cases()), $values, $locale.': entries for statuses that do not exist');
    }
});
