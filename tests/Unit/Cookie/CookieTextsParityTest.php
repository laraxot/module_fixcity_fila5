<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

use function Safe\file_get_contents;
use function Safe\preg_match_all;

/*
 * DCP-153: il sito e' in 4 lingue. Ogni testo del banner e della cookie policy deve esistere in tutte (la lingua di ripiego e' l'italiano e
 * nasconderebbe un buco: in /de comparirebbe un testo italiano), e ogni chiave usata da una vista deve esistere. Si legge il file lingua,
 * non trans(): una chiave mancante, chiesta con trans(), farebbe scrivere una riga nel DB dall'auto-writer del modulo Lang.
 */
/**
 * @return array<string, string>
 */
function cookieTexts(string $locale): array
{
    /** @var array<string, string> $texts */
    $texts = require base_path('Modules/Gdpr/lang/cookie-consent/').$locale.'/texts.php';

    return $texts;
}

it('has the same keys in it, en, de and es', function (): void {
    $reference = array_keys(cookieTexts('it'));
    foreach (['en', 'de', 'es'] as $locale) {
        $keys = array_keys(cookieTexts($locale));
        Assert::assertSame([], array_values(array_diff($reference, $keys)), "Chiavi mancanti in '{$locale}'");
        Assert::assertSame([], array_values(array_diff($keys, $reference)), "Chiavi in piu' in '{$locale}'");
    }
});

it('has a non empty text in every language that is not the Italian one', function (string $locale): void {
    $it = cookieTexts('it');
    // identiche in piu' lingue per costruzione: nome del cookie e del documento
    $same = ['policy_name', 'policy_title', 'policy_marketing', 'policy_type'];
    foreach (cookieTexts($locale) as $key => $value) {
        Assert::assertNotSame('', trim($value), "{$locale}.{$key} vuoto");
        if (! in_array($key, $same, true)) {
            Assert::assertNotSame($it[$key], $value, "{$locale}.{$key} e' uguale all'italiano: testo non tradotto?");
        }
    }
})->with(['en', 'de', 'es']);

it('keeps the placeholders of the duration texts in every language', function (string $locale): void {
    $texts = cookieTexts($locale);
    Assert::assertStringContainsString(':minutes', $texts['duration_minutes']);
    Assert::assertStringContainsString(':days', $texts['duration_days']);
})->with(['it', 'en', 'de', 'es']);

it('defines every key used by the banner view, the cookie policy block and the inventory', function (): void {
    $files = [
        base_path('Modules/Gdpr/resources/views/cookie-consent/index.blade.php'),
        base_path('Themes/Sixteen/resources/views/components/blocks/cookie-policy/design-comuni.blade.php'),
        base_path('Modules/Gdpr/app/Actions/Cookie/GetCookieInventoryAction.php'),
    ];
    $used = [];
    foreach ($files as $file) {
        preg_match_all("/(?:'|texts\\.)((?:alert|settings?|policy|purpose|duration|reopen)_[a-z_]+)'/", file_get_contents($file), $found);
        $used = [...$used, ...$found[1]];
    }
    $used = array_values(array_unique($used));
    Assert::assertGreaterThan(20, count($used), 'La ricerca delle chiavi non trova abbastanza chiavi: aggiornare il criterio');
    $defined = array_keys(cookieTexts('it'));
    Assert::assertSame([], array_values(array_diff($used, $defined)), 'Chiavi usate ma non definite');
});

/*
 * Il pacchetto statikbe registra il namespace `cookie-consent` dopo il modulo e per le traduzioni vince l'ultimo: senza la registrazione a boot
 * concluso (GdprServiceProvider) il banner mostrava le chiavi grezze. Qui si chiede il testo con trans(), non si legge il file.
 * Le chiavi esistono in tutte e 4 le lingue (test sopra), quindi trans() non puo' far scrivere righe all'auto-writer del modulo Lang.
 */
it('resolves the module texts through the translator, not the package ones', function (string $locale): void {
    app()->setLocale($locale);
    foreach (['alert_ok', 'alert_more', 'policy_title', 'setting_essential', 'settings_save'] as $key) {
        Assert::assertSame(cookieTexts($locale)[$key], trans('cookie-consent::texts.'.$key), "{$locale}: {$key} non risolto dal file del modulo");
    }
})->with(['it', 'en', 'de', 'es']);
