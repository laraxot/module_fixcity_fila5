<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Modules\Fixcity\Actions\ConfirmTicketResolutionAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

uses(TestCase::class);

// The standalone module's Testbench does not load laravel/routes/web.php from the host app.
beforeEach(function (): void {
    if (! Route::has('tickets.resolution')) {
        Route::post('/{lang}/tickets/{ticket}/resolution', ConfirmTicketResolutionAction::class)
            ->middleware(['web', 'auth'])
            ->name('tickets.resolution');
    }
});

/** @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md */
it('lets the owner confirm a resolved ticket exactly once', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'status' => TicketStatusEnum::RESOLVED,
    ]);
    $url = '/it/tickets/'.SafeStringCastAction::cast($ticket->getKey()).'/resolution';

    $this->actingAs($owner)->post($url, ['decision' => 'confirm'])
        ->assertRedirect('/it/tickets/'.SafeStringCastAction::cast($ticket->getKey()));

    $fresh = $ticket->fresh();
    if (! $fresh instanceof Ticket) {
        throw new RuntimeException('Ticket non trovato dopo la conferma.');
    }
    expect($fresh->resolveTicketStatusValue())->toBe(TicketStatusEnum::CLOSED->value);
    expect(TicketActivity::query()->where('ticket_id', $ticket->getKey())->count())->toBe(1);

    $this->post($url, ['decision' => 'confirm'])->assertSessionHasErrors('decision');
    expect(TicketActivity::query()->where('ticket_id', $ticket->getKey())->count())->toBe(1);
});

/** @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md */
it('lets the owner contest a resolved ticket and keeps the reason off the public timeline', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'status' => TicketStatusEnum::RESOLVED,
    ]);
    $url = '/it/tickets/'.SafeStringCastAction::cast($ticket->getKey()).'/resolution';

    $this->actingAs($owner)->post($url, ['decision' => 'dispute', 'reason' => 'Il problema persiste'])
        ->assertRedirect('/it/tickets/'.SafeStringCastAction::cast($ticket->getKey()));

    $fresh = $ticket->fresh();
    if (! $fresh instanceof Ticket) {
        throw new RuntimeException('Ticket non trovato dopo la contestazione.');
    }
    expect($fresh->resolveTicketStatusValue())->toBe(TicketStatusEnum::REOPENED->value);
    $events = TicketActivity::query()->where('ticket_id', $ticket->getKey())->get();
    expect($events)->toHaveCount(2);

    $public = $events->firstWhere('visibility', TicketActivityVisibilityEnum::Public->value);
    $private = $events->firstWhere('visibility', TicketActivityVisibilityEnum::AuthorOnly->value);
    expect($public?->reason)->toBeNull()
        ->and($private?->reason)->toBe('Il problema persiste');
});

/** @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md */
it('rejects a nonowner and requires a reason for contesting the resolution', function (): void {
    Notification::fake();
    $owner = UserFactory::new()->createOne();
    $stranger = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $owner->getAuthIdentifier(),
        'status' => TicketStatusEnum::RESOLVED,
    ]);
    $url = '/it/tickets/'.SafeStringCastAction::cast($ticket->getKey()).'/resolution';

    $this->actingAs($stranger)->post($url, ['decision' => 'confirm'])->assertForbidden();
    $this->actingAs($owner)->post($url, ['decision' => 'dispute'])->assertSessionHasErrors('reason');

    $fresh = $ticket->fresh();
    if (! $fresh instanceof Ticket) {
        throw new RuntimeException('Ticket non trovato dopo il rifiuto.');
    }
    expect($fresh->resolveTicketStatusValue())->toBe(TicketStatusEnum::RESOLVED->value);
    expect(TicketActivity::query()->where('ticket_id', $ticket->getKey())->count())->toBe(0);
});
