<?php

declare(strict_types=1);

use Modules\Fixcity\Enums\TicketStatusEnum;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;

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

it('translates every ticket workflow status in every served language', function () use ($moduleLang): void {
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        $labels = personalAreaLang($moduleLang.'/'.$locale.'/personal_area.php')['status'] ?? null;
        Assert::assertIsArray($labels, $locale.': status');

        foreach (TicketStatusEnum::cases() as $status) {
            Assert::assertArrayHasKey($status->value, $labels, $locale.': no label for status '.$status->value);
        }
        Assert::assertCount(count(TicketStatusEnum::cases()), $labels, $locale.': labels for statuses that do not exist');
    }
});
