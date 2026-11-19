<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Support\MessageBag;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Modules\Fixcity\Tests\TestCase;
use Modules\Gdpr\Filament\Widgets\Auth\RegisterWidget;
use Modules\Gdpr\Models\Consent;
use Modules\User\Models\User;

uses(TestCase::class);

/*
 * Registrazione del cittadino (DCP-178): per registrarsi si accettano informativa e termini, e deve restare la PROVA dell'accettazione.
 * Misurato il 2026-10-14: `treatments` vuota e seeder mai chiamato, quindi la registrazione non salvava nessun consenso; la password
 * non aveva regole; il messaggio degli errori sulle caselle era vuoto.
 */

/**
 * @param  array<string, mixed>  $override
 * @return array<string, mixed>
 */
function validRegistration(array $override = []): array
{
    return array_merge([
        'first_name' => 'Maria',
        'last_name' => 'Rossi',
        'email' => 'maria.rossi.'.uniqid().'@example.test',
        'password' => 'Str0ng!Passw0rd#2026',
        'password_confirmation' => 'Str0ng!Passw0rd#2026',
        'privacy_accepted' => true,
        'terms_accepted' => true,
        'marketing_consent' => false,
    ], $override);
}

/**
 * @param  array<string, mixed>  $fields
 * @return Testable<RegisterWidget>
 */
function registrationComponent(array $fields): Testable
{
    $component = Livewire::test(RegisterWidget::class);
    foreach ($fields as $name => $value) {
        $component->set($name, $value);
    }

    return $component;
}

function consentOf(User $user, string $type): Consent
{
    return Consent::query()->where('user_id', $user->getKey())->where('type', $type)->firstOrFail();
}

beforeEach(function (): void {
    // `Password::defaults()` ha `uncompromised`: la richiesta a HaveIBeenPwned non deve uscire dal test
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('', 200)]);
});

it('refuses the registration when privacy and terms are not accepted, with a readable message', function (): void {
    /** @var TestCase $this */
    $component = registrationComponent(validRegistration(['privacy_accepted' => false, 'terms_accepted' => false]))->call('submit');

    $component->assertHasErrors(['privacy_accepted', 'terms_accepted']);
    foreach (['privacy_accepted' => 'gdpr::register.consents.privacy_policy_required', 'terms_accepted' => 'gdpr::register.consents.terms_required'] as $field => $key) {
        // il testo e' quello tradotto della chiave, non una chiave nuda ne' il messaggio generico di Laravel
        expect(__($key))->not->toBe($key);
        $errors = $component->errors();
        expect($errors)->toBeInstanceOf(MessageBag::class);
        expect($errors instanceof MessageBag ? $errors->first($field) : '')->toBe(__($key));
    }
    expect(User::query()->where('email', 'like', 'maria.rossi.%@example.test')->count())->toBe(0);
});

it('refuses a weak password', function (): void {
    /** @var TestCase $this */
    registrationComponent(validRegistration(['password' => 'abc', 'password_confirmation' => 'abc']))
        ->call('submit')
        ->assertHasErrors(['password']);
});

it('keeps the proof of what the citizen accepted, with the document version', function (): void {
    /** @var TestCase $this */
    $data = validRegistration();
    registrationComponent($data)->call('submit')->assertHasNoErrors();

    $user = User::query()->where('email', $data['email'])->firstOrFail();
    expect(Consent::query()->where('user_id', $user->getKey())->count())->toBe(3);

    expect(consentOf($user, 'privacy_policy')->accepted_at)->not->toBeNull();
    expect(consentOf($user, 'terms_conditions')->accepted_at)->not->toBeNull();
    expect(consentOf($user, 'marketing_consent')->accepted_at)->toBeNull();
    foreach (['privacy_policy', 'terms_conditions'] as $type) {
        $version = data_get(consentOf($user, $type), 'metadata.document_version');
        expect($version)->toBeString()->not->toBe('');
    }
});

it('records the optional consent only when the citizen ticks it', function (): void {
    /** @var TestCase $this */
    $data = validRegistration(['marketing_consent' => true]);
    registrationComponent($data)->call('submit')->assertHasNoErrors();

    $user = User::query()->where('email', $data['email'])->firstOrFail();
    expect(consentOf($user, 'marketing_consent')->accepted_at)->not->toBeNull();
});

it('lets the citizen open the privacy notice and the terms from the registration form, in every language', function (string $lang): void {
    /** @var TestCase $this */
    $html = $this->get("/{$lang}/auth/register")->assertOk()->getContent();
    $html = is_string($html) ? $html : '';
    $start = (int) strpos($html, 'wire:model="privacy_accepted"');
    $section = substr($html, $start, (int) strpos($html, '</fieldset>', $start) - $start);

    expect($section)->toContain(url("/{$lang}/privacy"))->toContain(url("/{$lang}/terms"));
})->with(['it', 'en', 'de', 'es']);

it('slows down a flood of registration attempts from the same address, with a readable message', function (): void {
    /** @var TestCase $this */
    for ($attempt = 0; $attempt < 10; $attempt++) {
        registrationComponent(validRegistration(['first_name' => '']))->call('submit');
    }

    $data = validRegistration();
    $component = registrationComponent($data)->call('submit');

    $component->assertHasErrors(['email']);
    $errors = $component->errors();
    expect($errors instanceof MessageBag ? $errors->first('email') : '')->toContain('tentativi');
    expect(User::query()->where('email', $data['email'])->exists())->toBeFalse();
});
