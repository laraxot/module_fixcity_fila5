<?php

declare(strict_types=1);

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\AssessPrivacyPolicyAction;
use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\preg_match_all;

uses(TestCase::class);

/*
 * DCP-130: /{lingua}/privacy e' la pagina JSON `privacy.index` (blocco legal-document). Risponde sempre 200 con <h1>: col testo
 * completo mostra il testo; con testo segnaposto, incompleto o assente mostra un avviso che elenca che cosa deve fornire il titolare.
 * Questi test fissano il testo dell'ente con una finta; il testo VERO del tenant lo verifica PrivacyPolicyPublicationGateTest.
 */
function fakeTenantLegalText(?string $markdown): void
{
    app()->instance(GetPublishedLegalDocumentAction::class, new class($markdown) extends GetPublishedLegalDocumentAction
    {
        public function __construct(private readonly ?string $markdown) {}

        public function execute(string $file): ?string
        {
            return $this->markdown;
        }
    });
}

function completePolicyText(string $locale): string
{
    app()->setLocale($locale);
    $text = "# Titolo del testo\n\nIntroduzione dell’ente.\n";
    foreach (AssessPrivacyPolicyAction::SECTIONS as $key) {
        $text .= "\n## ".__('fixcity::privacy.sections.'.$key.'.title')."\n\nCorpo {$key} fornito dall’ente.\n";
    }

    return $text."\n<script>alert('unsafe')</script>\n";
}

dataset('privacy page locales', ['it' => ['it', 'Informativa privacy'], 'en' => ['en', 'Privacy notice'], 'de' => ['de', 'Datenschutzerklärung'], 'es' => ['es', 'Aviso de privacidad']]);

it('serves the complete policy as sanitized text with no warning', function (string $locale, string $title): void {
    fakeTenantLegalText(completePolicyText($locale));

    $response = $this->get("/{$locale}/privacy")->assertOk();
    $response
        ->assertSee('<h1 class="text-black" data-element="page-name">'.$title.'</h1>', false)
        ->assertSee('Corpo controller fornito dall’ente.', false)
        ->assertSee('data-element="legal-text"', false)
        ->assertDontSee('data-element="legal-incomplete"', false)
        ->assertDontSee("alert('unsafe')", false);
    // il titolo del testo diventa <h2>: la pagina ha un solo <h1>
    expect(substr_count((string) $response->getContent(), '<h1'))->toBe(1)
        ->and((string) $response->getContent())->toContain('<h2>Titolo del testo</h2>');
})->with('privacy page locales');

it('warns and lists the fields to fill when the policy still has placeholders', function (string $locale): void {
    fakeTenantLegalText(file_get_contents(dirname(__DIR__, 3)."/resources/templates/privacy-policy.{$locale}.md"));
    app()->setLocale($locale);

    $response = $this->get("/{$locale}/privacy")->assertOk()->assertSee('data-element="legal-incomplete"', false);
    foreach (AssessPrivacyPolicyAction::SECTIONS as $key) {
        $response->assertSee(e((string) __('fixcity::privacy.sections.'.$key.'.title')), false);
    }
    $response->assertSee(e((string) __('fixcity::privacy.reason.placeholder')), false);
})->with('privacy page locales');

it('is a 200 page with a heading and a warning, never empty, when the tenant has no policy text', function (string $locale, string $title): void {
    fakeTenantLegalText(null);

    $response = $this->get("/{$locale}/privacy")->assertOk();
    $response
        ->assertSee('<h1 class="text-black" data-element="page-name">'.$title.'</h1>', false)
        ->assertSee('data-element="legal-incomplete"', false)
        ->assertDontSee('data-element="legal-text"', false);
    expect((string) $response->getContent())->toContain(e((string) __('pub_theme::legal.incomplete.intro_missing')));
})->with('privacy page locales');

it('names only the missing section when the rest of the policy is complete', function (): void {
    $text = str_replace("## Periodo di conservazione\n\nCorpo retention fornito dall’ente.\n", '', completePolicyText('it'));
    fakeTenantLegalText($text);

    $this->get('/it/privacy')->assertOk()
        ->assertSee('data-element="legal-incomplete"', false)
        ->assertSee('<strong>Periodo di conservazione</strong>', false)
        ->assertDontSee('<strong>Titolare del trattamento</strong>', false);
});

it('is the page the wizard consent link and the footer point to', function (string $locale): void {
    // TicketForm::getGdprHtml costruisce il link cosi'
    $wizardUrl = (string) LaravelLocalization::getLocalizedURL($locale, '/privacy');
    $this->get($wizardUrl)->assertOk()->assertSee('data-slug="privacy.index"', false);

    preg_match_all('#<a href="([^"]*/privacy)"#', (string) $this->get("/{$locale}")->getContent(), $footerLinks);
    $privacyLinks = array_values(array_unique($footerLinks[1]));
    expect($privacyLinks)->toBe([$wizardUrl]);
})->with(['it', 'en', 'de', 'es']);
