<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;

/*
 * Guardia i18n (DCP-111): i testi delle pagine "Notifiche" e "Impostazioni e privacy" esistono in it, en, de, es con le stesse chiavi,
 * nessuna stringa vuota e gli stessi segnaposto (`:code`, `:status`, `:count`, `:article`) in ogni lingua. Solo filesystem.
 */
$langRoot = dirname(__DIR__, 2).'/lang';

/** @return array<array-key, mixed> */
function personalAreaLoad(string $path): array
{
    $lines = require $path;
    Assert::assertIsArray($lines, $path);

    return $lines;
}

/**
 * @param  array<array-key, mixed>  $array
 * @return array<string, string>
 */
function personalAreaFlatten(array $array, string $prefix = ''): array
{
    $flat = [];
    foreach ($array as $key => $value) {
        $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
        if (is_array($value)) {
            $flat += personalAreaFlatten($value, $path);
        } else {
            Assert::assertIsString($value, $path);
            $flat[$path] = $value;
        }
    }

    return $flat;
}

/** @return list<string> */
function personalAreaPlaceholders(string $text): array
{
    preg_match_all('/:[a-z_]+/', $text, $matches);
    $found = array_values(array_unique($matches[0]));
    sort($found);

    return $found;
}

dataset('personal area lang files', [
    'personal_area_settings' => ['personal_area_settings'],
    'ticket_notification_preferences' => ['ticket_notification_preferences'],
]);

it('has the same keys, no empty text and the same placeholders in it, en, de, es', function (string $file) use ($langRoot): void {
    $italian = personalAreaFlatten(personalAreaLoad($langRoot.'/it/'.$file.'.php'));
    Assert::assertNotSame([], $italian);

    foreach (['en', 'de', 'es'] as $locale) {
        $translated = personalAreaFlatten(personalAreaLoad($langRoot.'/'.$locale.'/'.$file.'.php'));

        Assert::assertSame(array_keys($italian), array_keys($translated), $file.' ('.$locale.'): keys differ from it');
        foreach ($translated as $key => $text) {
            Assert::assertNotSame('', trim($text), $file.' ('.$locale.'): '.$key.' is empty');
            if (str_ends_with($key, '.complaint_url') || str_ends_with($key, '.article')) {
                continue; // dati, non testo tradotto
            }
            Assert::assertSame(personalAreaPlaceholders($italian[$key]), personalAreaPlaceholders($text), $file.' ('.$locale.'): '.$key.' placeholders differ');
        }
    }
})->with('personal area lang files');

it('has a status label in every language for each ticket status, so notifications never fall back to Italian', function () use ($langRoot): void {
    $italian = personalAreaFlatten(personalAreaLoad($langRoot.'/it/ticket_status_enum.php'));

    foreach (['en', 'de', 'es'] as $locale) {
        $translated = personalAreaFlatten(personalAreaLoad($langRoot.'/'.$locale.'/ticket_status_enum.php'));
        Assert::assertSame(array_keys($italian), array_keys($translated), 'ticket_status_enum ('.$locale.'): keys differ from it');
    }
});
