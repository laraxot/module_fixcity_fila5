<?php

declare(strict_types=1);

use Filament\Forms\Components\Checkbox;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-377 (T-08): senza messaggi propri il checkbox del consenso usava «:attribute deve essere accettato» con l'etichetta intera come
 * attributo: «Il campo ho letto e compreso l'informativa sulla privacy deve essere accettato.» (in inglese «The i have read ... rmust be accepted»).
 * Ora ha una frase propria, tradotta, senza segnaposto.
 */
dataset('privacy_consent_locales', ['it', 'en', 'de', 'es']);

it('words the privacy consent errors with its own text instead of the checkbox label', function (string $locale): void {
    app()->setLocale($locale);
    $checkbox = TicketForm::getPrivacySchema()['privacyAccepted'];
    Assert::assertInstanceOf(Checkbox::class, $checkbox);

    $expected = (string) __('fixcity::ticket_form.validation.privacy_accepted');
    Assert::assertNotSame('fixcity::ticket_form.validation.privacy_accepted', $expected, "{$locale}: chiave non tradotta");

    $messages = $checkbox->getValidationMessages();
    foreach (['accepted', 'required'] as $rule) {
        Assert::assertSame($expected, $messages[$rule] ?? null, "{$locale}: il messaggio '{$rule}' non e' quello del consenso");
    }
    Assert::assertStringNotContainsString(':attribute', $expected);
})->with('privacy_consent_locales');

it('has a different consent message in every language', function (): void {
    $texts = [];
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        app()->setLocale($locale);
        $texts[$locale] = (string) __('fixcity::ticket_form.validation.privacy_accepted');
    }

    Assert::assertCount(4, array_unique($texts));
});
