<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-53: /lista-categorie rispondeva 500 perche' `[container0]/index.blade.php` includeva
 * `pub_theme::pages.services.categories-content`, rimasta solo in `pages_old/`. Il partial vive ora in
 * `components/pages/categories.blade.php`, come faq e sitemap. Il layout ha gia' un `<main>`: il partial non ne aggiunge un secondo.
 * (Il test omonimo di Themes/Sixteen/tests/Feature/ComunePagesTest.php non gira: usa una classe `Modules\Fixcity\App\Models\Ticket` che non esiste.)
 */
dataset('locales', ['it', 'en', 'de', 'es']);

it('renders the category directory in every language with a single main landmark and one h1', function (string $locale): void {
    /** @var TestCase $this */
    $response = $this->get("/{$locale}/lista-categorie");
    $response->assertOk();

    $document = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);
    Assert::assertCount(1, $document->querySelectorAll('main'), 'Piu\' di un landmark main.');
    Assert::assertCount(1, $document->querySelectorAll('h1'), 'Deve esserci un solo h1.');
    Assert::assertNotNull($document->querySelector('#category-directory'), 'Manca il catalogo delle categorie.');
    Assert::assertNotNull($document->querySelector('a[href$="/'.$locale.'/tickets"]'), 'Manca il link all\'elenco segnalazioni localizzato.');
})->with('locales');

it('does not leak translation keys into the category directory', function (string $locale): void {
    /** @var TestCase $this */
    $this->get("/{$locale}/lista-categorie")->assertOk()->assertDontSee('pub_theme::services', false);
})->with('locales');
