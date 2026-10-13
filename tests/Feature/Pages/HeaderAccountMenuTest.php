<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Illuminate\Support\Facades\Auth;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Safe\parse_url;

uses(TestCase::class);

/**
 * Header del sito reale (DCP-52): l'ospite vede "Accedi all'area personale" e va alla pagina di login della SUA lingua
 * (prima il link era `/login`, che risponde 404); il loggato vede il proprio nome e un menu le cui voci portano tutte a una
 * pagina che risponde. Niente "Mario Rossi" ne' avatar da servizi esterni.
 *
 * @return list<string>
 */
function headerLocales(): array
{
    return ['it', 'en', 'de', 'es'];
}

function headerDom(string $html): HTMLDocument
{
    return HTMLDocument::createFromString($html, LIBXML_NOERROR);
}

it('sends a guest to the login page of the current language, not to /login', function (string $locale): void {
    $response = $this->get("/{$locale}");
    $response->assertOk();

    $link = headerDom((string) $response->getContent())->querySelector('a[data-element="personal-area-login"]');
    Assert::assertNotNull($link, 'Il CTA di accesso manca per un ospite.');

    $path = parse_url((string) $link->getAttribute('href'), PHP_URL_PATH);
    Assert::assertSame("/{$locale}/auth/login", $path);
    Assert::assertNotSame('', trim((string) $link->getAttribute('aria-label')), 'Il link-icona non ha un nome accessibile.');
    $this->get((string) $path)->assertOk();
})->with(fn (): array => array_combine(headerLocales(), array_map(static fn (string $l): array => [$l], headerLocales())));

it('shows the signed-in user instead of the login call to action', function (string $locale): void {
    $user = UserFactory::new()->createOne(['name' => 'Giulia Verdi']);
    Auth::login($user);

    $response = $this->get("/{$locale}");
    $response->assertOk();
    $html = (string) $response->getContent();
    $document = headerDom($html);

    Assert::assertNull($document->querySelector('a[data-element="personal-area-login"]'), 'Un utente loggato vede ancora il CTA di accesso.');
    $wrapper = $document->querySelector('.it-user-wrapper');
    Assert::assertNotNull($wrapper, 'Manca il menu utente.');
    Assert::assertStringContainsString('Giulia Verdi', (string) $wrapper->textContent);
    Assert::assertStringNotContainsString('Mario Rossi', $html);
    Assert::assertStringNotContainsString('picsum.photos', $html, 'Avatar da un servizio esterno.');
    Assert::assertStringNotContainsString('pub_theme::', (string) $wrapper->textContent, 'Chiave di traduzione grezza nel menu.');
    Assert::assertStringNotContainsString('header.user.', (string) $wrapper->textContent, 'Chiave di traduzione grezza nel menu.');
})->with(fn (): array => array_combine(headerLocales(), array_map(static fn (string $l): array => [$l], headerLocales())));

it('links every menu entry to a page that works and has something to show', function (): void {
    Auth::login(UserFactory::new()->createOne());
    $document = headerDom((string) $this->get('/it')->assertOk()->getContent());

    $entries = $document->querySelectorAll('.it-user-wrapper a[role="menuitem"]');
    Assert::assertGreaterThan(0, count($entries), 'Il menu utente non ha voci.');

    foreach ($entries as $entry) {
        $href = (string) $entry->getAttribute('href');
        Assert::assertNotSame('', $href);
        Assert::assertNotSame('#', $href, 'Voce di menu senza destinazione: "'.trim((string) $entry->textContent).'".');

        $page = $this->get($href);
        $page->assertOk();
        $body = headerDom((string) $page->getContent());
        Assert::assertNotNull($body->querySelector('h1'), "La pagina {$href} non ha un titolo (h1).");
    }
});

it('offers a logout that ends the session', function (): void {
    Auth::login(UserFactory::new()->createOne());
    $document = headerDom((string) $this->get('/it')->assertOk()->getContent());

    $form = $document->querySelector('.it-user-wrapper form[method="POST"]');
    Assert::assertNotNull($form, 'Manca il form di uscita.');
    Assert::assertNotNull($form->querySelector('input[name="_token"]'), 'Il form di uscita non ha il token CSRF.');

    $action = parse_url((string) $form->getAttribute('action'), PHP_URL_PATH);
    Assert::assertIsString($action);
    $this->post($action)->assertRedirect();
    $this->assertGuest();
});
