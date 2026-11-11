<?php

declare(strict_types=1);

use Modules\Xot\Tests\XotBaseTestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\preg_match_all;

uses(XotBaseTestCase::class);

/*
 * Upload foto da fotocamera: il sito e' multilingua, quindi nessuna frase vive nel Blade o nel JavaScript.
 * 1) ogni chiave `fixcity::ticket_form.fields.images.*` usata da dc-file-upload.blade.php esiste, tradotta, in it/en/de/es;
 * 2) `validation.uploaded` (il messaggio che Livewire mostra quando PHP rifiuta il file) e' una frase intera in ogni lingua: in italiano
 *    mancava e Livewire ricadeva sulla propria stringa inglese cablata "The ... failed to upload"; senza `:attribute`, perche' Livewire
 *    passa il nome interno del campo (`data.images.f...`).
 * Si leggono solo i file di traduzione, ma con `fallback_locale = it` un `trans()` su una chiave mancante in en/de/es restituirebbe il testo
 * italiano e un test sul solo valore passerebbe a vuoto: si chiede quindi al traduttore se la chiave esiste NELLA lingua (`hasForLocale`).
 * Con il traduttore di Lang una chiave assente scrive una riga in `translations`: eseguire solo sui database di prova (pest-scratch-db.sh).
 */
dataset('site locales', ['it', 'en', 'de', 'es']);

it('translates every image upload key used by the upload view', function (string $locale): void {
    $view = (string) file_get_contents(dirname(__DIR__, 2).'/resources/views/filament/forms/components/dc-file-upload.blade.php');
    preg_match_all('/fixcity::ticket_form\.fields\.images\.([a-z_]+)/', $view, $matches);
    $keys = array_unique($matches[1]);

    Assert::assertContains('error_prepare', $keys);
    Assert::assertContains('camera_button', $keys);

    foreach ($keys as $key) {
        $full = "fixcity::ticket_form.fields.images.{$key}";
        Assert::assertTrue(app('translator')->hasForLocale($full, $locale), "Chiave assente in '{$locale}' (il fallback it la nasconderebbe): {$full}");
        $text = trans($full, [], $locale);
        Assert::assertNotSame('', trim($text));
    }
})->with('site locales');

it('has a whole-sentence upload failure message in every site language', function (string $locale): void {
    Assert::assertTrue(app('translator')->hasForLocale('validation.uploaded', $locale), "validation.uploaded assente in '{$locale}'");
    $message = trans('validation.uploaded', ['attribute' => 'data.images.f1'], $locale);

    Assert::assertNotSame('validation.uploaded', $message, "Chiave grezza in '{$locale}'");
    Assert::assertStringNotContainsString('data.images', $message, "Il nome interno del campo trapela in '{$locale}'");
    Assert::assertStringNotContainsString(':attribute', $message);
})->with('site locales');
