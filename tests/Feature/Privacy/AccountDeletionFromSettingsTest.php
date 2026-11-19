<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Modules\Fixcity\Tests\TestCase;
use Modules\Gdpr\Models\Consent;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Filament\Widgets\Auth\LoginWidget;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;

uses(TestCase::class);

/*
 * Privacy notice: "Quando chiedi di cancellarlo, l'account è disattivato subito". The personal area settings page offers the
 * request (POST /{lang}/account/delete, DeleteAccountAction) behind an explicit confirmation.
 * Story: Modules/Fixcity/docs/stories/privacy-notice-retention-exif-account-deletion.story.md
 */
beforeEach(function (): void {
    config(['activitylog.enabled' => false]);
});

function signedInCitizen(): UserContract
{
    $id = UserFactory::new()->createOne(['email_verified_at' => now()])->getKey();

    return XotData::make()->getUserClass()::query()->whereKey($id)->firstOrFail();
}

it('offers account deletion with a required confirmation on the settings page', function (string $locale, string $button): void {
    $this->actingAs(signedInCitizen());

    $html = (string) $this->get("/{$locale}/area-personale/impostazioni")->assertOk()->getContent();

    expect($html)->toContain('id="account-deletion"')
        ->toContain('action="'.url("/{$locale}/account/delete").'"')
        ->toContain('method="POST"')
        ->toContain('name="confirm"')
        ->toContain('value="cancella"')
        ->toContain('required')
        ->toContain($button)
        ->toContain('href="#account-deletion"');
})->with([
    'it' => ['it', 'Elimina il mio account'],
    'en' => ['en', 'Delete my account'],
    'de' => ['de', 'Mein Konto löschen'],
    'es' => ['es', 'Eliminar mi cuenta'],
]);

it('deletes the account on POST: deactivated, signed out, deletion recorded', function (): void {
    $citizen = signedInCitizen();
    $this->actingAs($citizen);

    $this->post('/it/account/delete', ['confirm' => 'cancella', 'reason' => 'non uso più il servizio'])
        ->assertRedirect('/it')
        ->assertSessionHas('status', 'gdpr::account.flash.account_deleted');

    $this->assertGuest();
    expect(XotData::make()->getUserClass()::query()->whereKey($citizen->getKey())->value('deleted_at'))->not->toBeNull()
        ->and(Consent::query()->where('user_id', $citizen->getKey())->where('type', 'account_deletion')->exists())->toBeTrue();
});

it('refuses the POST without the confirmation and keeps the account', function (): void {
    $citizen = signedInCitizen();
    $this->actingAs($citizen);

    $this->from('/it/area-personale/impostazioni')->post('/it/account/delete', [])
        ->assertRedirect('/it/area-personale/impostazioni')
        ->assertSessionHasErrors('confirm');

    expect(XotData::make()->getUserClass()::query()->whereKey($citizen->getKey())->value('deleted_at'))->toBeNull();
});

it('does not let a deleted account sign in again with its password', function (): void {
    app()->setLocale('it');
    $citizen = UserFactory::new()->createOne(['password' => Hash::make('password123'), 'deleted_at' => now()]);

    Livewire::test(LoginWidget::class)
        ->set('data.email', $citizen->email)
        ->set('data.password', 'password123')
        ->call('save')
        ->assertHasErrors('data.email');

    $this->assertGuest();
});

it('still lets an active account sign in', function (): void {
    app()->setLocale('it');
    $citizen = UserFactory::new()->createOne(['password' => Hash::make('password123')]);

    Livewire::test(LoginWidget::class)
        ->set('data.email', $citizen->email)
        ->set('data.password', 'password123')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertAuthenticated();
});
