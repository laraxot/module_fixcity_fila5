<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildPersonalAreaSettingsDataAction;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

/*
 * DCP-111: "I tuoi dati" mostra solo quello che il sistema ha davvero e solo dell'utente indicato. Sorgenti: `users.name|first_name|last_name|email`
 * e `profiles.phone` (stessa lettura di BuildTicketReviewSummaryAction). Nessun segnaposto, nessuna riga senza valore.
 */
it('shows name, email and phone the system holds for the citizen', function (): void {
    $citizen = UserFactory::new()->createOne(['name' => 'Giulia Verdi', 'email' => 'giulia.verdi@example.test']);
    $citizen->profile()->firstOrCreate([])->forceFill(['phone' => '+39 333 1234567'])->saveOrFail();

    $data = app(BuildPersonalAreaSettingsDataAction::class)->execute($citizen);

    expect($data['account_rows'])->toBe([
        ['label' => __('fixcity::personal_area_settings.settings.account.fields.name'), 'value' => 'Giulia Verdi'],
        ['label' => __('fixcity::personal_area_settings.settings.account.fields.email'), 'value' => 'giulia.verdi@example.test'],
        ['label' => __('fixcity::personal_area_settings.settings.account.fields.phone'), 'value' => '+39 333 1234567'],
    ])->and($data['email_verified'])->toBeTrue();
});

it('omits the rows without a value instead of inventing them', function (): void {
    $citizen = UserFactory::new()->createOne(['name' => '', 'email' => 'solo.email@example.test', 'email_verified_at' => null]);

    $data = app(BuildPersonalAreaSettingsDataAction::class)->execute($citizen);

    expect(array_column($data['account_rows'], 'value'))->toBe(['solo.email@example.test'])
        ->and($data['email_verified'])->toBeFalse();
});

it('never leaks the data of another user', function (): void {
    $citizen = UserFactory::new()->createOne(['name' => 'Cittadina Uno', 'email' => 'uno@example.test']);
    $stranger = UserFactory::new()->createOne(['name' => 'Estraneo Due', 'email' => 'due@example.test']);
    $stranger->profile()->firstOrCreate([])->forceFill(['phone' => '+39 000 0000000'])->saveOrFail();

    $values = array_column(app(BuildPersonalAreaSettingsDataAction::class)->execute($citizen)['account_rows'], 'value');

    expect($values)->toBe(['Cittadina Uno', 'uno@example.test']);
});
