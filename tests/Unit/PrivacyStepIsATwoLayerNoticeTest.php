<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;
use function Safe\preg_split;

uses(TestCase::class);

/*
 * DCP-457 (guardia; il codice e' in DCP-456): il passo 1 del wizard stampava l'intera `policy.md` (1.729 parole, 10 titoli, 5 segnaposto `[[ente.*]]` grezzi) contro le ~98 parole
 * del riferimento `segnalazione-01-privacy`. Informativa a due livelli (art. 12 par. 1 GDPR): al passo un avviso breve e un link, il testo
 * completo vive su `/privacy`. Il testo lungo e' finto e con segnaposto: il test non dipende da quello che l'ente ha scritto o compilato.
 */
dataset('privacy_step_locales', ['it', 'en', 'de', 'es']);

/** Sostituisce l'informativa del tenant con una molto lunga che contiene un segnaposto non risolto. */
function privacyStepFakeLongPublishedPolicy(): void
{
    $body = '# Informativa'."\n\n".'## Titolare del trattamento'."\n\n".'Titolare: [[ente.name]]. '.str_repeat('Testo lungo di prova sul trattamento. ', 400);
    app()->instance(GetPublishedPrivacyPolicyAction::class, new class($body) extends GetPublishedPrivacyPolicyAction
    {
        public function __construct(private readonly string $body) {}

        public function execute(): string
        {
            return $this->body;
        }
    });
}

function privacyStepText(): string
{
    return trim(html_entity_decode(strip_tags((string) TicketForm::getGdprHtml())));
}

it('shows a short first-layer notice instead of the whole published policy', function (string $locale): void {
    app()->setLocale($locale);
    privacyStepFakeLongPublishedPolicy();
    $maxWords = 150;

    $html = (string) TicketForm::getGdprHtml();
    $words = count(preg_split('/\s+/u', privacyStepText()));

    Assert::assertLessThanOrEqual($maxWords, $words, "{$locale}: il passo 1 ha {$words} parole, il primo livello deve stare sotto {$maxWords}");
    Assert::assertStringNotContainsString('Testo lungo di prova', $html, "{$locale}: il testo dell'informativa non va riprodotto nel passo");
    Assert::assertStringNotContainsString('[[ente', $html, "{$locale}: segnaposto dell'ente visibile al cittadino");
    Assert::assertStringNotContainsString('fixcity::', $html, "{$locale}: chiave di traduzione grezza");
    Assert::assertDoesNotMatchRegularExpression('/<h[1-6]\b|<li\b/', $html, "{$locale}: titoli o elenchi dell'informativa nel passo");
})->with('privacy_step_locales');

it('links to the complete notice and related legal pages in the same language', function (string $locale): void {
    app()->setLocale($locale);
    privacyStepFakeLongPublishedPolicy();

    $html = (string) TicketForm::getGdprHtml();
    $found = preg_match_all('/<a\b[^>]*href="([^"]*)"/', $html, $links);

    Assert::assertSame(4, $found, "{$locale}: il primo livello deve offrire quattro approfondimenti legali");
    Assert::assertStringContainsString('/'.$locale.'/privacy', $html, "{$locale}: manca l'informativa privacy");
    Assert::assertStringContainsString('/'.$locale.'/terms', $html, "{$locale}: mancano i termini");
    Assert::assertStringContainsString('/'.$locale.'/note-legali', $html, "{$locale}: mancano le note legali");
    Assert::assertStringContainsString('/'.$locale.'/cookie-policy', $html, "{$locale}: manca la cookie policy");
})->with('privacy_step_locales');

it('names the system, not a municipality, as the data controller', function (): void {
    app()->setLocale('it');
    privacyStepFakeLongPublishedPolicy();

    $text = privacyStepText();

    Assert::assertStringContainsString('FixCity', $text);
    Assert::assertStringNotContainsString('Comune', $text);
    Assert::assertStringNotContainsString(':entity', $text);
    Assert::assertStringNotContainsString(':name', $text);
});

it('describes the FixCity purpose instead of claiming that the municipality manages data', function (): void {
    app()->setLocale('it');
    privacyStepFakeLongPublishedPolicy();

    $text = privacyStepText();

    Assert::assertStringContainsString('ricevere la segnalazione di un disservizio', $text);
    Assert::assertStringContainsString('gestirla e comunicarti gli aggiornamenti', $text);
    Assert::assertStringNotContainsString('gestisce i dati personali', $text);
});

it('keeps the warning when no privacy policy is published', function (): void {
    app()->setLocale('it');
    app()->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): ?string
        {
            return null;
        }
    });

    Assert::assertStringContainsString('role="alert"', (string) TicketForm::getGdprHtml());
});

it('has a neutral first-layer text in every language', function (string $key): void {
    $texts = [];
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        app()->setLocale($locale);
        $texts[$locale] = (string) __('fixcity::ticket.gdpr_notice.'.$key);
        Assert::assertNotSame('fixcity::ticket.gdpr_notice.'.$key, $texts[$locale], "{$locale}: chiave non tradotta");
        Assert::assertStringNotContainsString(':entity', $texts[$locale], "{$locale}: segnaposto non sostituito");
        Assert::assertStringNotContainsString(':municipality', $texts[$locale], "{$locale}: segnaposto ente non sostituito");
    }

    Assert::assertCount(4, array_unique($texts), 'il primo livello e\' uguale in due lingue: fallback it nascosto');
})->with(['text', 'text_no_name']);
