<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Livewire\PersonalAreaNotificationsWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\Notification;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;

use function Safe\preg_match;

uses(TestCase::class);

/*
 * DCP-111: pagine reali `/{lang}/area-personale/notifiche` e `/{lang}/area-personale/impostazioni` (JSON CMS `middleware: ["auth"]`).
 * Le notifiche di partenza nascono dal flusso reale (TicketActivity pubblica -> notifica `database`), mai da righe scritte a mano.
 */
function notifyStatusChange(UserContract $owner, string $code, TicketStatusEnum $to): Ticket
{
    $ticket = TicketFactory::new()->createOne(['owner_id' => $owner->getAuthIdentifier(), 'code' => $code]);
    TicketActivity::query()->create([
        'ticket_id' => $ticket->getKey(),
        'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
        'payload' => ['v' => 1, 'from' => TicketStatusEnum::OPEN->value, 'to' => $to->value],
        'visibility' => TicketActivityVisibilityEnum::Public->value,
    ]);

    return $ticket;
}

/** Contenuto di <main>: le pagine non devono avere link finti (`href="#"`), solo ancore vere come `#footer`. */
function mainHtml(string $html): string
{
    preg_match('#<main.*?</main>#s', $html, $match);

    return $match[0] ?? '';
}

dataset('personal area pages', [
    'notifiche' => ['notifiche'],
    'impostazioni' => ['impostazioni'],
]);

dataset('personal area locales', [
    'it' => ['it', 'Notifiche', 'Impostazioni e privacy', 'I tuoi diritti sui dati'],
    'en' => ['en', 'Notifications', 'Settings and privacy', 'Your data rights'],
    'de' => ['de', 'Benachrichtigungen', 'Einstellungen und Datenschutz', 'Deine Rechte an deinen Daten'],
    'es' => ['es', 'Notificaciones', 'Ajustes y privacidad', 'Tus derechos sobre los datos'],
]);

describe('guest', function (): void {
    it('is redirected to the localized login', function (string $slug, string $locale): void {
        /** @var TestCase $this */
        $this->get("/{$locale}/area-personale/{$slug}")->assertRedirectContains("/{$locale}/auth/login");
    })->with('personal area pages')->with(['it', 'en', 'de', 'es']);
});

describe('notifications page', function (): void {
    it('shows the real status updates of the citizen in every language', function (string $locale, string $title): void {
        /** @var TestCase $this */
        $citizen = UserFactory::new()->createOne();
        $stranger = UserFactory::new()->createOne();
        notifyStatusChange($citizen, 'TCK-PAGE-MINE', TicketStatusEnum::RESOLVED);
        notifyStatusChange($stranger, 'TCK-PAGE-THEIRS', TicketStatusEnum::RESOLVED);

        $html = $this->actingAs($citizen)
            ->get("/{$locale}/area-personale/notifiche")
            ->assertOk()
            ->assertSee($title)
            ->assertSee('TCK-PAGE-MINE')
            ->assertSee("/{$locale}/tickets/track?code=TCK-PAGE-MINE", false)
            ->assertSee("/{$locale}/area-personale/impostazioni", false)
            ->assertDontSee('TCK-PAGE-THEIRS')
            ->getContent();

        expect(mainHtml((string) $html))->not->toContain('href="#"');
    })->with('personal area locales');

    it('shows the status in the language of the page, not the one stored at send time', function (): void {
        /** @var TestCase $this */
        $citizen = UserFactory::new()->createOne();
        notifyStatusChange($citizen, 'TCK-PAGE-DE', TicketStatusEnum::RESOLVED);

        foreach (['de' => 'Die Meldung TCK-PAGE-DE hat einen neuen Status: ', 'es' => 'La incidencia TCK-PAGE-DE tiene un nuevo estado: '] as $locale => $sentence) {
            $status = trans('fixcity::ticket_status_enum.values.resolved.label', [], $locale);
            expect($status)->not->toBe(trans('fixcity::ticket_status_enum.values.resolved.label', [], 'it'));

            $this->actingAs($citizen)->get("/{$locale}/area-personale/notifiche")->assertOk()->assertSee($sentence.$status.'.');
        }
    });

    it('shows a useful empty state with a way forward', function (): void {
        /** @var TestCase $this */
        $this->actingAs(UserFactory::new()->createOne())
            ->get('/it/area-personale/notifiche')
            ->assertOk()
            ->assertSee(__('fixcity::personal_area_settings.notifications.empty_title'))
            ->assertSee('/it/area-personale/pratiche', false)
            ->assertDontSee('Segna tutte come lette');
    });
});

describe('notifications widget', function (): void {
    it('marks one notification as read and announces it', function (): void {
        $citizen = UserFactory::new()->createOne();
        notifyStatusChange($citizen, 'TCK-WIDGET-1', TicketStatusEnum::RESOLVED);
        $id = SafeStringCastAction::cast(Notification::query()->where('notifiable_id', $citizen->getKey())->firstOrFail()->getKey());

        Livewire::actingAs($citizen)
            ->test(PersonalAreaNotificationsWidget::class)
            ->assertSee(__('fixcity::personal_area_settings.notifications.mark_read'))
            ->call('markAsRead', $id)
            ->assertSee(__('fixcity::personal_area_settings.notifications.marked_read'))
            ->assertDontSee('Segna come letta la notifica');

        expect(Notification::query()->whereKey($id)->firstOrFail()->getAttribute('read_at'))->not->toBeNull();
    });

    it('cannot mark the notification of another citizen as read', function (): void {
        $citizen = UserFactory::new()->createOne();
        $stranger = UserFactory::new()->createOne();
        notifyStatusChange($stranger, 'TCK-WIDGET-THEIRS', TicketStatusEnum::RESOLVED);
        $theirs = Notification::query()->where('notifiable_id', $stranger->getKey())->firstOrFail();

        Livewire::actingAs($citizen)
            ->test(PersonalAreaNotificationsWidget::class)
            ->call('markAsRead', SafeStringCastAction::cast($theirs->getKey()))
            ->assertSet('statusMessage', '');

        expect($theirs->fresh()?->getAttribute('read_at'))->toBeNull();
    });

    it('marks every notification as read at once', function (): void {
        $citizen = UserFactory::new()->createOne();
        notifyStatusChange($citizen, 'TCK-WIDGET-A', TicketStatusEnum::IN_PROGRESS);
        notifyStatusChange($citizen, 'TCK-WIDGET-B', TicketStatusEnum::RESOLVED);

        Livewire::actingAs($citizen)
            ->test(PersonalAreaNotificationsWidget::class)
            ->call('markAllAsRead')
            ->assertSet('statusMessage', trans_choice('fixcity::personal_area_settings.notifications.marked_all_read', 2, ['count' => 2]))
            ->assertDontSee(__('fixcity::personal_area_settings.notifications.mark_all_read'));

        expect(Notification::query()->where('notifiable_id', $citizen->getKey())->whereNull('read_at')->count())->toBe(0);
    });

    it('does not render for a guest', function (): void {
        Livewire::test(PersonalAreaNotificationsWidget::class)->assertForbidden();
    });
});

describe('settings page', function (): void {
    it('shows preferences, own account data and data rights in every language', function (string $locale, string $notificationsTitle, string $title, string $rightsTitle): void {
        /** @var TestCase $this */
        $citizen = UserFactory::new()->createOne(['name' => 'Giulia Verdi', 'email' => 'giulia.verdi@example.test']);
        UserFactory::new()->createOne(['name' => 'Estraneo Rossi', 'email' => 'estraneo.rossi@example.test']);

        $html = $this->actingAs($citizen)
            ->get("/{$locale}/area-personale/impostazioni")
            ->assertOk()
            ->assertSee($title)
            ->assertSee('id="email-ticket-updates"', false)
            ->assertSee(__('fixcity::ticket_notification_preferences.section_title', [], $locale))
            ->assertSee(__('fixcity::ticket_notification_preferences.save', [], $locale))
            ->assertSee('Giulia Verdi')
            ->assertSee('giulia.verdi@example.test')
            ->assertSee($rightsTitle)
            ->assertSee('Art. 15')
            ->assertSee('Art. 21')
            ->assertSee('https://www.garanteprivacy.it/', false)
            ->assertSee("/{$locale}/area-personale/notifiche", false)
            ->assertDontSee('Estraneo Rossi')
            ->assertDontSee('estraneo.rossi@example.test')
            ->getContent();

        expect(mainHtml((string) $html))->not->toContain('href="#"')->and($html)->toContain('href="#footer"');
    })->with('personal area locales');

    it('links the rights section to the contacts that really exist in the footer', function (): void {
        /** @var TestCase $this */
        $html = (string) $this->actingAs(UserFactory::new()->createOne())->get('/it/area-personale/impostazioni')->assertOk()->getContent();

        expect($html)->toContain('id="footer"');
    });

    it('says honestly whether the email address is verified', function (): void {
        /** @var TestCase $this */
        $unverified = UserFactory::new()->createOne(['email_verified_at' => null]);

        $this->actingAs($unverified)
            ->get('/it/area-personale/impostazioni')
            ->assertOk()
            ->assertSee(__('fixcity::personal_area_settings.settings.email_unverified'))
            ->assertDontSee(__('fixcity::personal_area_settings.settings.email_verified'));
    });
});
