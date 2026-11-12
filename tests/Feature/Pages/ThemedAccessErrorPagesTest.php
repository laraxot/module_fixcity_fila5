<?php

declare(strict_types=1);

use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match;

uses(TestCase::class);

/**
 * Audit front office 2026-11-11: 419 (sessione scaduta) e 403 (accesso negato) davano la pagina nuda di Laravel in inglese ("Page Expired",
 * "Forbidden"), senza header, footer ne' lingua. Ora il front office le serve con il layout del tema, nella lingua della richiesta, senza mai
 * mostrare il messaggio dell'eccezione; API, Livewire e JSON restano alla risposta standard. Le rotte di prova lanciano l'eccezione come farebbe
 * il CSRF (419) o una policy (403).
 */
beforeEach(function (): void {
    Route::middleware('web')->group(static function (): void {
        Route::get('/it/zz-forbidden', static fn () => abort(403, 'dettaglio-tecnico-riservato'));
        Route::get('/en/zz-forbidden', static fn () => abort(403, 'dettaglio-tecnico-riservato'));
        Route::get('/de/zz-forbidden', static fn () => abort(403));
        Route::get('/es/zz-forbidden', static fn () => abort(403));
        Route::get('/it/zz-expired', static fn () => throw new TokenMismatchException);
        Route::get('/en/zz-expired', static fn () => throw new TokenMismatchException);
    });
    Route::middleware('api')->get('/api/zz-forbidden', static fn () => abort(403));
});

describe('403 e 419 del front office', function (): void {
    test('403 mostra la pagina del tema nella lingua della richiesta', function (string $locale, string $title, string $login): void {
        /** @var TestCase $this */
        $this->get('/'.$locale.'/zz-forbidden')
            ->assertForbidden()
            ->assertSee($title)
            ->assertSee($login)
            ->assertSee('it-header-wrapper', false)
            ->assertSee('<footer', false)
            ->assertDontSee('fixcity::error_pages')
            ->assertDontSee('dettaglio-tecnico-riservato');
    })->with([
        'it' => ['it', 'Accesso non consentito', 'Accedi'],
        'en' => ['en', 'Access not allowed', 'Sign in'],
        'de' => ['de', 'Zugriff nicht erlaubt', 'Anmelden'],
        'es' => ['es', 'Acceso no permitido', 'Iniciar sesión'],
    ]);

    test('419 mostra la pagina del tema con stato 419', function (string $locale, string $title): void {
        /** @var TestCase $this */
        $this->get('/'.$locale.'/zz-expired')
            ->assertStatus(419)
            ->assertSee($title)
            ->assertSee('it-header-wrapper', false)
            ->assertDontSee('fixcity::error_pages');
    })->with([
        'it' => ['it', 'Sessione scaduta'],
        'en' => ['en', 'Session expired'],
    ]);

    test('offre accesso e home nella lingua della richiesta e nessun link vuoto', function (): void {
        /** @var TestCase $this */
        $html = $this->get('/en/zz-expired')
            ->assertStatus(419)
            ->assertSee('href="'.url('/en/auth/login').'"', false)
            ->assertSee('href="'.url('/en').'"', false)
            ->getContent();
        Assert::assertIsString($html);

        $found = preg_match('#<main\b.*?</main>#s', $html, $main);
        Assert::assertSame(1, $found);
        Assert::assertStringNotContainsString('href="#"', $main[0]);
    });

    test('API e richieste JSON non ricevono la pagina HTML del tema', function (): void {
        /** @var TestCase $this */
        $this->get('/api/zz-forbidden')->assertForbidden()->assertDontSee('it-header-wrapper', false);
        $this->getJson('/it/zz-forbidden')->assertForbidden()->assertJsonStructure(['message']);
    });
});
