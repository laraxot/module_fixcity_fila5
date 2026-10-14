<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\AssessPrivacyPolicyAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;

uses(TestCase::class);

/*
 * DCP-130: l'informativa del tenant deve avere le dieci sezioni dell'art. 13 GDPR, senza segnaposto. Il codice non scrive i valori
 * (titolare, DPO, base giuridica, termini): li fornisce l'ente, qui si verifica solo che ci siano.
 */
dataset('privacy locales', ['it', 'en', 'de', 'es']);

/**
 * Un testo completo: ogni sezione obbligatoria con un corpo qualsiasi (nessun valore legale vero).
 *
 * @param  list<string>  $without  chiavi di sezione da omettere
 */
function privacyPolicyFixture(array $without = [], string $body = 'Testo fornito dall’ente.'): string
{
    $text = "# Informativa\n\nIntroduzione.\n";
    foreach (AssessPrivacyPolicyAction::SECTIONS as $key) {
        if (! in_array($key, $without, true)) {
            $text .= "\n## ".__('fixcity::privacy.sections.'.$key.'.title')."\n\n".$body."\n";
        }
    }

    return $text;
}

/**
 * @return array<string, string>
 */
function assessPrivacy(?string $markdown): array
{
    return app(AssessPrivacyPolicyAction::class)->execute($markdown);
}

it('accepts a policy that has every art. 13 section and no placeholder', function (string $locale): void {
    app()->setLocale($locale);

    expect(assessPrivacy(privacyPolicyFixture()))->toBe([]);
})->with('privacy locales');

it('lists every section when there is no policy text', function (): void {
    expect(assessPrivacy(null))->toBe(array_fill_keys(AssessPrivacyPolicyAction::SECTIONS, 'missing'));
});

it('names the sections that are missing or empty', function (): void {
    $text = privacyPolicyFixture(['dpo', 'retention', 'complaint'])."\n## ".__('fixcity::privacy.sections.complaint.title')."\n\n   \n";

    expect(assessPrivacy($text))->toBe(['dpo' => 'missing', 'retention' => 'missing', 'complaint' => 'missing']);
});

it('rejects placeholders inside a section and outside the sections', function (string $placeholder): void {
    $inside = str_replace('Testo fornito dall’ente.', $placeholder, privacyPolicyFixture(), $count);
    expect($count)->toBe(10);
    expect(assessPrivacy($inside))->toBe(array_fill_keys(AssessPrivacyPolicyAction::SECTIONS, 'placeholder'));

    $outside = str_replace('Introduzione.', $placeholder, privacyPolicyFixture());
    expect(assessPrivacy($outside))->toBe(['document' => 'placeholder']);
})->with([
    'double brackets' => ['[[DA COMPILARE: titolare]]'],
    'stub file' => ['Edit this file to define the privacy policy for your application.'],
    'lorem ipsum' => ['Lorem ipsum dolor sit amet'],
    'todo' => ['TODO scrivere il termine'],
    'example address' => ['privacy@comune.example.it'],
    'x run' => ['telefono xxxx'],
]);

it('keeps sub-headings and numbered titles inside their section', function (): void {
    $title = __('fixcity::privacy.sections.controller.title');
    $text = privacyPolicyFixture(['controller'])."\n## 1. {$title}\n\n### Recapiti\n\nTesto.\n";

    expect(assessPrivacy($text))->toBe([]);
});

it('ships a template per locale with every section and only placeholders as content', function (string $locale): void {
    app()->setLocale($locale);
    $template = file_get_contents(dirname(__DIR__, 2)."/resources/templates/privacy-policy.{$locale}.md");

    foreach (AssessPrivacyPolicyAction::SECTIONS as $key) {
        Assert::assertStringContainsString("\n## ".__('fixcity::privacy.sections.'.$key.'.title')."\n", $template, "Modello {$locale} senza la sezione {$key}.");
    }
    expect(assessPrivacy($template))->toBe(array_fill_keys(AssessPrivacyPolicyAction::SECTIONS, 'placeholder'));
})->with('privacy locales');
