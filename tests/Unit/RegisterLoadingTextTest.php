<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;

uses(TestCase::class);

/*
 * DCP-377 (A-13): il form di registrazione mostrava il testo di caricamento del login («Accesso in corso...») nel pulsante e nello stato
 * per lo screen reader. Ha un testo proprio, in tutte e 4 le lingue, diverso da quello del login. Solo filesystem, nessun database.
 */
dataset('register_loading_locales', ['it', 'en', 'de', 'es']);

it('has its own loading text for the registration form in every language', function (string $locale): void {
    $lang = require dirname(__DIR__, 2).'/lang/'.$locale.'/auth.php';
    Assert::assertIsArray($lang);

    $register = Arr::get($lang, 'register.loading.text');
    $login = Arr::get($lang, 'login.loading.text');

    Assert::assertIsString($register, "{$locale}: manca fixcity::auth.register.loading.text");
    Assert::assertNotSame('', trim($register));
    Assert::assertNotSame($login, $register, "{$locale}: il testo e' quello del login");
})->with('register_loading_locales');

it('does not borrow the login loading text in the registration view', function (): void {
    $view = file_get_contents(dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/filament/widgets/auth/register.blade.php');

    Assert::assertStringNotContainsString('auth.login.loading', $view);
    Assert::assertStringContainsString("__('fixcity::auth.register.loading.text')", $view);
});
