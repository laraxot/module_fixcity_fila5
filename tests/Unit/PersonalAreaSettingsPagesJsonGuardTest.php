<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;
use function Safe\glob;
use function Safe\json_decode;

/*
 * Guardia page-json (DCP-111): `/{lang}/area-personale/notifiche` e `/impostazioni` sono pagine JSON CMS riservate agli autenticati.
 * `PageSlugMiddleware` risolve ogni voce di `middleware` come alias o classe: `web` darebbe 500, `guest` bloccherebbe proprio gli utenti
 * autenticati, quindi la chiave deve essere esattamente ["auth"] (modello: tickets.create.json, DCP-42). Il tenant vince sul modulo:
 * le due copie devono restare identiche byte per byte. Solo filesystem, nessun database.
 */
$laravelRoot = dirname(__DIR__, 4);

/** @return array<array-key, mixed> */
function personalAreaPageJson(string $path): array
{
    Assert::assertFileExists($path);
    $json = json_decode(file_get_contents($path), true);
    Assert::assertIsArray($json, $path);

    return $json;
}

dataset('personal area page slugs', [
    'notifiche' => ['notifiche', 'personal-area-settings/notifications'],
    'impostazioni' => ['impostazioni', 'personal-area-settings/settings'],
]);

it('is served by one JSON per tenant and module with auth middleware and the Design Comuni layout', function (string $name) use ($laravelRoot): void {
    foreach (['config/local/fixcity', 'Modules/Fixcity/config'] as $base) {
        $path = $laravelRoot.'/'.$base.'/database/content/pages/area-personale.'.$name.'.json';
        $json = personalAreaPageJson($path);

        Assert::assertSame(['auth'], $json['middleware'] ?? null, $path.': middleware must be exactly ["auth"] (no "web", no "guest").');
        Assert::assertSame('design-comuni', $json['layout'] ?? null, $path.': layout');
        Assert::assertSame('area-personale.'.$name, $json['slug'] ?? null, $path.': slug');
        Assert::assertSame('area-personale.'.$name, $json['id'] ?? null, $path.': id must equal slug');
        foreach (['it', 'en', 'de', 'es'] as $locale) {
            $title = Arr::get($json, 'title.'.$locale);
            Assert::assertIsString($title, $path.': title.'.$locale);
            Assert::assertNotSame('', trim($title), $path.': title.'.$locale);
        }
    }
})->with(['notifiche', 'impostazioni']);

it('keeps the tenant and module copies byte for byte identical', function (string $name) use ($laravelRoot): void {
    $tenant = file_get_contents($laravelRoot.'/config/local/fixcity/database/content/pages/area-personale.'.$name.'.json');
    $module = file_get_contents($laravelRoot.'/Modules/Fixcity/config/database/content/pages/area-personale.'.$name.'.json');

    Assert::assertSame(md5($tenant), md5($module), 'area-personale.'.$name.'.json: tenant and module copies differ.');
})->with(['notifiche', 'impostazioni']);

it('points every locale to the theme block of the page, which exists and is not a Volt component', function (string $name, string $block) use ($laravelRoot): void {
    $json = personalAreaPageJson($laravelRoot.'/config/local/fixcity/database/content/pages/area-personale.'.$name.'.json');
    $blockFile = $laravelRoot.'/Themes/Sixteen/resources/views/components/blocks/'.$block.'.blade.php';
    Assert::assertFileExists($blockFile);

    foreach (['it', 'en', 'de', 'es'] as $locale) {
        Assert::assertSame(
            'pub_theme::components.blocks.'.str_replace('/', '.', $block),
            Arr::get($json, 'content_blocks.'.$locale.'.0.data.view'),
            $name.' ('.$locale.'): view',
        );
    }
    // BlockData deduce "Volt" dai primi 1024 byte: un commento con `state(` o `volt(` trasformerebbe il blocco in un componente
    Assert::assertDoesNotMatchRegularExpression('/state\(|volt\(|Livewire\\\\Volt/', substr(file_get_contents($blockFile), 0, 1024));
})->with('personal area page slugs');

it('has no dummy link in the theme blocks of the personal area settings pages', function () use ($laravelRoot): void {
    $files = glob($laravelRoot.'/Themes/Sixteen/resources/views/components/blocks/personal-area-settings/*.blade.php');
    Assert::assertNotSame([], $files);

    foreach ($files as $file) {
        Assert::assertIsString($file);
        Assert::assertStringNotContainsString('href="#"', file_get_contents($file), $file);
    }
});
