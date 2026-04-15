<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;

test('create ticket wizard uses module blade view for design comuni sidebar layout', function (): void {
    $prop = new ReflectionProperty(CreateTicketWizardWidget::class, 'view');

    expect($prop->getDefaultValue())->toBe('fixcity::filament.widgets.ticket-create-wizard');
});

test('wizard mount shows title from blockData', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateTicketWizardWidget::class, [
        'blockData' => [
            'title' => 'Titolo blocco CMS',
            'issue_types' => ['public_damage' => 'Danno'],
        ],
    ])
        ->assertOk()
        ->assertSee('Titolo blocco CMS');
});

test('wizard submit maps issue type keys to TicketTypeEnum and redirects to conferma', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $before = Ticket::count();
    $confirmationSlug = (string) config('fixcity.wizard.confirmation_slug');

    Livewire::test(CreateTicketWizardWidget::class, [
        'blockData' => [
            'issue_types' => ['public_damage' => 'Danno'],
        ],
    ])
        ->set('privacyAccepted', true)
        ->set('currentStep', 2)
        ->set('address', 'Via Roma 1')
        ->set('issueType', 'public_damage')
        ->set('title', 'Titolo segnalazione')
        ->set('details', 'Dettagli completi')
        ->set('email', 'test@example.com')
        ->set('currentStep', 3)
        ->call('submit')
        ->assertRedirect(route('tests.view', ['slug' => $confirmationSlug]));

    expect(Ticket::count())->toBe($before + 1);

    $ticket = Ticket::query()->latest('id')->first();
    expect($ticket)->not->toBeNull()
        ->and($ticket->owner_id)->toBe($user->id)
        ->and($ticket->type)->toBe(TicketTypeEnum::ROAD_MAINTENANCE);
});
