<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Actions\GetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Livewire\TicketNotificationPreferencesWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('requires authentication to access notification preferences', function (): void {
    $this->get('/it/area-personale/impostazioni')->assertRedirect();
});

it('renders a localized and accessible preference for an authenticated citizen', function (): void {
    $user = UserFactory::new()->createOne();

    $this->actingAs($user)
        ->get('/it/area-personale/impostazioni')
        ->assertOk()
        ->assertSee(__('fixcity::ticket_notification_preferences.section_title'))
        ->assertSee('id="email-ticket-updates"', false)
        ->assertSee(__('fixcity::ticket_notification_preferences.save'));
});

it('links notification preferences from the citizen practice and notification pages', function (): void {
    $user = UserFactory::new()->createOne();

    $this->actingAs($user)
        ->get('/it/area-personale/pratiche')
        ->assertOk()
        ->assertSee('area-personale/impostazioni');

    $this->get('/it/area-personale/notifiche')
        ->assertOk()
        ->assertSee('area-personale/impostazioni');
});

it('enables email updates by default and stores changes without replacing other preferences', function (): void {
    $user = UserFactory::new()->createOne();
    expect(app(GetFixcityEmailNotificationPreferenceAction::class)->execute($user))->toBeTrue();

    $profile = $user->profile()->firstOrCreate([]);
    $profile->setAttribute('preferences', ['locale' => 'it', 'fixcity' => ['other' => 'keep']]);
    $profile->saveOrFail();

    Livewire::actingAs($user)
        ->test(TicketNotificationPreferencesWidget::class)
        ->assertSet('emailTicketUpdates', true)
        ->set('emailTicketUpdates', false)
        ->call('save')
        ->assertSee(__('fixcity::ticket_notification_preferences.saved'));

    $preferences = $user->profile()->firstOrFail()->getAttribute('preferences');
    expect($preferences)->toBe([
        'locale' => 'it',
        'fixcity' => ['other' => 'keep', 'email_ticket_updates' => false],
    ])->and(app(GetFixcityEmailNotificationPreferenceAction::class)->execute($user))->toBeFalse();
});
