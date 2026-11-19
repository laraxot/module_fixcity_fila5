<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match;

uses(TestCase::class);

/**
 * DCP-151: un indirizzo sbagliato dava la pagina nuda di Laravel ("Not Found", senza header, footer ne' lingua). Ora il front office risponde 404
 * con il layout del tema, nella lingua della richiesta e con tre strade che funzionano; API, JSON e file restano alla risposta standard.
 */
describe('404 del front office', function (): void {
    test('mostra la pagina del tema nella lingua della richiesta', function (string $locale, string $title, string $home): void {
        /** @var TestCase $this */
        $this->get('/'.$locale.'/zzz/yyy')
            ->assertNotFound()
            ->assertSee($title)
            ->assertSee('it-header-wrapper', false)
            ->assertSee('<footer', false)
            ->assertSee($home)
            ->assertDontSee('fixcity::error_pages');
    })->with([
        'it' => ['it', 'Pagina non trovata', 'Vai alla home'],
        'en' => ['en', 'Page not found', 'Go to the home page'],
        'de' => ['de', 'Seite nicht gefunden', 'Zur Startseite'],
        'es' => ['es', 'Página no encontrada', 'Ir a la página de inicio'],
    ]);

    test('offre tre strade nella lingua della richiesta e nessun link vuoto', function (): void {
        /** @var TestCase $this */
        $html = $this->get('/en/zzz/yyy')
            ->assertNotFound()
            ->assertSee('href="'.url('/en').'"', false)
            ->assertSee('href="'.url('/en/tickets').'"', false)
            ->assertSee('href="'.url('/en/tickets/create').'"', false)
            ->getContent();
        Assert::assertIsString($html);

        // solo il contenuto della pagina: header e footer sono il chrome condiviso (i loro link li verifica la story del footer)
        $found = preg_match('#<main\b.*?</main>#s', $html, $main);
        Assert::assertSame(1, $found);
        Assert::assertStringNotContainsString('href="#"', $main[0]);
    });

    test('vale anche per un ticket inesistente e per un percorso senza lingua', function (): void {
        /** @var TestCase $this */
        $this->get('/it/tickets/999999')->assertNotFound()->assertSee('Pagina non trovata');
        $this->get('/zzz')->assertNotFound()->assertSee('it-header-wrapper', false);
    });

    test('API, Livewire e richieste JSON non ricevono la pagina HTML del tema', function (): void {
        /** @var TestCase $this */
        $this->get('/api/zzz')->assertNotFound()->assertDontSee('it-header-wrapper', false);
        $this->get('/livewire/zzz')->assertNotFound()->assertDontSee('it-header-wrapper', false);
        $this->getJson('/it/zzz/yyy')->assertNotFound()->assertJsonStructure(['message']);
    });
});
